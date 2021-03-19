<?php

namespace App\Repository\TaxonomyFlock;

use App\Entity\TaxonomyFlock\TaxonomyTerm;
use App\Repository\AdminFlock\AdminRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method TaxonomyTerm|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaxonomyTerm|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaxonomyTerm[]    findAll()
 * @method TaxonomyTerm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxonomyTermRepository extends ServiceEntityRepository implements AdminRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaxonomyTerm::class);
    }

    public function getNumberOfTermsForVocabulary(TaxonomyTerm $vocabulary)
    {
        $expr = new Expr();

        return $this->createQueryBuilder('tt')
            ->andWhere($expr->isInstanceOf('tt', get_class($vocabulary)))
            ->select('COUNT(tt.id) as fortunesPrinted')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @inheritDoc
     */
    public function findByTermForAutocompletion(string $term, bool $filterMode = false): array
    {
        // TODO: Implement findByTermForAutocompletion() method.
    }
}
