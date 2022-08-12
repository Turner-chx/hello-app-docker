<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Divalto\DivaltoHandler;
use App\Entity\Article;
use App\Entity\Sav;
use App\Entity\SavHistory;
use App\Entity\StatusSetting;
use App\Enum\HistoryEventsEnum;
use App\Mailer\Mailer;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SavAdminController extends CRUDController
{
    /**
     * @Route("/is-new-message", name="new_message")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function newMessageAction(Request $request, TranslatorInterface $translator): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $data = $request->request->all();

            $sav = $this->getDoctrine()->getManager()->getRepository(Sav::class)->find($data['savId']);

            $sav->setNewMessage(false);
            $this->getDoctrine()->getManager()->persist($sav);
            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse([
                'success' => true,
            ]);
        }
        return new JsonResponse([
            'success' => false,
            'message' => $translator->trans('app.error.user.not_authorized')
        ]);
    }

    /**
     * @Route("/is-new-sav", name="new_sav")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function newSavAction(Request $request, TranslatorInterface $translator): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $data = $request->request->all();

            $sav = $this->getDoctrine()->getManager()->getRepository(Sav::class)->find($data['savId']);

            $sav->setIsNew(false);
            $this->getDoctrine()->getManager()->persist($sav);
            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse([
                'success' => true,
            ]);
        }
        return new JsonResponse([
            'success' => false,
            'message' => $translator->trans('app.error.user.not_authorized')
        ]);
    }

    /**
     * @Route("/send-to-divalto/{id}", name="send_to_divalto")
     */
    public function sendToDivaltoAction($id, DivaltoHandler $divalto, TranslatorInterface $translator)
    {
        $sav = $this->getDoctrine()->getRepository(Sav::class)
            ->findOneBy([
                'id' => $id
            ]);
        if (null === $sav) {
            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        $customer = $sav->getCustomer();
        $dealer = $sav->getDealer();
        if (null === $translator) {
            $this->addFlash('danger', 'Une erreur est survenue');
            return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $id]));
        }
        if (null === $customer) {
            $this->addFlash('danger', $translator->trans('app.entity.Sav.divalto.unknown_customer'));
            return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $id]));
        }
        if (null === $dealer) {
            $this->addFlash('danger', $translator->trans('app.entity.Sav.divalto.unknown_dealer'));
            return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $id]));
        }
        $resultParamsSav = $divalto->getParamsForSav($sav, $customer, $dealer);
        if (!$resultParamsSav['success']) {
            $this->addFlash('danger', $translator->trans('app.entity.Sav.divalto.unknown_customer'));
            return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $id]));
        }
        $paramsSav = $resultParamsSav['params'];
        $params = &$paramsSav;
        /** @var Article $replacementArticle */
        foreach ($sav->getReplacementArticles() as $replacementArticle) {
            $paramsSav['<CDLG>'][] = [
                'numligne' => count($params['<CDLG>']),
                'référence' => $replacementArticle->getReference(),
                'quantité' => 1,
                'prix' => 0,
                'remise en montant' => 0,
                'remise en %' => 100,
                'code opération' => 'CGR',
                'motif de retour' => '',
                'désignation' => ''
            ];
        }
        $return = $divalto->createOrder($paramsSav);
        if (isset($return['error'])) {
            $this->addFlash('danger', $return['error']);
            return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $id]));
        }
        if (null !== $sav->getDivaltoNumber()) {
            $sav->setDivaltoNumber($sav->getDivaltoNumber() . '/' . $return['commande_ref']);
        } else {
            $sav->setDivaltoNumber($return['commande_ref']);
        }
        $this->getDoctrine()->getManager()->persist($sav);
        $this->getDoctrine()->getManager()->flush();
        return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $id]));
    }

    /**
     * @Route("/send-mail-sav-commercial/{id}", name="send_mail_commercial")
     */
    public function sendMailCommercialAction($id, Mailer $mailer): RedirectResponse
    {
        $sav = $this->getDoctrine()->getRepository(Sav::class)->findOneBy(['id' => $id]);

        if (null === $sav) {
            $this->addFlash('danger', 'Erreur SAV non trouvé');
            return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $id]));
        }
        $savArticles = $sav->getSavArticles();

        $mailer->sendMailCommercialSav($sav, $savArticles);
        $sav->setEmailSent(true);
        $this->getDoctrine()->getManager()->persist($sav);
        $this->getDoctrine()->getManager()->flush();
        return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $id]));
    }

    public function endClosingAction(){

        $statusEnAttente = $this->getDoctrine()->getRepository(StatusSetting::class)->findOneBy([
            'setting' => 'En attente'
        ]);
        if (null === $statusEnAttente) {
            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        $statusResolute = $this->getDoctrine()->getRepository(StatusSetting::class)->findOneBy([
            'setting' => 'Résolu'
        ]);
        if (null === $statusResolute) {
            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        $allSav = $this->getDoctrine()->getRepository(Sav::class)->findBy([
            'statusSetting' => $statusEnAttente->getId()
        ]);

        
        $date = new \DateTime();
        foreach ($allSav as $sav){
            $savHistories = $sav->getSavHistories();
            $lastHistory = null;
            foreach ($savHistories as $savHistory ){
                if ($lastHistory === null) {
                    $lastHistory = $savHistory;
                } else if ($lastHistory->getId() < $savHistory->getId()){
                    $lastHistory = $savHistory;
                }
            }
            $dateInterval = date_diff($date, $lastHistory->getHistoryDate());
            if ($dateInterval->days >= 30 && ($lastHistory->getStatusSetting() !== null) && $sav->getStatusSetting()->getSetting() === $statusEnAttente->getSetting()) {
                $sav->setStatusSetting($statusResolute);
                $this->getDoctrine()->getManager()->persist($sav);
                $this->getDoctrine()->getManager()->flush();

                $allSavHistory2 = $this->getDoctrine()->getRepository(SavHistory::class)->findBy([
                    'sav' => $lastHistory->getSav(),
                ]);
                $lastHistory = end($allSavHistory2);
                $lastHistory->setEvent(HistoryEventsEnum::ENDCLOSING);

                $this->getDoctrine()->getManager()->persist($lastHistory);
                $this->getDoctrine()->getManager()->flush();
            }
        }
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
