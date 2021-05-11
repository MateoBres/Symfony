<?php

namespace App\Controller\ProfessorFlock;

use App\Entity\ProfessorFlock\Professor;
use App\Form\ProfessorFlock\ProfessorType;
use App\Filter\ProfessorFlock\ProfessorFilterType;
use App\Repository\ProfessorFlock\ProfessorRepository;
use App\Controller\AdminFlock\AdminController;
use App\Security\AdminFlock\Authorization\Voter\AdminVoter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/professor_flock_professor", name="professor_flock_professor")
 */
class ProfessorController extends AdminController
{
    protected $flock_name   = 'ProfessorFlock';
    protected $entity_namespace = '';
    protected $entity_name   = 'Professor';
    protected $templates_path = 'professor_flock/professor';

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Professor::class);
    }


    protected function tweakRenderVariables(Array &$renderVars, $operation): void
    {
        $renderVars['prova'] = 'Pippo superstar';
        $renderVars['prova1'] = 'Pippo superstar1';
    }


    protected function createQuery()
    {
        $em = $this->getDoctrine()->getManager();

        $fullEntityNamespace = $this->getFullEntityNamespace();

        $query = $em
            ->getRepository($fullEntityNamespace)
            ->createQueryBuilder($this->getQueryMainAliasName())
//            ->leftJoin('Professor.students', 'Student')
//            ->addSelect('Student')
            ->orderBy("{$this->getQueryMainAliasName()}.id", 'ASC');

        return $query;
    }

    protected function getNewEntity()
    {
        return new Professor();
    }

    protected function getNewEntityType()
    {
        return ProfessorType::class;
    }

    protected function getNewEntityFilterType()
    {
        return ProfessorFilterType::class;
    }
}
