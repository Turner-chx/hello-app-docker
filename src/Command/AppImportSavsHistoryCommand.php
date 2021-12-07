<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 02/04/19
 * Time: 14:42
 */

namespace App\Command;


use App\Entity\Sav;
use App\Entity\SavHistory;
use App\Entity\User;
use App\Enum\HistoryEventsEnum;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportSavsHistoryCommand extends Command
{

    protected static $defaultName = 'app:import:savhistory';
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
        $this->setDescription('Importe l\'historique des Savs depuis l\'intranet V1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->em;
        $oldSavHistories = [];
        $i = 0;
        $batchSize = 1000;

        try {
            $bdd = new \PDO('mysql:host=127.0.0.1:3306;dbname=' . $this->dbName . ';charset=utf8', $this->dbUser, $this->dbPwd);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        $query = $bdd->query('
            SELECT
                id AS id,
                idSav AS idSav,
                user AS user,
                date AS date
            FROM savhisto
        ');

        while ($data = $query->fetch()) {
            $oldSavHistories[] = [
                'id' => $data['id'],
                'savId' => $data['idSav'],
                'userName' => $data['user'],
                'date' => $data['date']
            ];
        }
        $query->closeCursor();

        $count = \count($oldSavHistories);
        $output->writeln('');
        $output->writeln('========== SAV HISTORY ==========');
        $progress = new ProgressBar($output, $count);

        foreach ($oldSavHistories as $oldSavHistory){
            /** @var Sav $sav */
            $sav = $manager->getRepository(Sav::class)
                ->findOneBy([
                    'oldId' => $oldSavHistory['savId']
                ]);

            if (null !== $sav && $sav->getSavHistories()->isEmpty()) {
                $savHistory = new SavHistory();

                $savHistory->setSav($sav);
                $savHistory->setEvent(HistoryEventsEnum::EDIT);

                /** @var \DateTime $dateHistory */
                $dateHistory = ($oldSavHistory['date'] !== '0000-00-00 00:00:00') ? \DateTime::createFromFormat('Y-m-d H:i:s', $oldSavHistory['date']) : \DateTime::createFromFormat('Y-m-d H:i:s', \date('Y-m-d H:i:s'));
                $savHistory->setHistoryDate($dateHistory);

                if (null !== $oldSavHistory['userName'] && $oldSavHistory['userName'] !== '') {
                    /** @var User $user */
                    $user = $manager->getRepository(User::class)
                        ->findOneBy([
                            'login' => $data['user']
                        ]);
                    if (null !== $user){
                        $savHistory->setUserName($user->getFirstName() . ' ' . $user->getLastName());
                    } else {
                        $savHistory->setUserName('Faleuam CAM');
                    }
                }

                $savHistory->setStatusSetting($sav->getStatusSetting());
                if (null !== $sav->getProduction()) {
                    $savHistory->setSavProduction($sav->getProduction());
                }

                $manager->persist($savHistory);

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