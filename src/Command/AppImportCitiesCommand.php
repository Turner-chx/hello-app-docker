<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 14/09/21
 * Time: 09:41
 */

namespace App\Command;


use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportCitiesCommand extends Command
{
    protected static $defaultName = 'app:import:cities';

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
        $this->setDescription('Importe les villes et les codes postales');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = 'cities.csv';
        $i = 1;
        $j = 0;
        $batchSize = 250;

        $cities = [];

        $manager = $this->em;

        $handle = fopen($this->projectDir . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $filename, 'rb');

        if (false === $handle) {
            return false;
        }

        while (false !== ($data = fgetcsv($handle, 1024, ','))) {

            if ($i > 1) {
                $postCode = $data[2];
                $name = $data[10];

                $count = strlen($postCode);

                if ($count < 5) {
                    $postCode = '0' . $postCode;
                }

                if (!array_key_exists($name, $cities)) {
                    $cities[$name] = [];
                }

                if (!in_array($postCode, $cities[$name])) {
                    $cities[$name][] = $postCode;
                }

            }
            $i++;
        }
        fclose($handle);

        foreach ($cities as $name => $cityArray) {
            foreach ($cityArray as $postCode) {
                $city = new City();
                $city->setPostCode($postCode);
                $city->setName($name);

                $manager->persist($city);

                if ($j % $batchSize === 0) {
                    $manager->flush();
                }
                $j++;
            }

        }
        $manager->flush();
    }
}