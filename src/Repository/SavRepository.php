<?php

namespace App\Repository;

use App\Entity\Sav;
use App\Entity\Source;
use App\Entity\StatusSetting;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sav|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sav|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sav[]    findAll()
 * @method Sav[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SavRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sav::class);
    }

    /** @throws NonUniqueResultException */
    public function findIdMax(): int
    {
        $maxId = $this->createQueryBuilder('s')
                ->select('MAX(s.id)')
                ->getQuery()
                ->getSingleScalarResult();

        return null !== $maxId ? (int)$maxId : 0;
    }

    /** @throws NonUniqueResultException */
    public function findRmaMax(): string
    {
        $maxRma = $this->createQueryBuilder('s')
            ->select('MAX(s.rmaCode)')
            ->getQuery()
            ->getSingleScalarResult();

        return null !== $maxRma ? (string)$maxRma : '0';
    }

    public function findSavWithSourceSettingAndDate(DateTime $start, DateTime $end, Source $source, StatusSetting $statusSetting)
    {
        return $this->createQueryBuilder('s')
            ->where('s.source = :source')
            ->andWhere('s.statusSetting = :statusSetting')
            ->andWhere("DATE_FORMAT(s.createdAt, '%Y-%m-%d') >= :lastMonth")
            ->andWhere("DATE_FORMAT(s.createdAt, '%Y-%m-%d') <= :now")
            ->setParameter('source', $source)
            ->setParameter('statusSetting', $statusSetting)
            ->setParameter('lastMonth', $start->format('Y-m-d'))
            ->setParameter('now', $end->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function searchSav(string $q)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.customer', 'c')
            ->leftJoin('s.user', 'u')
            ->leftJoin('s.source', 'so')
            ->leftJoin('s.dealer', 'd')
            ->leftJoin('s.statusSetting', 'ss')
            ->leftJoin('s.savArticles', 'sa')
            ->leftJoin('sa.article', 'a')
            ->leftJoin('s.replacementArticles', 'ra')
            ->where('s.id LIKE UPPER(:q)')
            ->orWhere('s.description LIKE UPPER(:q)')
            ->orWhere('s.serialNumberCustomer LIKE UPPER(:q)')
            ->orWhere('s.comment LIKE UPPER(:q)')
            ->orWhere('s.clientType LIKE UPPER(:q)')
            ->orWhere('s.store LIKE UPPER(:q)')
            ->orWhere('s.dealerReference LIKE UPPER(:q)')
            ->orWhere('s.divaltoNumber LIKE UPPER(:q)')
            ->orWhere('s.carrierCode LIKE UPPER(:q)')
            ->orWhere('s.customerPrinter LIKE UPPER(:q)')
            ->orWhere('s.jiraLink LIKE UPPER(:q)')
            ->orWhere('s.secretCode LIKE UPPER(:q)')
            ->orWhere('s.family LIKE UPPER(:q)')
            ->orWhere('c.name LIKE UPPER(:q)')
            ->orWhere('c.email LIKE UPPER(:q)')
            ->orWhere('c.phoneNumber LIKE UPPER(:q)')
            ->orWhere('c.customerContact LIKE UPPER(:q)')
            ->orWhere('c.postalCode LIKE UPPER(:q)')
            ->orWhere('c.city LIKE UPPER(:q)')
            ->orWhere('u.email LIKE UPPER(:q)')
            ->orWhere('u.username LIKE UPPER(:q)')
            ->orWhere('u.username LIKE UPPER(:q)')
            ->orWhere('so.name LIKE UPPER(:q)')
            ->orWhere('d.email LIKE UPPER(:q)')
            ->orWhere('d.name LIKE UPPER(:q)')
            ->orWhere('d.dealerCode LIKE UPPER(:q)')
            ->orWhere('ss.setting LIKE UPPER(:q)')
            ->orWhere('sa.serialNumber LIKE UPPER(:q)')
            ->orWhere('sa.serialNumber2 LIKE UPPER(:q)')
            ->orWhere('a.reference LIKE UPPER(:q)')
            ->orWhere('a.designation LIKE UPPER(:q)')
            ->orWhere('ra.reference LIKE UPPER(:q)')
            ->orWhere('ra.designation LIKE UPPER(:q)')
            ->setParameter('q', '%' . strtoupper($q) . '%')
            ->orderBy('s.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }


    // /**
    //  * @return Sav[] Returns an array of Sav objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sav
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
