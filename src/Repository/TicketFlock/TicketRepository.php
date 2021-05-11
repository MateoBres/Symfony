<?php

namespace App\Repository\TicketFlock;

use App\Entity\TicketFlock\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    // /**
    //  * @return Ticket[] Returns an array of Ticket objects
    //  */

//    public function find($id, $lockMode = null, $lockVersion = null)
//    {
//        $query = $this->createQueryBuilder('ticket')
//            ->andWhere('ticket.id = :val')
//            ->setParameter('val', $id)
//            ->orderBy('ticket.id', 'ASC')
//            ->leftJoin('ticket.interventions', 'intervention')
//            ->addSelect('intervention')
//            ->getQuery()
//            ->getResult()
//        ;
////        dd($query);
//        return $query;
//    }


    /*
    public function findOneBySomeField($value): ?Ticket
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
