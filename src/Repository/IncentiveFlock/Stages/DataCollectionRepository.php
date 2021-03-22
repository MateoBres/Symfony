<?php

namespace App\Repository\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Stages\DataCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DataCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataCollection[]    findAll()
 * @method DataCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataCollection::class);
    }

    // /**
    //  * @return DataCollection[] Returns an array of DataCollection objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DataCollection
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
