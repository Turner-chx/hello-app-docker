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

class AppImportDealersCommand extends Command
{
    protected static $defaultName = 'app:import:dealers';

    protected $em;
    protected $dbName;
    protected $dbUser;
    protected $dbPwd;

    public function __construct(EntityManagerInterface $em, string $dbName, string $dbUser, string $dbPwd)
    {
        $this->em = $em;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPwd = $dbPwd;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Importe les clients revendeur depuis Divalto');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $i = 0;
        $dealersInfos = [];

        $manager = $this->em;

        try {
            $bdd = new \PDO('mysql:host=127.0.0.1:3306;dbname='.$this->dbName.';charset=utf8', $this->dbUser, $this->dbPwd);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        $query = $bdd->query('
            SELECT
                id AS id, 
                nom AS nom,
                codeclient AS codeclient,
                complement1 AS complement1,
                complement2 AS complement2,
                rue AS rue,
                localite AS localite,
                cp AS cp,
                ville AS ville,
                pays AS pays,
                commercial AS commercial,
                email AS email,
                statut AS statut
            FROM revendeur
        ');

        while($data = $query->fetch()){
            $data = array_map('trim', $data);

            if (null !== $data['codeclient'] && $data['codeclient'] !== '') {

                $additionalAddress = $data['localite'];
                $additionalAddress .= ((null !== $data['localite'] && $data['localite'] !== '') && (null !== $data['complement1'] && $data['complement1'] !== '')) ? (', ' . $data['complement1']) : $data['complement1'];
                $additionalAddress .= ((null !== $data['complement1'] && $data['complement1'] !== '') && (null !== $data['complement2'] && $data['complement2'] !== '')) ? (', ' . $data['complement2']) : $data['complement2'];
                $country = (null !== $data['pays'] || $data['pays'] !== '') ? $data['pays'] : 'FR';

                $dealersInfos[] = [
                    'id' => $data['id'],
                    'name' => $data['nom'],
                    'email' => $data['email'],
                    'address' => $data['rue'],
                    'additionalAddress' => $additionalAddress,
                    'postalCode' => $data['cp'],
                    'city' => $data['ville'],
                    'country' => $country,
                    'salesman' => $data['commercial'],
                    'dealerCode' => $data['codeclient'],
                    'status' => $data['statut']
                ];
            }
        }
        $query->closeCursor();

        $count = \count($dealersInfos);
        $output->writeln('');
        $output->writeln('========== ARRIVALS ==========');
        $progress = new ProgressBar($output, $count);

        foreach ($dealersInfos as $dealersInfo) {

            /** @var Dealer $dealer */
            $dealer = $manager->getRepository(Dealer::class)
                ->findOneBy([
                    'dealerCode' => $dealersInfo['dealerCode']
                ]);

            if (null === $dealer) {
                $dealer = new Dealer();

                if (null !== $dealersInfo['id'] && $dealersInfo['id'] !== '') {
                    $dealer->setOldId($dealersInfo['id']);
                }
                if (null !== $dealersInfo['name'] && $dealersInfo['name'] !== '') {
                    $dealer->setName($dealersInfo['name']);
                }
                if (null !== $dealersInfo['email'] && $dealersInfo['email'] !== '') {
                    $dealer->setEmail($dealersInfo['email']);
                }
                if (null !== $dealersInfo['address'] && $dealersInfo['address'] !== '') {
                    $dealer->setAddress($dealersInfo['address']);
                }
                if (null !== $dealersInfo['additionalAddress'] && $dealersInfo['additionalAddress'] !== '') {
                    $dealer->setAdditionalAddress($dealersInfo['additionalAddress']);
                }
                if (null !== $dealersInfo['postalCode'] && $dealersInfo['postalCode'] !== '') {
                    $dealer->setPostalCode($dealersInfo['postalCode']);
                }
                if (null !== $dealersInfo['city'] && $dealersInfo['city'] !== '') {
                    $dealer->setCity($dealersInfo['city']);
                }
                if (null !== $dealersInfo['country'] && $dealersInfo['country'] !== '') {
                    $dealer->setCountry($dealersInfo['country']);
                }
                if (null !== $dealersInfo['salesman'] && $dealersInfo['salesman'] !== '') {
                    $dealer->setSalesmanName($dealersInfo['salesman']);
                }
                if (null !== $dealersInfo['dealerCode'] && $dealersInfo['dealerCode'] !== '') {
                    $dealer->setDealerCode($dealersInfo['dealerCode']);
                }
                if (null !== $dealersInfo['status'] && $dealersInfo['status'] !== '') {
                    $dealer->setStatus($dealersInfo['status']);
                }

                $manager->persist($dealer);
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