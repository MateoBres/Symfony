<?php

namespace App\Controller\IncentiveFlock;

use App\Entity\IncentiveFlock\Intervention;
use App\Form\IncentiveFlock\InterventionType;
use App\Filter\IncentiveFlock\InterventionFilterType;
use App\Repository\IncentiveFlock\InterventionRepository;
use App\Controller\AdminFlock\AdminController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/incentive_flock_intervention", name="incentive_flock_intervention")
 */
class InterventionController extends AdminController
{
    protected $flock_name   = 'IncentiveFlock';
    protected $entity_namespace = '';
    protected $entity_name   = 'Intervention';
    protected $templates_path = 'incentive_flock/intervention';

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Intervention::class);
    }

    protected function createQuery()
    {
        $em = $this->getDoctrine()->getManager();

        $fullEntityNamespace = $this->getFullEntityNamespace();

        $query = $em
            ->getRepository($fullEntityNamespace)
            ->createQueryBuilder($this->getQueryMainAliasName())

            ->leftJoin('Intervention.realEstate', 'realEstate')->addSelect('realEstate')
            ->orderBy("{$this->getQueryMainAliasName()}.id", 'DESC');
        //dd($query);
        return $query;




        //$query = parent::createQuery();//array('a', 'b')
        //dd($query);
        // add here your join
        //$query->leftJoin('a.realEstate', 'b')->addSelect('b');
        //$query->leftJoin('{{ entity_class }}.childEntity','b')->addSelect('b');
        //dd($query);
        //return $query;
    }

    protected function getNewEntity()
    {
        return new Intervention();
    }

    protected function getNewEntityType()
    {
        return InterventionType::class;
    }

    protected function getNewEntityFilterType()
    {
        return InterventionFilterType::class;
    }
}
