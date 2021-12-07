<?php

namespace App\Repository;

use App\Entity\StatusSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StatusSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatusSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatusSetting[]    findAll()
 * @method StatusSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatusSetting::class);
    }

    public function findDefault(EntityRepository $er): QueryBuilder
    {
        $qb = $er->createQueryBuilder('ss')
            ->select('ss')
            ->orderBy('ss.byDefault', 'DESC');
        return $qb;
    }

    // /**
    //  * @return StatusSetting[] Returns an array of StatusSetting objects
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
    public function findOneBySomeField($value): ?StatusSetting
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
