<?php


namespace App\EventDispatcher;


use App\Entity\Production;
use App\Entity\ProductionHistory;
use App\Entity\Sav;
use App\Entity\SavHistory;
use App\Entity\SerialNumberLmeco;
use App\Entity\User;
use App\Enum\HistoryEventsEnum;

class HistoryListener
{
    /**
     * @param Production|Sav $entity
     * @param string $event
     * @return ProductionHistory|SavHistory
     */
    public function addHistory($entity, string $event)
    {
        if ($event === HistoryEventsEnum::NEW) {
            $historyDate = $entity->getCreatedAt();
        } else {
            $historyDate = $entity->getUpdatedAt();
        }

        switch (get_class($entity)) {
            case Sav::class:
                /** @var Sav $sav */
                $sav = $entity;
                $savHistory = new SavHistory();

                $savHistory->setHistoryDate($historyDate);
                $savHistory->setEvent($event);

                /** @var User $user */
                $user = $sav->getUser();
                if (null !== $user) {
                    $savHistory->setUserName($user->getFirstName() . ' ' . $user->getLastName());
                }

                if (null !== $sav->getComment()) {
                    $savHistory->setComment($sav->getComment());
                }

                $savHistory->setStatusSetting($sav->getStatusSetting());
                if (null !== $sav->getProduction()) {
                    $savHistory->setSavProduction($sav->getProduction()->getSerialNumber());
                }

                return $savHistory;
                break;
            case Production::class:
                /** @var Production $production */
                $production = $entity;
                $productionHistory = new ProductionHistory();

                /** @var SerialNumberLmeco $serialNumberLmeco */
                $serialNumberLmeco = $production->getSerialNumberLmeco();
                if (null !== $serialNumberLmeco) {
                    $productionHistory->setSerialNumberLmeco($serialNumberLmeco->getSerialNumber());
                }
                $productionHistory->setEvent($event);

                /** @var User $user */
                $user = $production->getUser();
                if (null !== $user) {
                    $productionHistory->setUserName($user->getFirstName() . ' ' . $user->getLastName());
                }

                $productionHistory->setHistoryDate($historyDate);

                if (null !== $production->getComment()) {
                    $productionHistory->setComment($production->getComment());
                }

                return $productionHistory;
                break;
        }
    }
}