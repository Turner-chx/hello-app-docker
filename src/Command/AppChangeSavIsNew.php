<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 23/10/19
 * Time: 10:01
 */

namespace App\Command;


use App\Entity\Sav;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppChangeSavIsNew extends Command
{

    protected static $defaultName = 'app:change:sav:isnew';

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Commande pour changer le isnew des Sav');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->em;

        $savs = $em->getRepository(Sav::class)->findAll();
        $i = 0;
        $batchSize = 50;

        if (!empty($savs)) {
            foreach ($savs as $sav) {
                foreach ($sav->getSavArticles() as $savArticle) {
                    $article = $savArticle->getArticle();
                    if (null !== $article) {
                        $family = $article->getProductType();
                        if (null !== $family) {
                            $sav->setFamily($family->getCodeLama());
                            $em->persist($sav);
                        }
                    }
                }
                $i++;
                if ($i % $batchSize === 0) {
                    $em->flush();
                }
            }
            $em->flush();
        }
    }
}