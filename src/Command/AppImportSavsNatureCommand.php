<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 02/04/19
 * Time: 16:01
 */

namespace App\Command;


use App\Entity\NatureSetting;
use App\Entity\Sav;
use App\Entity\StatusSetting;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportSavsNatureCommand extends Command
{
    protected static $defaultName = 'app:import:savnature';
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
        $this->setDescription('Importe les Natures des Savs depuis l\'intranet V1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->em;
        $batchSize = 100;
        $i = 0;
        $count = 0;
        $savsNatures = [];
        $natureValues = [
            '0' => 'Consommables',
            '1' => 'Produit incomplet',
            '2' => 'Lié à l\'installation (drivers)',
            '3' => 'Lié à l\'imprimante',
            '4' => 'Lié à la livraison',
            '5' => 'Qualité d\'impression',
            '6' => 'Machine en erreur / code erreur',
            '7' => 'Machine ne s\'allume pas',
            '8' => 'Problème de configuration (en réseau)',
            '9' => 'Carton abîmé',
            '10' => 'Paramétrage de l\'imprimante',
        ];

        try {
            $bdd = new \PDO('mysql:host=127.0.0.1:3306;dbname='.$this->dbName.';charset=utf8', $this->dbUser, $this->dbPwd);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        $query = $bdd->query('
            SELECT
                id AS id,
                naturepb AS naturepb
            FROM sav
        ');

        while($data = $query->fetch()){
            $natures = explode(';', $data['naturepb']);
            $savsNatures[] = [
                'id' => $data['id'],
                'nature' => $natures
            ];
        }
        $query->closeCursor();

        foreach ($savsNatures as $savsNature){
            foreach ($savsNature['nature'] as $natureIndex){
                if (null !== $natureIndex && $natureIndex !== ''){
                    $count++;
                }
            }
        }

        $output->writeln('');
        $output->writeln('========== SAVS NATURES ==========');
        $progress = new ProgressBar($output, $count);

        foreach ($savsNatures as $savsNature){

            /** @var Sav $sav */
            $sav = $manager->getRepository(Sav::class)
                ->findOneBy([
                    'oldId' => $savsNature['id']
                ]);

            if (null !== $sav){
                foreach ($savsNature['nature'] as $natureIndex){
                    if (null !== $natureIndex && $natureIndex !== ''){

                        $nature = $natureValues[$natureIndex];

                        /** @var NatureSetting $natureSetting */
                        $natureSetting = $manager->getRepository(NatureSetting::class)
                            ->findOneBy([
                                'setting' => $nature
                            ]);
                        if (null !== $natureSetting) {
                            $sav->addNatureSetting($natureSetting);

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
                }
            }
        }
        $manager->flush();
        $progress->setMessage($i, 'item');
        $progress->displayMessage($i);
        $progress->finish();
    }
}