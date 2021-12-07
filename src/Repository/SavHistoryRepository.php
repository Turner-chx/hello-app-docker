<?php

namespace App\Repository;

use App\Entity\SavHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SavHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method SavHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method SavHistory[]    findAll()
 * @method SavHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SavHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SavHistory::class);
    }

    // /**
    //  * @return SavHistory[] Returns an array of SavHistory objects
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
    public function findOneBySomeField($value): ?SavHistory
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
