<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 30/11/20
 * Time: 09:05
 */

namespace App\Command\Lama;

use App\Entity\Article;
use App\Entity\Brand;
use App\Entity\Color;
use App\Entity\Gamme;
use App\Entity\Oem;
use App\Entity\ProductType;
use App\Handler\CsvHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportArticlesCommand extends Command
{
    protected static $defaultName = 'lama:import:articles';
    protected $em;
    protected $csvHandler;
    protected $projectDir;

    public function __construct(EntityManagerInterface $em, CsvHandler $csvHandler, string $projectDir)
    {
        $this->em = $em;
        $this->csvHandler = $csvHandler;
        $this->projectDir = $projectDir;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Importe les Articles depuis le fichier ArticlesIntranet.csv');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);
        ini_set('memory_limit', '8192M');

        $filename = 'ArticlesIntranet.csv';
        $manager = $this->em;

        $handle = fopen($this->projectDir . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $filename, 'rb+');
        $i = 0;
        $j = 0;
        $batchSize = 250;
        if (false === $handle) {
            return false;
        }
        fgetcsv($handle, 1024, ';');
        while (false !== ($data = fgetcsv($handle, 1024, ';'))) {
            $gamme = null;
            $productType = null;
            $brand = null;
            $data = array_map('trim', $data);
            $data = array_map('utf8_decode', $data);
            $ref = $data[0] ?? null;
            $des = $data[1] ?? null;
            $desAbr = $data[2] ?? null;
            $ean = $data[5] ?? null;
            $brandLama = substr($data[4] ?? '', -2);
            $productTypeLama = substr($data[4] ?? '', 0, 3);
            $gammeLama = $data[3] ?? null;
            $brand = $manager->getRepository(Brand::class)
                ->findOneBy([
                    'codeLama' => $brandLama
                ]);
            if (null === $brand) {
                $brand = new Brand();
                $brand->setStatus(true);
                $brand->setBrand($brandLama);
                $brand->setCodeLama($brandLama);
                $manager->persist($brand);
                $manager->flush();
            }
            $productType = $manager->getRepository(ProductType::class)
                ->findOneBy([
                    'codeLama' => $productTypeLama
                ]);
            if (null === $productType) {
                $productType = new ProductType();
                $productType->setCodeLama($productTypeLama);
                $productType->setType($productTypeLama);
                $manager->persist($productType);
                $manager->flush();
            }
            if (null !== $gammeLama) {
                $gamme = $manager->getRepository(Gamme::class)
                    ->findOneBy([
                        'gamme' => $gammeLama
                    ]);
                if (null === $gamme) {
                    $gamme = new Gamme();
                    $gamme->setGamme($gammeLama);
                    $manager->persist($gamme);
                    $manager->flush();
                }
            }
            $article = $manager->getRepository(Article::class)
                ->findOneBy([
                    'reference' => $ref
                ]);
            if (null !== $ref && null !== $des && null !== $desAbr) {
                if (null === $article) {
                    $article = new Article();
                }
                $article->setReference($ref);
                $article->setDesignation($des);
                $article->setDesignationAbridged($desAbr);
                $article->setEan($ean);
                $article->setStatus(true);
                if (null !== $gamme) {
                    $article->setGamme($gamme);
                }
                if (null !== $productType) {
                    $article->setProductType($productType);
                }
                if (null !== $brand) {
                    $article->setBrand($brand);
                }

                $manager->persist($article);
            }
            $i++;
            if ($i % $batchSize === 0) {
                $manager->flush();
            }
        }
        $manager->flush();
        fclose($handle);

        return  1;
    }
}