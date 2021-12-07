<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 28/03/19
 * Time: 14:08
 */

namespace App\Command;


use App\Entity\Production;
use App\Entity\ProductionHistory;
use App\Entity\User;
use App\Enum\HistoryEventsEnum;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportProductionsHistoryCommand extends Command
{
    protected static $defaultName = 'app:import:productionhistory';
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
        $this->setDescription('Importe l\'historique des Productions depuis l\'intranet V1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->em;
        $i = 0;
        $batchSize = 1000;

        try {
            $bdd = new \PDO('mysql:host=127.0.0.1:3306;dbname='.$this->dbName.';charset=utf8', $this->dbUser, $this->dbPwd);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        $query = $bdd->query('
            SELECT 
              id AS id, 
              date AS date, 
              user AS user, 
              idProduction AS idProduction
            FROM productionhisto
        ');

        $queryCount = $bdd->query('
            SELECT 
              COUNT(*) AS count
            FROM productionhisto
        ');

        while ($data = $queryCount->fetch()) {
            $count = $data['count'];
        }
        $queryCount->closeCursor();


        $output->writeln('');
        $output->writeln('========== PRODUCTION HISTORY ==========');
        $progress = new ProgressBar($output, $count);

        while ($data = $query->fetch()) {

            /** @var Production $production */
            $production = $manager->getRepository(Production::class)
                ->findOneBy([
                    'oldId' => $data['idProduction']
            ]);

            if (null !== $production && $production->getProductionHistories()->isEmpty()) {
                $productionHistory = new ProductionHistory();

                if (null !== $production->getComment()) {
                    $productionHistory->setComment($production->getComment());
                }

                $productionHistory->setEvent(HistoryEventsEnum::EDIT);

                /** @var \DateTime $dateHistory */
                $dateHistory = ($data['date'] !== '0000-00-00 00:00:00') ? \DateTime::createFromFormat('Y-m-d H:i:s', $data['date']) : \DateTime::createFromFormat('Y-m-d H:i:s', \date('Y-m-d H:i:s'));
                $productionHistory->setHistoryDate($dateHistory);

                if (null !== $data['user'] && $data['user'] !== '') {
                    /** @var User $user */
                    $user = $manager->getRepository(User::class)
                        ->findOneBy([
                            'login' => $data['user']
                        ]);
                    if (null !== $user){
                        $productionHistory->setUserName($user->getFirstName() . ' ' . $user->getLastName());
                    } else {
                        $productionHistory->setUserName('Faleuam CAM');
                    }
                }
                $productionHistory->setProduction($production);

                $i++;
                $manager->persist($productionHistory);
                $progress->setMessage($i, 'item');
                $progress->advance();
                $progress->displayMessage($i);

                if ($i % $batchSize === 0) {
                    $manager->flush();
                }

            } else {
                $progress->setMaxSteps($count--);
            }
        }
        $query->closeCursor();
        $manager->flush();
        $progress->setMessage($i, 'item');
        $progress->displayMessage($i);
        $progress->finish();
    }
}