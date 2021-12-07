<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 26/08/2019
 * Time: 15:29
 */

namespace App\Controller\Admin;


use App\Divalto\DivaltoHandler;
use App\Entity\Sav;
use App\Entity\SavHistory;
use App\Entity\User;
use App\Enum\HistoryEventsEnum;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class DivaltoController extends AbstractController
{
    /**
     * @Route("/send-replaced-production-to-divalto/{id}", name="sendReplacedProductionToDivalto")
     * @param $id
     * @param Request $request
     * @param DivaltoHandler $divaltoHandler
     * @param TranslatorInterface $translator
     * @return RedirectResponse
     */
    public function sendReplacedProductionToDivalto($id, Request $request, DivaltoHandler $divaltoHandler, TranslatorInterface $translator): RedirectResponse
    {
        /** @var Sav $sav */
        $sav = $this->getDoctrine()->getRepository(Sav::class)
            ->findOneBy([
                'id' => $id
            ]);
        if (null === $sav) {
            $this->addFlash('danger', $translator->trans('app.entity.Sav.action.send_link.error.unknown_sav'));
            return new RedirectResponse($request->headers->get('referer'));
        }
        $result = $divaltoHandler->sendSavToDivalto($sav, true);
        if ($result['success']) {
            $savHistory = new SavHistory();

            $savHistory->setHistoryDate(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
            $savHistory->setEvent( HistoryEventsEnum::REPLACE);

            /** @var User $user */
            $user = $sav->getUser();
            if (null !== $user) {
                $savHistory->setUserName($user->getFirstName() . ' ' . $user->getLastName());
            }

            $savHistory->setComment('Nouvel envoi Divalto ' . $sav->getDivaltoNumber());

            $savHistory->setStatusSetting($sav->getStatusSetting());
            if (null !== $sav->getProduction()) {
                $savHistory->setSavProduction($sav->getProduction()->getSerialNumber());
            }
            $this->addFlash('success', $translator->trans($result['message'], ['%divaltoNumber%' => $sav->getDivaltoNumber()]));
        } else {
            $this->addFlash('danger', $translator->trans($result['message']));
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}