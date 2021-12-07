<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 01/12/20
 * Time: 09:40
 */

namespace App\Command\Lama;

use App\Entity\Brand;
use App\Handler\CsvHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppCreateBrandsCommand extends Command
{
    protected static $defaultName = 'lama:create:brands';
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
        $this->setDescription('Créé les ProductType depuis le fichier ArticlesIntranet.csv');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = 'ArticlesIntranet.csv';

        $handle = fopen($this->projectDir . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $filename, 'rb+');
        if (false === $handle) {
            return false;
        }

        while (false !== ($dataCsv = fgetcsv($handle, 1024, ';'))) {
            $dataCsv = array_map('trim', $dataCsv);
            $dataCsv = array_map('utf8_decode', $dataCsv);

            $data = $this->csvHandler->getArrayCsv($dataCsv);

            if (isset($data['A']) && isset($data['B']) && isset($data['C'])) {

                $columnD = $data['D'];

                if (strlen($columnD) === 8) {
                    $codeBrand = substr($columnD, 6, 2);

                    $brand = $this->em->getRepository(Brand::class)
                        ->findOneBy([
                            'codeLama' => $codeBrand
                        ]);

                    if (null !== $codeBrand && '' !== $codeBrand && null === $brand) {
                        $brand = new Brand();
                        $brand->setCodeLama($codeBrand);

                        $this->em->persist($brand);
                        $this->em->flush();
                    }
                }
            }
        }
        $this->em->flush();
        fclose($handle);

        return  1;
    }
}