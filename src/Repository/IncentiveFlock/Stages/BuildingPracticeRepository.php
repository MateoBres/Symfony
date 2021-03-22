<?php

namespace App\Repository\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Stages\BuildingPractice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BuildingPractice|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuildingPractice|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuildingPractice[]    findAll()
 * @method BuildingPractice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuildingPracticeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuildingPractice::class);
    }

    // /**
    //  * @return BuildingPractice[] Returns an array of BuildingPractice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BuildingPractice
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
