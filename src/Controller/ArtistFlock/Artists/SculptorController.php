<?php

namespace App\Controller\ArtistFlock\Artists;

use App\Controller\ArtistFlock\ArtistController;
use App\Entity\ArtistFlock\Artists\Sculptor;
use App\Form\ArtistFlock\Artists\SculptorType;
use App\Filter\ArtistFlock\Artists\SculptorFilterType;
use App\Repository\ArtistFlock\Artists\SculptorRepository;
use App\Controller\AdminFlock\AdminController;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/artist_sculptor", name="artist_flock_artist_sculptor")
 */
class SculptorController extends ArtistController
{
    protected $flock_name   = 'ArtistFlock';
    protected $entity_namespace = 'Artists';
    protected $entity_name   = 'Sculptor';
    protected $templates_path = 'artist_flock/Artists/Sculptor';

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Sculptor::class);
    }

    protected function createQuery()
    {
        $query = parent::createQuery();

        // add here your join
        //$query->leftJoin('{{ entity_class }}.childEntity','b')->addSelect('b');

        return $query;
    }

    protected function getNewEntity()
    {
        return new Sculptor();
    }

    protected function getNewEntityType()
    {
        return SculptorType::class;
    }

    protected function getNewEntityFilterType()
    {
        return SculptorFilterType::class;
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
