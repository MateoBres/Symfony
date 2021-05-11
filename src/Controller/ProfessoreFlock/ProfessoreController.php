<?php

namespace App\Controller\ProfessoreFlock;

use App\Entity\AlumnoFlock\Alumno;
use App\Entity\ProfessoreFlock\Professore;
use App\Form\ProfessoreFlock\ProfessoreType;
use App\Filter\ProfessoreFlock\ProfessoreFilterType;
use App\Repository\ProfessoreFlock\ProfessoreRepository;
use App\Controller\AdminFlock\AdminController;
use App\Security\AdminFlock\Authorization\Voter\AdminVoter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/professore_flock_professore", name="professore_flock_professore")
 */
class ProfessoreController extends AdminController
{
    protected $flock_name   = 'ProfessoreFlock';
    protected $entity_namespace = '';
    protected $entity_name   = 'Professore';
    protected $templates_path = 'professore_flock/professore';

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Professore::class);
    }

    protected function createQuery()
    {
        $em = $this->getDoctrine()->getManager();

        $fullEntityNamespace = $this->getFullEntityNamespace();

        $query = $em
            ->getRepository($fullEntityNamespace)
            ->createQueryBuilder($this->getQueryMainAliasName())
            ->leftJoin('Professore.alunni', 'Alumno')
            ->addSelect('Alumno')
            ->orderBy("{$this->getQueryMainAliasName()}.id", 'DESC');
        //dd($this->getQueryMainAliasName());
        return $query;
    }

    protected function getNewEntity()
    {
        return new Professore();
    }

    protected function getNewEntityType()
    {
        return ProfessoreType::class;
    }

    protected function getNewEntityFilterType()
    {
        return ProfessoreFilterType::class;
    }
}
