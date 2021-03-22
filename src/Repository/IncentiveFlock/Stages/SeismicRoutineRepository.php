<?php

namespace App\Repository\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Stages\SeismicRoutine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SeismicRoutine|null find($id, $lockMode = null, $lockVersion = null)
 * @method SeismicRoutine|null findOneBy(array $criteria, array $orderBy = null)
 * @method SeismicRoutine[]    findAll()
 * @method SeismicRoutine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeismicRoutineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeismicRoutine::class);
    }

    // /**
    //  * @return SeismicRoutine[] Returns an array of SeismicRoutine objects
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
    public function findOneBySomeField($value): ?SeismicRoutine
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
