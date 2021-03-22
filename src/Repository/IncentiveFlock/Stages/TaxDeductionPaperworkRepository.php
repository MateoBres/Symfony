<?php

namespace App\Repository\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Stages\TaxDeductionPaperwork;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaxDeductionPaperwork|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaxDeductionPaperwork|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaxDeductionPaperwork[]    findAll()
 * @method TaxDeductionPaperwork[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxDeductionPaperworkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaxDeductionPaperwork::class);
    }

    // /**
    //  * @return TaxDeductionPaperwork[] Returns an array of TaxDeductionPaperwork objects
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
    public function findOneBySomeField($value): ?TaxDeductionPaperwork
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
