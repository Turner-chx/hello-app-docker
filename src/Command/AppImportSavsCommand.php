<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 29/03/19
 * Time: 09:38
 */

namespace App\Command;


use App\Entity\Sav;
use App\Entity\StatusSetting;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportSavsCommand extends Command
{
    protected static $defaultName = 'app:import:savs';
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
        $this->setDescription('Importe les SAV depuis l\'intranet V1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->em;
        $oldSavs = [];
        $batchSize = 100;
        $i = 0;

        $statusValues = [
            '0' => 'À rappeler',
            '1' => 'Terminé avec intervention',
            '2' => 'Terminé avec remplacement',
            '3' => 'Terminé sans remplacement',
            '4' => 'À traiter',
            '5' => 'Produit à enlever',
            '6' => 'Message laissé au client',
            '7' => 'Produit à remplacer',
            '8' => 'En attente de livraison',
            '9' => 'À réparer',
            '10' => 'À renvoyer au client',
            '11' => 'Terminé avec réparation',
        ];

        try {
            $bdd = new \PDO('mysql:host=127.0.0.1:3306;dbname=' . $this->dbName . ';charset=utf8', $this->dbUser, $this->dbPwd);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        // RAJOUT ADRESSE ET NUMERO DIVALTO

        $query = $bdd->query('
            SELECT
                s.id AS id,
                s.disponibilite AS disponibilite,
                s.description AS description,
                s.numserie AS numserie,
                s.date AS date,
                s.source AS source,
                s.idOperateur AS idOperateur,
                s.nomclient AS nomclient,
                s.emailclient AS emailclient,
                s.type AS type, 
                s.etat AS etat,
                o.prenom AS prenomoperateur,
                o.mail AS mailoperateur,
                s.idRevendeur AS idrevendeur,
                r.codeclient AS codeclient,
                sp.idProduction AS idProduction,
                s.magasin AS magasin,
                s.numerocommande AS numeroDivalto
            FROM sav s
            LEFT JOIN operateur o
                ON s.idOperateur = o.id
            LEFT JOIN revendeur r
                ON s.idRevendeur = r.id
            LEFT JOIN savproduction sp 
                ON s.id = sp.idSav
        ');

        while ($data = $query->fetch()) {
            $data = array_map('trim', $data);

            $oldSavs[] = [
                'id' => $data['id'],
                'availability' => $data['disponibilite'],
                'description' => $data['description'],
                'serialNumberCustomer' => $data['numserie'],
                'date' => $data['date'],
                'source' => $data['source'],
                'idUser' => $data['idOperateur'],
                'customerName' => $data['nomclient'],
                'customerEmail' => $data['emailclient'],
                'type' => $data['type'],
                'status' => $data['etat'],
                'userFirstName' => $data['prenomoperateur'],
                'userEmail' => $data['mailoperateur'],
                'idDealer' => $data['idrevendeur'],
                'dealerCode' => $data['codeclient'],
                'production' => $data['idProduction'],
                'magasin' => $data['magasin'],
                'divaltoNumber' => $data['numeroDivalto']
            ];
        }
        $query->closeCursor();

        $count = \count($oldSavs);
        $output->writeln('');
        $output->writeln('========== SAVS ==========');
        $progress = new ProgressBar($output, $count);

        foreach ($oldSavs as $oldSav) {
            /** @var Sav $sav */
            $sav = $manager->getRepository(Sav::class)
                ->findOneBy([
                    'oldId' => $oldSav['id']
                ]);

            if (null !== $sav) {
                if (null === $sav->getDivaltoNumber()) {
                    $sav->setDivaltoNumber($oldSav['divaltoNumber']);
                }

                $manager->persist($sav);

                if ($i % $batchSize === 0) {
                    $manager->flush();
                }

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