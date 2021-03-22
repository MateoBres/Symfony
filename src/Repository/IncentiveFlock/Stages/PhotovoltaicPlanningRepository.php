<?php

namespace App\Repository\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Stages\PhotovoltaicPlanning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PhotovoltaicPlanning|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhotovoltaicPlanning|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhotovoltaicPlanning[]    findAll()
 * @method PhotovoltaicPlanning[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotovoltaicPlanningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhotovoltaicPlanning::class);
    }

    // /**
    //  * @return PhotovoltaicPlanning[] Returns an array of PhotovoltaicPlanning objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PhotovoltaicPlanning
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
