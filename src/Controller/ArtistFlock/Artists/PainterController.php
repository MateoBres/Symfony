<?php

namespace App\Controller\ArtistFlock\Artists;

use App\Controller\ArtistFlock\ArtistController;
use App\Entity\ArtistFlock\Artists\Painter;
use App\Form\ArtistFlock\Artists\PainterType;
use App\Filter\ArtistFlock\Artists\PainterFilterType;
use App\Repository\ArtistFlock\Artists\PainterRepository;
use App\Controller\AdminFlock\AdminController;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/artist_painter", name="artist_flock_artist_painter")
 */
class PainterController extends ArtistController
{
    protected $flock_name   = 'ArtistFlock';
    protected $entity_namespace = 'Artists';
    protected $entity_name   = 'Painter';
    protected $templates_path = 'artist_flock/Artists/Painter';

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Painter::class);
    }

    protected function createQuery()
    {
        $query = parent::createQuery();

        // add here your join
        //$query->leftJoin('{{ entity_class }}.childEntity','b')->addSelect('b');

        return $query;
//        $em = $this->getDoctrine()->getManager();
//
//        $fullEntityNamespace = $this->getFullEntityNamespace();
//        $query = $em
//            ->getRepository($fullEntityNamespace)
//            ->createQueryBuilder($this->getQueryMainAliasName())
//            ->orderBy("{$this->getQueryMainAliasName()}.id", 'DESC');
//
//        return $query;
    }

    protected function getNewEntity()
    {
        return new Painter();
    }

    protected function getNewEntityType()
    {
        return PainterType::class;
    }

    protected function getNewEntityFilterType()
    {
        return PainterFilterType::class;
    }

    protected function setQuickFilters(QueryBuilder $query, Request $request)
    {
        // quick filter ($_GET['q'])
        if ($search_term = $request->get('q')) {

            $count = 0;
            $whereQuery = $query->expr()->orX();

            foreach ($this->fields_map as $field => $configuration) {

                if (isset($configuration['search_alias'])) {
                    //$conf = str_replace('\\', '_', $configuration['search_alias']);
                    $whereQuery->add($query->expr()->like($configuration['search_alias'], '?1'));

                    $count++;
                }
            }
            if ($count) {
                $query->andWhere($whereQuery);
                $query->setParameter('1', '%' . $search_term . '%');
            }
        }
    }
}
