<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 18/04/19
 * Time: 15:15
 */

namespace App\Command;


use App\Entity\FeatureSubProductType;
use App\Entity\ProductType;
use App\Entity\Brand;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppUpdateSubProductTypesCommand extends Command
{
    private $em;
    private $baseUri;
    private $projectDir;

    protected static $defaultName = 'app:update:subproduct:types';

    public function __construct(EntityManagerInterface $em, string $baseUri, string $projectDir)
    {
        $this->em = $em;
        $this->baseUri = $baseUri;
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Met à jour les sous gammes entre le site LMECO et l\'intranet');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->em;

        $baseUri = $this->baseUri;
        $subProductTypeMany = '/subgammes-many';
        $uri = $baseUri . $subProductTypeMany;

        $filename = 'features.csv';
        $featuresInfos = [];

        $i = 0;

        $handle = fopen($this->projectDir . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $filename, 'rb');

        if (false === $handle) {
            return false;
        }

        while (false !== ($data = fgetcsv($handle, 1024, ';'))) {
            $featuresInfos[] = $data[0];
        }
        fclose($handle);

        $client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => 200.0,
        ]);

        $response = json_decode($client->get($uri)->getBody()->getContents(), true);

        $count = \count($response);

        $output->writeln('');
        $output->writeln('========== UPDATE SUBPRODUCT TYPES ==========');
        $progress = new ProgressBar($output, $count);

        foreach ($response as $value){

            $subProductType = $manager->getRepository(Brand::class)
                ->findOneBy([
                    'oldId' => $value['id']
                ]);

            if (null === $subProductType){
                $subProductType = new Brand();
            }

            $subProductType->setType($value['name']);
            $subProductType->setSlug($value['slug']);
            $subProductType->setOldId($value['id']);

            if (array_key_exists('productTypeId', $value)){
                $productType = $manager->getRepository(ProductType::class)
                    ->findOneBy([
                        'oldId' => $value['productTypeId']
                    ]);
                if (null !== $productType) {
                    $subProductType->setProductType($productType);
                }

                if ($productType->getSlug() === 'imprimante'){
                    foreach ($featuresInfos as $featureInfo){
                        $feature = $manager->getRepository(FeatureSubProductType::class)
                            ->findOneBy([
                                'feature' => $featureInfo
                            ]);
                        if (null !== $feature){
                            $subProductType->addFeatureSubProductType($feature);
                        }
                    }
                }
            }

            $i++;
            $manager->persist($subProductType);
            $progress->setMessage($i, 'item');
            $progress->advance();
            $progress->displayMessage($i);
            $manager->flush();
        }
    }
}