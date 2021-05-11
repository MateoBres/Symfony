<?php

namespace App\Repository\ArtistFlock\Artists;

use App\Entity\ArtistFlock\Artists\Sculptor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sculptor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sculptor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sculptor[]    findAll()
 * @method Sculptor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SculptorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sculptor::class);
    }

    // /**
    //  * @return Sculptor[] Returns an array of Sculptor objects
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
    public function findOneBySomeField($value): ?Sculptor
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
