<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 16/12/20
 * Time: 15:19
 */

namespace App\EventListeners;


use App\Entity\Messaging;
use App\Entity\Sav;
use App\Entity\SavHistory;
use App\Entity\User;
use App\Enum\HistoryEventsEnum;
use App\Enum\SenderFileEnum;
use App\Handler\RequestForSavHandler;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\ORMException;

class DoctrineSubscriber implements EventSubscriber
{
    private $em;
    private $requestForSavHandler;

    public function __construct(EntityManager $entityManager, RequestForSavHandler $requestForSavHandler)
    {
        $this->em = $entityManager;
        $this->requestForSavHandler = $requestForSavHandler;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate,
            Events::postUpdate,
        ];
    }

    /**
     * @param PreUpdateEventArgs $args
     * @throws ORMException
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Sav) {
            /** @var Sav $sav */
            $sav = $entity;
            if ($args->hasChangedField('statusSetting')) {
                $event = HistoryEventsEnum::EVOLUTION;

            } else {
                $event = HistoryEventsEnum::EDIT;
            }

            $savHistory = $this->addHistory($sav, $event);

            $sav->addSavHistory($savHistory);

            $statusSetting = $sav->getStatusSetting();
            if (null !== $statusSetting) {
                if ($statusSetting->getOver()) {
                    $sav->setOver(true);
                } else {
                    $sav->setOver(false);
                }
            }
        }
    }

    public function postUpdate()
    {
        $this->em->flush();
    }

    /**
     * @param Sav $sav
     * @param string $event
     * @return SavHistory
     */
    public function addHistory(Sav $sav, string $event)
    {
        if ($event === HistoryEventsEnum::NEW) {
            $historyDate = $sav->getCreatedAt();
        } else {
            $historyDate = $sav->getUpdatedAt();
        }

        $savHistory = new SavHistory();

        $savHistory->setHistoryDate($historyDate);
        $savHistory->setEvent($event);

        /** @var User $user */
        $user = $sav->getUser();
        if (null !== $user) {
            $savHistory->setUserName($user->getUsername());
        }

        if (null !== $sav->getComment()) {
            $savHistory->setComment(substr($sav->getComment(), 0, 254));
        }

        if (null !== $sav->getStatusSetting()) {
            $savHistory->setStatusSetting($sav->getStatusSetting());
        }

        $this->em->persist($savHistory);

        return $savHistory;

    }
}