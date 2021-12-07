<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 18/04/19
 * Time: 15:15
 */

namespace App\Command;


use App\Entity\Article;
use App\Entity\FeatureSubProductType;
use App\Entity\Brand;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppImportFeaturesSubProductsCommand extends Command
{
    private $em;
    private $projectDir;

    protected static $defaultName = 'app:import:features';

    public function __construct(EntityManagerInterface $em, string $projectDir)
    {
        $this->em = $em;
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Importe les caractéristiques imprimantes des sous gammes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = 'features.csv';
        $featuresInfos = [];
        $manager = $this->em;
        $i = 0;

        $handle = fopen($this->projectDir . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $filename, 'rb');

        if (false === $handle) {
            return false;
        }

        while (false !== ($data = fgetcsv($handle, 1024, ';'))) {
            $featuresInfos[] = $data[0];
        }
        fclose($handle);

        $count = \count($featuresInfos);

        $output->writeln('');
        $output->writeln('========== IMPORT FEATURES SUB PRODUCTS ==========');
        $progress = new ProgressBar($output, $count);

        foreach ($featuresInfos as $value){

            $feature = new FeatureSubProductType();
            $feature->setFeature($value);

            $manager->persist($feature);
            $i++;
            $progress->setMessage($i, 'item');
            $progress->advance();
            $progress->displayMessage($i);
            $manager->flush();
        }
        $manager->flush();
    }
}