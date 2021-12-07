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

        $handle = fopen($this->projectDir . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $filename, 'rb+');
        $i = 0;
        $j = 0;
        $batchSize = 250;
        if (false === $handle) {
            return false;
        }

        while (false !== ($dataCsv = fgetcsv($handle, 1024, ';'))) {
            $dataCsv = array_map('trim', $dataCsv);
            $dataCsv = array_map('utf8_decode', $dataCsv);

            $data = $this->csvHandler->getArrayCsv($dataCsv);

            if (isset($data['A']) && isset($data['B']) && isset($data['C'])) {
                $codeOem = isset($data['N']) ? $data['N'] : null;

                if (null !== $codeOem && '' !== $codeOem) {
                    $oem = $this->em->getRepository(Oem::class)->findOneBy([
                        'oem' => $codeOem
                    ]);

                    if (null === $oem) {
                        $oem = new Oem();
                        $oem->setOem($codeOem);

                        $this->em->persist($oem);

                        if ($j % $batchSize === 0) {
                            $this->em->flush();
                        }
                        $j++;
                    }
                }
            }
        }
        $this->em->flush();
        fclose($handle);

        $handle = fopen($this->projectDir . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $filename, 'rb+');
        $i = 0;
        $batchSize = 250;
        if (false === $handle) {
            return false;
        }

        while (false !== ($dataCsv = fgetcsv($handle, 1024, ';'))) {
            $dataCsv = array_map('trim', $dataCsv);
            $dataCsv = array_map('utf8_decode', $dataCsv);

            $data = $this->csvHandler->getArrayCsv($dataCsv);

            if (isset($data['A']) && isset($data['B']) && isset($data['C'])) {
                $ref = isset($data['A']) ? $data['A'] : null;
                $des = isset($data['B']) ? $data['B'] : null;
                $desAbr = isset($data['C']) ? $data['C'] : null;
                $codeColor = isset($data['Q']) ? $data['Q'] : null;
                $codeOem = isset($data['N']) ? $data['N'] : null;
                $codeGamme = isset($data['E']) ? $data['E'] : null;

                $statusNumber = isset($data['U']) ? (string)$data['U'] : '0';
                switch ($statusNumber) {
                    case '2':
                        $status = true;
                        break;
                    case '1':
                    case '0':
                    default:
                        $status = false;
                        break;
                }

                $columnD = $data['D'];
                $codeLama = isset($columnD) ? substr($columnD, 0, 3) : null;
                $codeBrand = null;

                if (strlen($columnD) === 8) {
                    $codeBrand = substr($columnD, 6, 2);
                }

                $ean = isset($data['G']) ? $data['G'] : null;
                $article = $this->em->getRepository(Article::class)
                    ->findOneBy([
                        'reference' => $ref
                    ]);
                if (null === $article && null !== $ref && null !== $des && null !== $desAbr) {
                    $article = new Article();
                    $article->setReference($ref);
                    $article->setDesignation($des);
                    $article->setDesignationAbridged($desAbr);
                    $article->setEan($ean);
                    $article->setStatus($status);

                    if (null !== $codeLama && '' !== $codeLama) {
                        /** @var ProductType $productType */
                        $productType = $this->em->getRepository(ProductType::class)->findOneBy([
                            'codeLama' => $codeLama
                        ]);

                        if (null !== $productType) {
                            $article->setProductType($productType);
                        }
                    }

                    if (null !== $codeBrand) {
                        /** @var Brand $brand */
                        $brand = $this->em->getRepository(Brand::class)->findOneBy([
                            'codeLama' => $codeBrand
                        ]);

                        if (null !== $brand) {
                            $article->setBrand($brand);
                        }
                    }

                    if (null !== $codeColor) {
                        /** @var Color $color */
                        $color = $this->em->getRepository(Color::class)->findOneBy([
                            'idLama' => $codeColor
                        ]);

                        if (null !== $color) {
                            $article->setColor($color);
                        }
                    }

                    if (null !== $codeOem && '' !== $codeOem) {
                        /** @var Oem $oem */
                        $oem = $this->em->getRepository(Oem::class)->findOneBy([
                            'oem' => $codeOem
                        ]);

                        if (null !== $oem) {
                            $article->setOem($oem);
                        }
                    }

                    if (null !== $codeGamme && '' !== $codeGamme) {
                        /** @var Gamme $gamme */
                        $gamme = $this->em->getRepository(Gamme::class)->findOneBy([
                            'gamme' => $codeGamme
                        ]);

                        if (null !== $gamme) {
                            $article->setGamme($gamme);
                        }
                    }

                    $this->em->persist($article);
                }
                if ($i % $batchSize === 0) {
                    $this->em->flush();
                }
                $i++;
            }
        }
        $this->em->flush();
        fclose($handle);

        return  1;
    }
}