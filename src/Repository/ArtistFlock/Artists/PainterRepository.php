<?php

namespace App\Repository\ArtistFlock\Artists;

use App\Entity\ArtistFlock\Artists\Painter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Painter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Painter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Painter[]    findAll()
 * @method Painter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PainterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Painter::class);
    }

    // /**
    //  * @return Painter[] Returns an array of Painter objects
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
    public function findOneBySomeField($value): ?Painter
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
