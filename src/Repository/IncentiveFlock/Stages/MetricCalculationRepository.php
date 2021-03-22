<?php

namespace App\Repository\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Stages\MetricCalculation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MetricCalculation|null find($id, $lockMode = null, $lockVersion = null)
 * @method MetricCalculation|null findOneBy(array $criteria, array $orderBy = null)
 * @method MetricCalculation[]    findAll()
 * @method MetricCalculation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MetricCalculationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MetricCalculation::class);
    }

    // /**
    //  * @return MetricCalculation[] Returns an array of MetricCalculation objects
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
    public function findOneBySomeField($value): ?MetricCalculation
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
