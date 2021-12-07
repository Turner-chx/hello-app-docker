<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 29/03/19
 * Time: 09:38
 */

namespace App\Command;


use App\Entity\Article;
use App\Entity\Customer;
use App\Entity\Dealer;
use App\Entity\Production;
use App\Entity\Sav;
use App\Entity\Source;
use App\Entity\StatusSetting;
use App\Entity\User;
use App\Enum\ClientTypeEnum;
use App\Helper\ProgressBar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppUpdateSavsCommand extends Command
{
    protected static $defaultName = 'app:update:savs';
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Update les SAV');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->em;
        $batchSize = 100;
        $i = 0;

        $savs = $manager->getRepository(Sav::class)->findAll();

        $count = \count($savs);
        $output->writeln('');
        $output->writeln('========== MAJ SAVS ==========');
        $progress = new ProgressBar($output, $count);

        foreach ($savs as $sav) {
            /** @var StatusSetting $statusSetting */
            $statusSetting = $sav->getStatusSetting();

            if (null !== $statusSetting) {
                $sav->setOver($statusSetting->getOver());
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
        $manager->flush();
        $progress->setMessage($i, 'item');
        $progress->displayMessage($i);
        $progress->finish();
    }
}