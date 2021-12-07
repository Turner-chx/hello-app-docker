<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 18/04/19
 * Time: 15:15
 */

namespace App\Command;


use App\Entity\ProductType;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppUpdateProductTypesCommand extends Command
{
    private $em;
    private $baseUri;

    protected static $defaultName = 'app:update:product:types';

    public function __construct(EntityManagerInterface $em, string $baseUri)
    {
        $this->em = $em;
        $this->baseUri = $baseUri;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Met à jour les gammes entre le site LMECO et l\'intranet');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->em;
        $baseUri = $this->baseUri;
        $productTypeMany = '/gammes-many';
        $uri = $baseUri . $productTypeMany;
        $i = 0;

        $client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => 200.0,
        ]);

        $response = json_decode($client->get($uri)->getBody()->getContents(), true);

        $count = \count($response);

        $output->writeln('');
        $output->writeln('========== UPDATE PRODUCT TYPES ==========');
        $progress = new ProgressBar($output, $count);

        foreach ($response as $value){

            $productType = $manager->getRepository(ProductType::class)
                ->findOneBy([
                   'oldId' => $value['id']
                ]);

            if (null === $productType){
                $productType = new ProductType();
            }

            $productType->setType($value['name']);
            $productType->setSlug($value['slug']);
            $productType->setOldId($value['id']);

            $i++;
            $manager->persist($productType);
            $progress->setMessage($i, 'item');
            $progress->advance();
            $progress->displayMessage($i);
            $manager->flush();
        }
    }
}