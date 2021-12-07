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
