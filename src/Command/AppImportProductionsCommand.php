<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 27/03/19
 * Time: 15:56
 */

namespace App\Command;


use App\Entity\Article;
use App\Entity\Production;
use App\Entity\User;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportProductionsCommand extends Command
{
    protected static $defaultName = 'app:import:productions';
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
        $this->setDescription('Importe les Productions depuis l\'intranet V1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '8192M');
        $manager = $this->em;
        $batchSize = 1000;
        $i = 0;

        try {
            $bdd = new \PDO('mysql:host=127.0.0.1:3306;dbname=' . $this->dbName . ';charset=utf8', $this->dbUser, $this->dbPwd);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        $queryCount = $bdd->query('
            SELECT 
              COUNT(*) AS count
            FROM production p 
            JOIN articles a 
              ON p.idArticle = a.id
        ');

        while ($data = $queryCount->fetch()) {
            $count = $data['count'];
        }
        $queryCount->closeCursor();

        $query = $bdd->query('
            SELECT p.id AS id, 
                   a.reference AS reference, 
                   p.numserie AS numserie, 
                   p.idOperateur AS idoperateur, 
                   p.dateproduction AS dateproduction, 
                   p.commentaire AS commentaire 
            FROM production p 
            JOIN articles a 
              ON p.idArticle = a.id
        ');

        $output->writeln('');
        $output->writeln('========== PRODUCTION ==========');
        $progress = new ProgressBar($output, $count);

        while ($data = $query->fetch()) {
            /** @var Article $article */
            $article = $manager->getRepository(Article::class)
                ->findOneBy([
                    'reference' => $data['reference']
                ]);

            if (null !== $article) {

                $production = $manager->getRepository(Production::class)->findOneBy([
                    'oldId' => $data['id']
                ]);

                if (null === $production) {
                    $production = new Production();

                    if (null !== $data['commentaire'] && $data['commentaire'] !== '') {
                        $production->setComment($data['commentaire']);
                    }

                    /** @var \DateTime $date */
                    $date = ($data['dateproduction'] !== '0000-00-00 00:00:00') ? \DateTime::createFromFormat('Y-m-d H:i:s', $data['dateproduction']) : \DateTime::createFromFormat('Y-m-d H:i:s', \date('Y-m-d H:i:s'));
                    $production->setCreatedAt($date);
                    $production->setUpdatedAt($date);
                    $production->setOldId($data['id']);

                    if (null !== $data['idoperateur'] && $data['idoperateur'] !== '') {
                        /** @var User $user */
                        $user = $manager->getRepository(User::class)
                            ->findOneBy([
                                'email' => 'fcam@eco-imprimante.fr'
                            ]);

                        $production->setUser($user);
                    }

                    if (null !== $data['numserie'] && $data['numserie'] !== '') {
                        $production->setSerialNumber($data['numserie']);
                    }

                    $production->setArticle($article);

                    $i++;
                    $manager->persist($production);
                    $progress->setMessage($i, 'item');
                    $progress->advance();
                    $progress->displayMessage($i);

                    if ($i % $batchSize === 0) {
                        $manager->flush();
                    }
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