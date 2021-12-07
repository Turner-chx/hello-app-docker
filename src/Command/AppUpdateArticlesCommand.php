<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 18/04/19
 * Time: 15:15
 */

namespace App\Command;


use App\Entity\Article;
use App\Entity\Brand;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppUpdateArticlesCommand extends Command
{
    private $em;
    private $baseUri;

    protected static $defaultName = 'app:update:articles';

    public function __construct(EntityManagerInterface $em, string $baseUri)
    {
        $this->em = $em;
        $this->baseUri = $baseUri;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Met à jour les articles entre le site LMECO et l\'intranet');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->em;
        $baseUri = $this->baseUri;
        $articlesMany = '/articles-many';
        $uri = $baseUri . $articlesMany;
        $i = 0;

        $client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => 200.0,
        ]);

        $articles = $manager->getRepository(Article::class)->getArticlesArray();
        $chunkArticles = array_chunk($articles, 5);
        $count = \count($articles);

        $output->writeln('');
        $output->writeln('========== UPDATE ARTICLES ==========');
        $progress = new ProgressBar($output, $count);

        foreach ($chunkArticles as $articlesToSend){

            $response = json_decode($client->post($uri, ['form_params' => $articlesToSend])->getBody()->getContents(), true);

            $countRequest = \count($articlesToSend);
            $countResponse = \count($response);

            if ($countRequest !== $countResponse){
                $count -= ($countRequest - $countResponse);
            }

            foreach ($response as $value){
                $article = $manager->getRepository(Article::class)
                    ->findOneBy([
                        'reference' => isset($value['reference']) ? $value['reference'] : null
                    ]);
                if (null !== $article) {
                    if (array_key_exists('stock', $value) && array_key_exists('reappro_qte', $value)){
                        $stock = $value['reappro_qte'] + (($value['stock'] <= 0) ? 0 : $value['stock']);
                        $article->setStock($stock);
                    }
                    if (array_key_exists('status', $value)){
                        $article->setStatus($value['status']);
                    }
                    if (array_key_exists('model', $value)){
                        $article->setModel($value['model']);
                    }
                    if (array_key_exists('subProductId', $value)) {
                        $subProductType = $manager->getRepository(Brand::class)
                            ->findOneBy([
                                'oldId' => $value['subProductId']
                            ]);
                        $article->setSubProductType($subProductType);
                    }

                    if (array_key_exists('dureegarantie', $value)) {
                        $article->setGuarantee($value['dureegarantie']);
                    }

                    $manager->persist($article);
                    $i++;
                    $progress->setMessage($i, 'item');
                    $progress->advance();
                    $progress->displayMessage($i);
                }
            }
            $progress->setMaxSteps($count);
            $manager->flush();
        }
        $manager->flush();
    }
}