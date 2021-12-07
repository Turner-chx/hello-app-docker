<?php
/**
 * Created by PhpStorm.
 * User: Alexandre Lagoutte
 * Date: 27/06/19
 * Time: 13:46
 */

namespace App\Handler;


use App\Entity\Source;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class SavHandler
{
    private $em;
    private $requestStack;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function getAllSources()
    {
        return $this->em->getRepository(Source::class)
            ->findAll();
    }

    public function getSource(): ?Source
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $this->em->getRepository(Source::class)
                ->findOneBy([
                    'defaultSource' => true
                ]);
        }
        $source = $this->em->getRepository(Source::class)
            ->findOneBy([
                'name' => $request->getHttpHost()
            ]);
        if (!$source) {
            $source = $this->em->getRepository(Source::class)
                ->findOneBy([
                    'defaultSource' => true
                ]);
        }
        return $source;
    }
}