<?php

namespace App\Handler;

use App\Entity\SavHistory;
use App\Entity\StatusSetting;
use Doctrine\ORM\EntityManagerInterface;

class SelectOldSavHandler
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function selectOldSav()
    {
       $manager = $this->em;

        $statusEnAttente = $manager->getRepository(StatusSetting::class)->findOneBy([
            'id' => 2
        ]);
        if (null === $statusEnAttente) {
            return;
        }
        $statusResolute = $manager->getRepository(StatusSetting::class)->findOneBy([
            'id' => 3
        ]);
        if (null === $statusResolute) {
            return;
        }
        $allSavHistory = $manager->getRepository(SavHistory::class)->findBy([
            'statusSetting' => $statusEnAttente->getSetting()
        ]);
        $date = new \DateTime();
        foreach ($allSavHistory as $savHistory){
            $dateInterval = date_diff($date, $savHistory->getHistoryDate());
            $sav = $savHistory->getSav();
            if ($dateInterval->days >= 30 && ($savHistory->getStatusSetting() !== null) && $sav->getStatusSetting()->getId() === $statusEnAttente->getId()) {
                $sav->setStatusSetting($statusResolute);

                $manager->persist($sav);
                $manager->flush();

                $allSavHistory2 = $manager->getRepository(SavHistory::class)->findBy([
                    'sav' => $savHistory->getSav(),
                ]);
                $lastHistory = end($allSavHistory2);
                $lastHistory->setEvent('clôture automatique');

                $manager->persist($lastHistory);
                $manager->flush();
            }
        }
    }
}

