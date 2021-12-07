<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 24/06/19
 * Time: 15:00
 */

namespace App\Controller\Web;


use App\Entity\Files;
use App\Entity\Messaging;
use App\Entity\Sav;
use App\Entity\Source;
use App\Enum\ClientTypeEnum;
use App\Enum\SenderFileEnum;
use App\Form\FilesType;
use App\Form\MessageFormType;
use App\Form\RequestForSavType;
use App\Handler\RequestForSavHandler;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class RequestForSavController extends AbstractController
{
    /**
     * @param Request $request
     * @param RequestForSavHandler $requestForSavHandler
     * @return Response
     * @throws Exception
     */
    public function newRequestForSav(Request $request, RequestForSavHandler $requestForSavHandler): Response
    {
        $form = $this->createForm(RequestForSavType::class, null,  ['source' => $request->get('source')]);
        $form->handleRequest($request);

        /** @var Source $source */
        $source = $this->getDoctrine()->getManager()->getRepository(Source::class)->findOneBy([
            'slug' => $request->get('source')
        ]);

        if (null !== $source) {
            if ($form->isSubmitted() && $form->isValid()) {

                $sav = $requestForSavHandler->createSav($form, $source);

                $code = $requestForSavHandler->generateCode($sav);

                $requestForSavHandler->sendConfirmation($sav, $source, $code);

                if ($sav->getClientType() === ClientTypeEnum::DEALER) {
                    $typeClient = ClientTypeEnum::DEALER;
                } else {
                    $typeClient = ClientTypeEnum::CUSTOMER;
                }

                return $this->render('sav/url_my_sav.html.twig', [
                    'savId' => $sav->getId(),
                    'code' => $code,
                    'typeClient' => $typeClient
                ]);
            }

            return $this->render('sav/request_for_sav.html.twig', [
                'form' => $form->createView(),
                //'display_imei' => $source->getDisplayImeiNumber(),
                'idSource' => $source->getId()
            ]);
        }

        // Mettre une erreur car la source n'existe pas
    }

    /**
     * @param Request $request
     * @param RequestForSavHandler $requestForSavHandler
     * @param $clientType
     * @param $codeSav
     * @return Response
     * @throws Exception
     */
    public function followMySav(Request $request, RequestForSavHandler $requestForSavHandler, $codeSav): Response
    {
        /** @var Sav $sav */
        $sav = $this->getDoctrine()->getManager()->getRepository(Sav::class)->findOneBy([
            'secretCode' => $codeSav
        ]);

        if (null === $sav) {
            $this->addFlash('danger', 'Vous n\'êtes pas autorisé à consulter cette page');
            return $this->redirectToRoute('index');
        }
        $filesProofSavInfos = [];
        $filesProofSavArticleInfos = [];

        $filesProofSav = $sav->getSavFilesProof();
        foreach ($sav->getSavArticles() as $savArticle) {
            foreach ($savArticle->getFilesProof() as $file) {
                if (!isset($filesProofSavArticleInfos[$savArticle->getId()])) {
                    $filesProofSavArticleInfos[$savArticle->getId()] = [];
                }
                $mimeType = @mime_content_type($this->getParameter('kernel.project_dir').DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$this->getParameter('app.path.files').DIRECTORY_SEPARATOR.$file->getName());
                switch ($mimeType) {
                    case 'application/pdf':
                        $icon = 'fa fa-file-pdf';
                        break;
                    case 'image/gif':
                    case 'image/png':
                    case 'image/jpeg':
                    case 'image/jpg':
                        $icon = 'fa fa-file-image';
                        break;
                    case 'application/msword':
                    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                        $icon = 'fa fa-file-word';
                        break;
                    case 'application/vnd.ms-excel':
                    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                        $icon = 'fa fa-file-excel';
                        break;
                    case 'text/csv':
                        $icon = 'fa fa-file-csv';
                        break;
                    default:
                        $icon = 'fa fa-file';
                        break;
                }

                $sender = SenderFileEnum::get($file->getSender());

                $filesProofSavArticleInfos [$savArticle->getId()][] = [
                    'file' => $file,
                    'fileIcon' => $icon,
                    'sender' => $sender
                ];
            }
        }

        if ($filesProofSav->count() > 0) {
            foreach ($filesProofSav as $file) {
                
                $mimeType = @mime_content_type($this->getParameter('kernel.project_dir').DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$this->getParameter('app.path.files').DIRECTORY_SEPARATOR.$file->getName());
                switch ($mimeType) {
                    case 'application/pdf':
                        $icon = 'fa fa-file-pdf';
                        break;
                    case 'image/gif': 
                    case 'image/png':
                    case 'image/jpeg':
                    case 'image/jpg':
                        $icon = 'fa fa-file-image';
                        break;
                    case 'application/msword':
                    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                        $icon = 'fa fa-file-word';
                        break;
                    case 'application/vnd.ms-excel':
                    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                        $icon = 'fa fa-file-excel';
                        break;
                    case 'text/csv':
                        $icon = 'fa fa-file-csv';
                        break;
                    default:
                        $icon = 'fa fa-file';
                        break;
                }

                $sender = SenderFileEnum::get($file->getSender());

                $filesProofSavInfos [] = [
                    'file' => $file,
                    'fileIcon' => $icon,
                    'sender' => $sender
                ];
            }
        }
        $clientType = $sav->getClientType();

        if ($clientType !== ClientTypeEnum::CUSTOMER && $clientType !== ClientTypeEnum::DEALER) {
            // WLAN ERROR
            die();
        }

        $message = new Messaging();

        $messageSender = null;

        if ($clientType === ClientTypeEnum::CUSTOMER) {
            $message->setSender(SenderFileEnum::CUSTOMER);
        } elseif ($clientType === ClientTypeEnum::DEALER) {
            $message->setSender(SenderFileEnum::DEALER);
        }

        $message->setSav($sav);

        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hasError = false;

            if (!$hasError) {
                $sav->setNewMessage(true);
                $this->getDoctrine()->getManager()->persist($sav);

                $requestForSavHandler->newMessage($sav, $message, true);

                $this->getDoctrine()->getManager()->persist($message);
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('follow-my-sav', [
                    'codeSav' => $codeSav,
                    'clientType' => $clientType
                ]);
            }
        }
        $messageInfos = [];
        foreach ($sav->getMessagings() as $messaging) {
            $messageInfos [] = [
                'message' => $messaging,
                'sender' => $messaging->getSender(),
                'senderTranslate' => SenderFileEnum::get($messaging->getSender()),
                'files' => $messaging->getFiles()
            ];
        }


        $formFile = $this->createForm(FilesType::class, new Files());
        $formFile->handleRequest($request);

        if ($formFile->isSubmitted() && $formFile->isValid()) {

            /** @var Files $newFile */
            $newFile = $formFile->getData();


            if ($clientType === ClientTypeEnum::CUSTOMER) {
                $newFile->setSender(SenderFileEnum::CUSTOMER);
            } elseif ($clientType === ClientTypeEnum::DEALER) {
                $newFile->setSender(SenderFileEnum::DEALER);
            }

            $sav->addSavFilesProof($newFile);

            $this->getDoctrine()->getManager()->persist($sav);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('follow-my-sav', [
                'codeSav' => $codeSav,
                'clientType' => $clientType
            ]);
        }

        return $this->render('sav/follow-my-sav.html.twig', [
            'sav' => $sav,
            'client_type' => $clientType,
            'form' => $form->createView(),
            'formFile' => $formFile->createView(),
            'fileInfos' => $filesProofSavInfos,
            'savArticleFileInfos' => $filesProofSavArticleInfos,
            'messageInfos' => $messageInfos
        ]);

    }
}