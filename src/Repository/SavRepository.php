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
            ->where('s.id LIKE LOWER(:q)')
            ->orWhere('s.description LIKE LOWER(:q)')
            ->orWhere('s.serialNumberCustomer LIKE LOWER(:q)')
            ->orWhere('s.comment LIKE LOWER(:q)')
            ->orWhere('s.clientType LIKE LOWER(:q)')
            ->orWhere('s.store LIKE LOWER(:q)')
            ->orWhere('s.dealerReference LIKE LOWER(:q)')
            ->orWhere('s.divaltoNumber LIKE LOWER(:q)')
            ->orWhere('s.carrierCode LIKE LOWER(:q)')
            ->orWhere('s.customerPrinter LIKE LOWER(:q)')
            ->orWhere('s.jiraLink LIKE LOWER(:q)')
            ->orWhere('s.secretCode LIKE LOWER(:q)')
            ->orWhere('s.family LIKE LOWER(:q)')
            ->orWhere('c.name LIKE LOWER(:q)')
            ->orWhere('c.email LIKE LOWER(:q)')
            ->orWhere('c.phoneNumber LIKE LOWER(:q)')
            ->orWhere('c.customerContact LIKE LOWER(:q)')
            ->orWhere('c.postalCode LIKE LOWER(:q)')
            ->orWhere('c.city LIKE LOWER(:q)')
            ->orWhere('u.email LIKE LOWER(:q)')
            ->orWhere('u.username LIKE LOWER(:q)')
            ->orWhere('u.username LIKE LOWER(:q)')
            ->orWhere('so.name LIKE LOWER(:q)')
            ->orWhere('d.email LIKE LOWER(:q)')
            ->orWhere('d.name LIKE LOWER(:q)')
            ->orWhere('d.dealerCode LIKE LOWER(:q)')
            ->orWhere('ss.setting LIKE LOWER(:q)')
            ->orWhere('sa.serialNumber LIKE LOWER(:q)')
            ->orWhere('sa.serialNumber2 LIKE LOWER(:q)')
            ->orWhere('a.reference LIKE LOWER(:q)')
            ->orWhere('a.designation LIKE LOWER(:q)')
            ->orWhere('ra.reference LIKE LOWER(:q)')
            ->orWhere('ra.designation LIKE LOWER(:q)')
            ->setParameter('q', '%' . strtolower($q) . '%')
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
