<?php

namespace App\Command;

use App\Entity\Sav;
use App\Entity\SavHistory;
use App\Entity\StatusSetting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppSelectOldSavCommand extends Command
{
    protected static $defaultName = 'app:selectOldSav';

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Commande pour selectioner les sav de plus de 30 jour');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->em;

        $allSavHistory = $manager->getRepository(SavHistory::class)->findAll();

        $statusEnAttente = $manager->getRepository(StatusSetting::class)->findOneBy([
            'id' => 2
        ]);
        $statusResolute = $manager->getRepository(StatusSetting::class)->findOneBy([
            'id' => 3
        ]);

        $date = new \DateTime();
        foreach ($allSavHistory as $savHistory){
            $dateInterval = date_diff($date, $savHistory->getHistoryDate());
            if (($savHistory->getStatusSetting() !== null) && $savHistory->getStatusSetting() === $statusEnAttente->getSetting() && $dateInterval->days >= 30) {
                $sav = $manager->getRepository(Sav::class)->findOneBy([
                    'id' => $savHistory->getId()
                ]);
                $sav->setStatusSetting($statusResolute);
                $manager->persist($sav);
            }
        }
        $manager->flush();
    }
}