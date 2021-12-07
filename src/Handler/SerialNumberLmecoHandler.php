<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 11/04/19
 * Time: 16:29
 */

namespace App\Handler;


use App\Entity\SerialNumberLmeco;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SerialNumberLmecoHandler
{
    private $em;
    private $months = [
        '01' => 'D',
        '02' => 'E',
        '03' => 'F',
        '04' => 'G',
        '05' => 'H',
        '06' => 'K',
        '07' => 'L',
        '08' => 'M',
        '09' => 'N',
        '10' => 'P',
        '11' => 'R',
        '12' => 'S'
    ];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createSerialNumber(FormInterface $form): StreamedResponse
    {
        $manager = $this->em;

        $response = new StreamedResponse();
        $response->setCallback(function () use ($form, $manager) {

            $currentYear = date('Y');
            $currentMonth = date('m');

            $h = 0;

            $handle = fopen('php://output', 'wb+');
            fputcsv($handle, array('Serial number LMECO'));

            while ($h  < abs($form->get('quantityCreated')->getData())) {
                $strings = ['B', 'C', 'A', '0'];
                $letter = $this->months[$currentMonth];

                $count = $manager->getRepository(SerialNumberLmeco::class)->searchByMonthCount();
                $countCode = $count;
                $countCode++;

                $countCodeTmp = $countCode % 10;
                $i = 1;
                $j = 1;
                $k = 1;
                $l = 1;
                while ($i <= $countCodeTmp) {
                    $strings[3]++;
                    $i++;
                }
                $countCodeTmp = (int)($countCode / 10);
                $countCodeTmp2 = $countCodeTmp % 26;
                while ($j <= $countCodeTmp2) {
                    $strings[2]++;
                    $j++;
                }
                $countCodeTmp3 = (int)($countCodeTmp / 26);
                while ($k <= $countCodeTmp3) {
                    $strings[1]++;
                    $k++;
                }
                $countCodeTmp4 = $countCodeTmp3 % 24;
                $countCodeTmp4 = (int)($countCodeTmp4 / 24);
                while ($l <= $countCodeTmp4) {
                    $strings[0]++;
                    $l++;
                }
                $finalString = '';
                foreach ($strings as $string) {
                    $finalString .= $string;
                }

                $secret = (69 + ((int)$currentYear * (int)$currentMonth * $count)) % 10;
                $code = $letter . $finalString . '0' . $secret;

                fputcsv($handle, array($code), ';');

                $serialNumberLmeco = new SerialNumberLmeco();
                $serialNumberLmeco->setSerialNumber($code);
                $manager->persist($serialNumberLmeco);

                $manager->flush();

                $h++;
            }
            fclose($handle);

        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="serial_number.csv"');

        return $response;
    }
}