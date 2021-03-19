<?php

namespace App\Repository\ContactFlock;

use App\Entity\ContactFlock\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ContactRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactRepository[]    findAll()
 * @method ContactRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
     * @param string $term
     * @param bool $filterMode
     * @return Array[] Returns an array of tutors
     */
    public function findByTermForAutocompletion(string $term, bool $filterMode = false): array
    {
        return $this->createQueryBuilder('co')
            ->andWhere('co.cleanFullName LIKE :term')
            ->setParameter('term', "%".$term."%")
            ->select('co.id, co.cleanFullName as value')
            ->orderBy('co.cleanFullName', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getScalarResult()
            ;
    }
}
