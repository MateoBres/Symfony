<?php

namespace App\Repository\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Stages\ThermotechnicPlanning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ThermotechnicPlanning|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThermotechnicPlanning|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThermotechnicPlanning[]    findAll()
 * @method ThermotechnicPlanning[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThermotechnicPlanningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThermotechnicPlanning::class);
    }

    // /**
    //  * @return ThermotechnicPlanning[] Returns an array of ThermotechnicPlanning objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ThermotechnicPlanning
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
