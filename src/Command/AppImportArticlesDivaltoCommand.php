<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 27/03/19
 * Time: 09:05
 */

namespace App\Command;


use App\Entity\Article;
use App\Entity\Brand;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportArticlesDivaltoCommand extends Command
{
    protected static $defaultName = 'app:import:articles-divalto';
    protected $em;
    protected $projectDir;

    public function __construct(EntityManagerInterface $em, string $projectDir)
    {
        $this->em = $em;
        $this->projectDir = $projectDir;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Importe les Articles depuis l\'intranet V1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = 'ArticleIntranet.txt';

        $manager = $this->em;
        $handle = fopen($this->projectDir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'divalto' . DIRECTORY_SEPARATOR . $filename, 'rb+');
        $i = 0;
        $batchSize = 250;
        if (false === $handle) {
            return false;
        }
        fgetcsv($handle, 1024, ';');

        while (false !== ($data = fgetcsv($handle, 1024, ';'))) {
            $data = array_map('trim', $data);
            $data = array_map('utf8_decode', $data);
            $ref = isset($data[0]) ? $data[0] : null;
            $des = isset($data[1]) ? $data[1] : null;
            $desAbr = isset($data[2]) ? $data[2] : null;
            $family1 = isset($data[3]) ? $data[3] : null;
            $family2 = isset($data[4]) ? $data[4] : null;
            $family3 = isset($data[7]) ? $data[7] : null;
            $ean = isset($data[6]) ? $data[6] : null;
            $article = $manager->getRepository(Article::class)
                ->findOneBy([
                    'reference' => $ref
                ]);
            if (null === $article && null !== $ref && null !== $des && null !== $desAbr) {
                $article = new Article();
                $article->setReference($ref);
                $article->setDesignation($des);
                $article->setDesignationAbridged($desAbr);
                $article->setFamily1($family1);
                $article->setFamily2($family2);
                $article->setFamily3($family3);
                $article->setEan($ean);
                $article->setGuarantee(12);

                $manager->persist($article);
            }
            if ($i % $batchSize === 0) {
                $manager->flush();
            }
            $i++;
        }
        $manager->flush();
        fclose($handle);
    }
}