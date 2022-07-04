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
            ->where('s.id LIKE :q')
            ->orWhere('s.description LIKE :q')
            ->orWhere('s.serialNumberCustomer LIKE :q')
            ->orWhere('s.comment LIKE :q')
            ->orWhere('s.clientType LIKE :q')
            ->orWhere('s.store LIKE :q')
            ->orWhere('s.dealerReference LIKE :q')
            ->orWhere('s.divaltoNumber LIKE :q')
            ->orWhere('s.carrierCode LIKE :q')
            ->orWhere('s.customerPrinter LIKE :q')
            ->orWhere('s.jiraLink LIKE :q')
            ->orWhere('s.secretCode LIKE :q')
            ->orWhere('s.family LIKE :q')
            ->orWhere('c.name LIKE :q')
            ->orWhere('c.email LIKE :q')
            ->orWhere('c.phoneNumber LIKE :q')
            ->orWhere('c.customerContact LIKE :q')
            ->orWhere('c.postalCode LIKE :q')
            ->orWhere('c.city LIKE :q')
            ->orWhere('u.email LIKE :q')
            ->orWhere('u.username LIKE :q')
            ->orWhere('u.username LIKE :q')
            ->orWhere('so.name LIKE :q')
            ->orWhere('d.email LIKE :q')
            ->orWhere('d.name LIKE :q')
            ->orWhere('d.dealerCode LIKE :q')
            ->orWhere('ss.setting LIKE :q')
            ->orWhere('sa.serialNumber LIKE :q')
            ->orWhere('sa.serialNumber2 LIKE :q')
            ->orWhere('a.reference LIKE :q')
            ->orWhere('a.designation LIKE :q')
            ->orWhere('ra.reference LIKE :q')
            ->orWhere('ra.designation LIKE :q')
            ->setParameter('q', '%' . $q . '%')
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
