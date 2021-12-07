<?php


namespace App\Controller\Web;

use App\Entity\City;
use App\Entity\Files;
use App\Entity\Sav;
use App\Entity\Source;
use App\Entity\StatusSetting;
use App\Enum\SenderFileEnum;
use App\Form\SavType;
use App\Mailer\Mailer;
use Exception;
use Gedmo\Translator\TranslationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class SavController extends AbstractController
{
    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param Mailer $mailer
     * @return Response
     * @throws Exception
     */
    public function form(Request $request, TranslatorInterface $translator, Mailer $mailer): Response
    {
        $sav = new Sav();
        $form = $this->createForm(SavType::class, $sav);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $source = $this->getDoctrine()->getRepository(Source::class)
                ->findOneBy([
                    'name' => $request->getHttpHost()
                ]);
            if (!$source) {
                $source = $this->getDoctrine()->getRepository(Source::class)
                    ->findOneBy([
                        'defaultSource' => true
                    ]);
            }
            foreach ($sav->getSavArticles() as $savArticle) {
                foreach ($savArticle->getFilesProof() as $fileProof) {
                    $fileUnknown = $savArticle->getFileUnknown();
                    if ((null !== $fileUnknown) && $fileUnknown instanceof UploadedFile) {
                        $file = new Files();
                        $file->setFile($fileUnknown);
                        $file->setName($fileUnknown->getClientOriginalName());
                        $file->setSender(SenderFileEnum::CUSTOMER);
                        $this->getDoctrine()->getManager()->persist($file);
                        $savArticle->setFileUnknown($file);
                        $this->getDoctrine()->getManager()->persist($savArticle);
                    }
                    if ($fileProof instanceof UploadedFile) {
                        $file = new Files();
                        $file->setFile($fileProof);
                        $file->setName($fileProof->getClientOriginalName());
                        $file->setSender(SenderFileEnum::CUSTOMER);
                        $this->getDoctrine()->getManager()->persist($file);
                        $savArticle->removeFilesProof($fileProof);
                        $savArticle->addFilesProof($file);
                        $this->getDoctrine()->getManager()->persist($savArticle);
                    }
                }
                $article = $savArticle->getArticle();
                if (null !== $article) {
                    $sav->addReplacementArticle($article);
                    $family = $article->getProductType();
                    if (null !== $family) {
                        $sav->setFamily($family->getCodeLama());
                    }
                }
            }
            foreach ($sav->getSavFilesProof() as $fileProof) {
                if ($fileProof instanceof UploadedFile) {
                    $file = new Files();
                    $file->setFile($fileProof);
                    $file->setSender(SenderFileEnum::CUSTOMER);
                    $file->setName($fileProof->getClientOriginalName());
                    $this->getDoctrine()->getManager()->persist($file);
                    $sav->removeSavFilesProof($fileProof);
                    $sav->addSavFilesProof($file);
                }
            }
            $state = $this->getDoctrine()->getRepository(StatusSetting::class)
                ->findOneBy([
                    'byDefault' => true
                ]);
            if (null !== $state) {
                $sav->setStatusSetting($state);
            }
            $code = $this->generateCode($sav);
            $sav->setSecretCode($code);
            if (null !== $source) {
                $sav->setSource($source);
                $sav->setDealer($source->getDealer());
            }
            $this->getDoctrine()->getManager()->persist($sav);
            $this->getDoctrine()->getManager()->flush();


            $mailer->sendMailSavNew($sav);
            $mailer->sendAdminMailSavNew($sav);

            $this->addFlash('success', $translator->trans('app.mail.sav_new.name'));
            return $this->redirectToRoute('sav_success', ['code' => $sav->getSecretCode()]);
        }

        return $this->render('sav/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function success($code)
    {
        $sav = $this->getDoctrine()->getRepository(Sav::class)
            ->findOneBy([
                'secretCode' => $code
            ]);
        if (null === $sav) {
            $this->addFlash('danger', 'Vous n\'êtes pas autorisé à consulter cette page');
            return $this->redirectToRoute('index');
        }
        return $this->render('sav/sav_success.html.twig', ['savId' => $sav->getId(), 'code' => $sav->getSecretCode()]);
    }

    public function emailSent(Request $request)
    {
        $idSav = $request->request->get('sav');
        $sav = $this->getDoctrine()->getRepository(Sav::class)
            ->findOneBy([
                'id' => $idSav
            ]);
        if (null === $sav) {
            return new JsonResponse([]);
        }
        $sav->setEmailSent(true);
        $this->getDoctrine()->getManager()->persist($sav);
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @param Sav $sav
     * @return string
     * @throws Exception
     */
    public function generateCode(Sav $sav): string
    {
        $codeHash = [];
        $i = 0;
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $randomCoeff = 15;
        $code = '';

        $idNumber = str_split($sav->getId());
        foreach ($idNumber as $number) {
            for ($j = 0; $j < $randomCoeff; $j++) {
                $codeHash[$i][] = $alphabet[random_int(0, 25)];
            }

            $codeHash[$i][random_int(0, $randomCoeff - 1)] = $number;
            $i++;
        }

        foreach ($codeHash as $array) {
            $code .= implode('', $array);
        }

        return $code;
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function findCity(Request $request, TranslatorInterface $translator): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $data = $request->request->all();

            $postCode = $data['postCode'];

            $city = $this->getDoctrine()->getRepository(City::class)->findOneBy([
                'postCode' => $postCode
            ]);

            if (null !== $city) {
                return new JsonResponse([
                    'success' => true,
                    'cityName' => strtoupper($city->getName())
                ]);
            }

            return new JsonResponse([
                'success' => false,
                'message' => $translator->trans('app.error.sav.not_authorized')
            ]);
        }

        return new JsonResponse([
            'success' => false,
            'message' => $translator->trans('app.error.sav.not_authorized')
        ]);
    }
}

