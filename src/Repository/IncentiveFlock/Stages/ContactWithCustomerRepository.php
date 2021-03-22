<?php

namespace App\Repository\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Stages\ContactWithCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactWithCustomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactWithCustomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactWithCustomer[]    findAll()
 * @method ContactWithCustomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactWithCustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactWithCustomer::class);
    }

    // /**
    //  * @return ContactWithCustomer[] Returns an array of ContactWithCustomer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContactWithCustomer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
