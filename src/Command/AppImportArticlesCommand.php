<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 27/03/19
 * Time: 09:05
 */

namespace App\Command;


use App\Entity\Article;
use App\Entity\Brand;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportArticlesCommand extends Command
{
    protected static $defaultName = 'app:import:articles';
    protected $em;
    protected $projectDir;
    protected $dbName;
    protected $dbUser;
    protected $dbPwd;

    public function __construct(EntityManagerInterface $em, string $projectDir, string $dbName, string $dbUser, string $dbPwd)
    {
        $this->em = $em;
        $this->projectDir = $projectDir;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPwd = $dbPwd;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Importe les Articles depuis l\'intranet V1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = 'asg.csv';
        $subTypeArticle = [];
        $i = 0;
        $articleDone = [];
        $batchSize = 20;

        $manager = $this->em;

        $handle = fopen($this->projectDir . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $filename, 'rb');

        if (false === $handle) {
            return false;
        }

        while (false !== ($data = fgetcsv($handle, 1024, ';'))) {
            $subTypeArticle[$data[0]] = $data[2];
        }
        fclose($handle);

        try {
            $bdd = new \PDO('mysql:host=127.0.0.1:3306;dbname='.$this->dbName.';charset=utf8', $this->dbUser, $this->dbPwd);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }

        $queryCount = $bdd->query('
            SELECT 
              COUNT(*) AS count
            FROM `articles`
        ');

        while ($data = $queryCount->fetch()) {
            $count = $data['count'];
        }

        $queryCount->closeCursor();

        $query = $bdd->query('
            SELECT 
              `reference` AS reference, 
              `designation` AS designation, 
              `designation_abregee` AS designation_abridged, 
              `famille1` AS famille1, 
              `famille2` AS famille2, 
              `famille3` AS famille3, 
              `ean` AS ean 
            FROM `articles`
        ');

        $output->writeln('');
        $output->writeln('========== ARTICLES ==========');
        $progress = new ProgressBar($output, $count);

        while ($data = $query->fetch()) {
            /** @var Article $article */
            $article = $manager->getRepository(Article::class)
                ->findOneBy([
                    'reference' => $data['reference']
                ]);

            if (null === $article && false === in_array($data['reference'], $articleDone, true)) {
                $articleDone[] = $data['reference'];
                $article = new Article();

                if(null !== $data['reference'] && $data['reference'] !== '') {
                    $article->setReference($data['reference']);
                }
                if(null !== $data['designation'] && $data['designation'] !== ''){
                    $article->setDesignation($data['designation']);
                }
                if (null !== $data['designation_abridged'] && $data['designation_abridged'] !== ''){
                    $article->setDesignationAbridged($data['designation_abridged']);
                }
                if (null !== $data['famille1'] && $data['famille1'] !== '' ){
                    $article->setFamily1($data['famille1']);
                }
                if (null !== $data['famille2'] && $data['famille2'] !== ''){
                    $article->setFamily2($data['famille2']);
                }
                if (null !== $data['famille3'] && $data['famille3'] !== ''){
                    $article->setFamily3($data['famille3']);
                }
                if (null !== $data['ean'] && $data['ean'] !== '') {
                    $article->setEan($data['ean']);
                }

                if (array_key_exists($data['reference'], $subTypeArticle)){
                    /** @var Brand $subProductType */
                    $subProductType = $manager->getRepository(Brand::class)
                        ->findOneBy([
                           'slug' => $subTypeArticle[$data['reference']]
                        ]);

                    $article->setSubProductType($subProductType);
                }

                $article->setGuarantee(12);

                $manager->persist($article);

                if ($i % $batchSize === 0) {
                    $manager->flush();
                }

                $i++;
                $progress->setMessage($i, 'item');
                $progress->advance();
                $progress->displayMessage($i);
            }
        }
        $query->closeCursor();
        $manager->flush();
        $progress->setMessage($i, 'item');
        $progress->displayMessage($i);
        $progress->finish();
    }
}