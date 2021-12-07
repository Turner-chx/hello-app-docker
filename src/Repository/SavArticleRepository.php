<?php

namespace App\Repository;

use App\Entity\SavArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SavArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method SavArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method SavArticle[]    findAll()
 * @method SavArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SavArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SavArticle::class);
    }

    // /**
    //  * @return SavArticle[] Returns an array of SavArticle objects
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
    public function findOneBySomeField($value): ?SavArticle
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
