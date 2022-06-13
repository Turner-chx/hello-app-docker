<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Dealer;
use App\Form\FileType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class DealerAdminController extends CRUDController
{
    public function importDealersAction(Request $request)
    {
        set_time_limit(0);
        ini_set('max_execution_time', '3600');
        $form = $this->createForm(FileType::class);
        $form->handleRequest($request);
        $manager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $dateStart = date('d/m/Y H:i:s');
            $data = $request->files->get('file');
            $i = 0;
            $batchSize = 500;
            if (isset($data['file'])) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $data['file']['file'];
                $readerSpreadsheet = IOFactory::createReader('Xlsx');
                $spreadsheet = $readerSpreadsheet->load($uploadedFile->getPathname());
                $spreadsheet->setActiveSheetIndex(1);
                $dealersArray = $spreadsheet->getActiveSheet()->toArray();

                //Unset useless rows
                unset($dealersArray[0], $dealersArray[1], $dealersArray[2], $dealersArray[3]);

                foreach ($dealersArray as $dealerItem) {
                    if (null === $dealerItem[1] || null === $dealerItem[4]) {
                        break;
                    }
                    $dealerCode = trim($dealerItem[1]);
                    $name = trim($dealerItem[4]);
                    $salesmanName = trim($dealerItem[4]);
                    $addAddress = trim($dealerItem[6]);
                    $address = trim($dealerItem[8]);
                    $city = trim($dealerItem[10] ?? '');
                    $country = trim($dealerItem[11]);
                    $postalCode = trim($dealerItem[12]);
                    $createdAt = new \DateTime();
                    $email = trim($dealerItem[16]);

                    $dealer = $manager->getRepository(Dealer::class)->findOneBy(['email' => $email]);

                    if (null === $dealer) {
                        $dealer = new Dealer();
                    }

                    $dealer->setName($name);
                    $dealer->setStatus(true);
                    $dealer->setCreatedAt($createdAt);
                    $dealer->setAddress($address);
                    $dealer->setAdditionalAddress($addAddress);
                    $dealer->setCity($city);
                    $dealer->setCountry($country);
                    $dealer->setPostalCode($postalCode);
                    $dealer->setDealerCode($dealerCode);
                    $dealer->setEmail($email);
                    $dealer->setSalesmanName($salesmanName);

                    $manager->persist($dealer);

                    if ($i % $batchSize === 0) {
                        $manager->flush();
                    }
                    $i++;
                }
            }
            $manager->flush();
            $dateEnd = date('d/m/Y H:i:s');
            $this->addFlash('success', $i . ' revendeurs mis à jour entre ' . $dateStart . ' et ' . $dateEnd);
            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->renderWithExtraParams('admin/dealer/import_dealer.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
