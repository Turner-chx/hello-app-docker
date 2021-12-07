<?php

namespace App\Repository;

use App\Entity\MessagingTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MessagingTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessagingTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessagingTemplate[]    findAll()
 * @method MessagingTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagingTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessagingTemplate::class);
    }

    // /**
    //  * @return MessagingTemplate[] Returns an array of MessagingTemplate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MessagingTemplate
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
