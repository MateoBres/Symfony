<?php

namespace App\Repository\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Stages\FiscalAssertion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FiscalAssertion|null find($id, $lockMode = null, $lockVersion = null)
 * @method FiscalAssertion|null findOneBy(array $criteria, array $orderBy = null)
 * @method FiscalAssertion[]    findAll()
 * @method FiscalAssertion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FiscalAssertionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FiscalAssertion::class);
    }

    // /**
    //  * @return FiscalAssertion[] Returns an array of FiscalAssertion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FiscalAssertion
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
