<?php

namespace App\Repository;

use App\Entity\NatureSetting;
use App\Entity\Source;
use App\Entity\Brand;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NatureSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method NatureSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method NatureSetting[]    findAll()
 * @method NatureSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NatureSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NatureSetting::class);
    }

    public static function getNatureSettingBySubProduct(EntityRepository $er): QueryBuilder
    {
        return $er->createQueryBuilder('ns')
            ->select('ns')
            ->innerJoin('ns.subProductType', 'spt')
            ->innerJoin('spt.articles', 'a')
            ->where('ns.status = true')
            ->andWhere('spt.status = true')
            ->groupBy('ns.id');
    }

    public function findNaturesWithSource(Source $source)
    {
        return $this->createQueryBuilder('ns')
            ->join('ns.source', 's')
            ->where('s.id = :source')
            ->setParameter('source', $source->getId())
            ->getQuery()
            ->getResult();
    }

    public function findNaturesWithSubProductType(Brand $subProductType)
    {
        return $this->createQueryBuilder('ns')
            ->join('ns.subProductType', 's')
            ->where('s.id = :subProductType')
            ->setParameter('subProductType', $subProductType->getId())
            ->getQuery()
            ->getResult();
    }

    public function findNatureSavWithDate(DateTime $start, DateTime $end)
    {
        return $this->createQueryBuilder('n')
            ->innerJoin('n.savs', 's')
            ->where("DATE_FORMAT(s.createdAt, '%Y-%m-%d') >= :lastMonth")
            ->andWhere("DATE_FORMAT(s.createdAt, '%Y-%m-%d') <= :now")
            ->setParameter('lastMonth', $start->format('Y-m-d'))
            ->setParameter('now', $end->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return NatureSetting[] Returns an array of NatureSetting objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NatureSetting
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
