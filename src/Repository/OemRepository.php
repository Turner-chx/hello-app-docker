<?php

namespace App\Repository;

use App\Entity\Oem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Oem|null find($id, $lockMode = null, $lockVersion = null)
 * @method Oem|null findOneBy(array $criteria, array $orderBy = null)
 * @method Oem[]    findAll()
 * @method Oem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Oem::class);
    }

    // /**
    //  * @return Oem[] Returns an array of Oem objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Oem
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
