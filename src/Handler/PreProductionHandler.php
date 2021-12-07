<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 05/06/19
 * Time: 15:01
 */

namespace App\Handler;


use App\Entity\Production;
use App\Entity\SerialNumberLmeco;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class PreProductionHandler
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createPreProductions(FormInterface $form): array
    {
        $manager = $this->em;

        $i = 0;
        $productions = [];
        $quantity = abs($form->get('quantity')->getData());

        while ($i < $quantity) {

            $production = new Production();
            $production->setArticle($form->get('article')->getData());
            $production->setUser($form->get('user')->getData());
            $production->setSupplier($form->get('supplier')->getData());
            $production->setArrival($form->get('arrival')->getData());

//            $serialNumberLmeco = $manager->getRepository(SerialNumberLmeco::class)->findOneBy(['status' => true]);
//
//            if (null !== $serialNumberLmeco) {
//                $production->setSerialNumberLmeco($serialNumberLmeco);
//                $production->setStringSerialNumberLmeco($serialNumberLmeco->getSerialNumber());
//
//                $serialNumberLmeco->setStatus(false);
//                $manager->persist($serialNumberLmeco);
//            }

            foreach ($form->get('features')->getData() as $feature) {
                $production->addFeature($feature);
            }

            $manager->persist($production);
            $manager->flush();

            $productions[] = $production;
            $i++;
        }

        return $productions;
    }
}