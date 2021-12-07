<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 04/04/19
 * Time: 11:54
 */

namespace App\Command;


use App\Entity\Arrival;
use App\Entity\Supplier;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportArrivalsCommand extends Command
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
        $this->setDescription('Importe les arrivages depuis Divalto');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = 'arrivals.csv';
        $i = 0;
        $arrivalsInfos = [];

        $manager = $this->em;

        $handle = fopen($this->projectDir . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $filename, 'rb');

        if (false === $handle) {
            return false;
        }

        while (false !== ($data = fgetcsv($handle, 1024, ';'))) {
            if (isset ($data[1])) {
                $data = array_map('utf8_encode', $data);
                $data = array_map('trim', $data);

                if (strpos($data[1], 'F') === 0) {

                    $quantity = explode('.', $data[4])[0];

                    $arrivalsInfos[] = [
                        'serialNumber' => $data[2],
                        'piref' => $data[3],
                        'orderDate' => $data[5],
                        'factoryReleaseDate' => $data[6],
                        'invoiceDate' => $data[7],
                        'quantity' => $quantity,
                        'supplierCode' => $data[1],
                        'refArticle' => $data[0]
                    ];
                }
            }
        }
        fclose($handle);

        $count = \count($arrivalsInfos);
        $output->writeln('');
        $output->writeln('========== ARRIVALS ==========');
        $progress = new ProgressBar($output, $count);

        foreach ($arrivalsInfos as $arrivalsInfo) {

            $arrival = new Arrival();

            if ($arrivalsInfo['serialNumber'] !== '') {
                $arrival->setSerialNumber($arrivalsInfo['serialNumber']);
            }
            if ($arrivalsInfo['piref'] !== '') {
                $arrival->setPiref($arrivalsInfo['piref']);
            }
            if ($arrivalsInfo['orderDate'] !== 'NULL' && $arrivalsInfo['orderDate'] !== '') {
                $arrival->setOrderDate(\DateTime::createFromFormat('Y-m-d', $arrivalsInfo['orderDate']));
            }
            if ($arrivalsInfo['factoryReleaseDate'] !== 'NULL' && $arrivalsInfo['factoryReleaseDate'] !== '') {
                $arrival->setFactoryReleaseDate(\DateTime::createFromFormat('Y-m-d', $arrivalsInfo['factoryReleaseDate']));
            }
            if ($arrivalsInfo['invoiceDate'] !== 'NULL' && $arrivalsInfo['invoiceDate'] !== '') {
                $arrival->setInvoiceDate(\DateTime::createFromFormat('Y-m-d', $arrivalsInfo['invoiceDate']));
            }
            if ($arrivalsInfo['quantity'] !== '') {
                $arrival->setQuantity($arrivalsInfo['quantity']);
            }

            /** @var Supplier $supplier */
            $supplier = $manager->getRepository(Supplier::class)
                ->findOneBy([
                    'supplierCode' => $arrivalsInfo['supplierCode']
                ]);

            if (null !== $supplier){
                $arrival->setSupplier($supplier);
            }

            $manager->persist($arrival);
            $i++;
            $progress->setMessage($i, 'item');
            $progress->advance();
            $progress->displayMessage($i);

        }
        $manager->flush();
        $progress->setMessage($i, 'item');
        $progress->displayMessage($i);
        $progress->finish();
    }
}