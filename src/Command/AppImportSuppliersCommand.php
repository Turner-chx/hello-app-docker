<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 04/04/19
 * Time: 09:41
 */

namespace App\Command;


use App\Entity\Supplier;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Intl\Intl;

class AppImportSuppliersCommand extends Command
{

    protected static $defaultName = 'app:import:suppliers';

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
        $this->setDescription('Importe les suppliers depuis Divalto');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = 'suppliers.csv';
        $i = 0;
        $suppliersInfos = [];

        $manager = $this->em;

        $countryNames = Intl::getRegionBundle()->getCountryNames();

        $handle = fopen($this->projectDir . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $filename, 'rb');

        if (false === $handle) {
            return false;
        }

        while (false !== ($data = fgetcsv($handle, 1024, ';'))) {
            if (isset ($data[1])){
                $data = array_map('utf8_encode', $data);
                $data = array_map('trim', $data);

                $additionalAddress = $data[6];
                $additionalAddress .= ($data[6] !== '' && $data[3] !== '') ? (', ' . $data[3]) : $data[3];
                $additionalAddress .= ($data[3] !== '' && $data[4] !== '') ? (', ' . $data[4]) : $data[4];

                $suppliersInfos[] = [
                    'supplierCode' => $data[0],
                    'name' => $data[2],
                    'email' => $data[13],
                    'address' => $data[5],
                    'additionalAddress' => $additionalAddress,
                    'postalCode' => $data[9],
                    'city' => $data[7],
                    'country' => $data[8],
                    'phoneNumber' => $data[10],
                    'faxNumber' => $data[11],
                    'website' => $data[12],
                    'siret' => $data[15]
                ];
            }
        }
        fclose($handle);

        $count = \count($suppliersInfos);
        $output->writeln('');
        $output->writeln('========== SUPPLIERS ==========');
        $progress = new ProgressBar($output, $count);

        foreach ($suppliersInfos as $suppliersInfo){
            /** @var Supplier $supplier */
            $supplier = $manager->getRepository(Supplier::class)
                ->findOneBy([
                    'supplierCode' => $suppliersInfo['supplierCode']
                ]);

            if(null === $supplier) {
                $supplier = new Supplier();

                if ($suppliersInfo['supplierCode'] !== '') {
                    $supplier->setSupplierCode($suppliersInfo['supplierCode']);
                }
                if ($suppliersInfo['name'] !== '') {
                    $supplier->setName($suppliersInfo['name']);
                }
                if ($suppliersInfo['email'] !== '') {
                    $supplier->setEmail($suppliersInfo['email']);
                }
                if ($suppliersInfo['address'] !== '') {
                    $supplier->setAddress($suppliersInfo['address']);
                }
                if ($suppliersInfo['additionalAddress'] !== '') {
                    $supplier->setAdditionalAddress($suppliersInfo['additionalAddress']);
                }
                if ($suppliersInfo['postalCode'] !== '') {
                    $supplier->setPostalCode($suppliersInfo['postalCode']);
                }
                if ($suppliersInfo['city'] !== '') {
                    $supplier->setCity($suppliersInfo['city']);
                }
                if ($suppliersInfo['country'] !== '' && array_key_exists($suppliersInfo['country'], $countryNames)) {
                    $supplier->setCountry($suppliersInfo['country']);
                }
                if ($suppliersInfo['phoneNumber'] !== '') {
                    $supplier->setPhoneNumber($suppliersInfo['phoneNumber']);
                }
                if ($suppliersInfo['faxNumber'] !== '') {
                    $supplier->setFaxNumber($suppliersInfo['faxNumber']);
                }
                if ($suppliersInfo['website'] !== '') {
                    $supplier->setWebsite($suppliersInfo['website']);
                }
                if ($suppliersInfo['siret'] !== '') {
                    $supplier->setSiret($suppliersInfo['siret']);
                }

                $manager->persist($supplier);
                $i++;
                $progress->setMessage($i, 'item');
                $progress->advance();
                $progress->displayMessage($i);
            }
        }
        $manager->flush();
        $progress->setMessage($i, 'item');
        $progress->displayMessage($i);
        $progress->finish();
    }
}