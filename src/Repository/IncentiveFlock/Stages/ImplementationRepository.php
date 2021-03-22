<?php

namespace App\Repository\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Stages\Implementation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Implementation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Implementation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Implementation[]    findAll()
 * @method Implementation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImplementationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Implementation::class);
    }

    // /**
    //  * @return Implementation[] Returns an array of Implementation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Implementation
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
