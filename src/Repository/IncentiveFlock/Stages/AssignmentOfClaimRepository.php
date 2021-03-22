<?php

namespace App\Repository\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Stages\AssignmentOfClaim;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AssignmentOfClaim|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssignmentOfClaim|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssignmentOfClaim[]    findAll()
 * @method AssignmentOfClaim[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssignmentOfClaimRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssignmentOfClaim::class);
    }

    // /**
    //  * @return AssignmentOfClaim[] Returns an array of AssignmentOfClaim objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AssignmentOfClaim
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
