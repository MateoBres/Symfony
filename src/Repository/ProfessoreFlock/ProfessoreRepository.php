<?php

namespace App\Repository\ProfessoreFlock;

use App\Entity\ProfessoreFlock\Professore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Professore|null find($id, $lockMode = null, $lockVersion = null)
 * @method Professore|null findOneBy(array $criteria, array $orderBy = null)
 * @method Professore[]    findAll()
 * @method Professore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfessoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Professore::class);
    }

    // /**
    //  * @return Professore[] Returns an array of Professore objects
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
    public function findOneBySomeField($value): ?Professore
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
