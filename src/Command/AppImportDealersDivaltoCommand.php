<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 05/04/19
 * Time: 10:02
 */

namespace App\Command;


use App\Entity\Dealer;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportDealersDivaltoCommand extends Command
{
    protected static $defaultName = 'app:import:dealers-divalto';
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
        $this->setDescription('Importe les clients revendeur depuis Divalto');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = 'ClientIntranet.txt';

        $manager = $this->em;
        $handle = fopen($this->projectDir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'divalto' . DIRECTORY_SEPARATOR . $filename, 'rb+');
        $i = 0;
        $batchSize = 250;
        if (false === $handle) {
            return false;
        }

        while (false !== ($data = fgetcsv($handle, 1024, ';'))) {
            $data = array_map('trim', $data);
            $data = array_map('utf8_decode', $data);
            $ref = isset($data[0]) ? $data[0] : null;
            $name = isset($data[1]) ? $data[1] : null;
            $additionnalAddress = isset($data[2]) ? $data[2] : null;
            $additionnalAddress2 = isset($data[3]) ? $data[3] : null;
            $street = isset($data[4]) ? $data[4] : null;
            $postCode = isset($data[6]) ? $data[6] : null;
            $city = isset($data[7]) ? $data[7] : null;
            $country = isset($data[8]) ? $data[8] : null;
            $commercial1 = isset($data[9]) ? $data[9] : null;
            $email = isset($data[11]) ? $data[11] : null;
            $dealer = $manager->getRepository(Dealer::class)
                ->findOneBy([
                    'dealerCode' => $ref
                ]);
            if (null === $dealer && null !== $ref && null !== $name && null !== $street) {
                $dealer = new Dealer();
                $dealer->setName($name);
                $dealer->setStatus(true);
                $dealer->setEmail($email);
                $dealer->setAddress($street);
                $dealer->setAdditionalAddress($additionnalAddress . ' ' . $additionnalAddress2);
                $dealer->setPostalCode($postCode);
                $dealer->setCity($city);
                $dealer->setCountry($country);
                $dealer->setDealerCode($ref);
                $dealer->setSalesmanName($commercial1);
                $manager->persist($dealer);
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