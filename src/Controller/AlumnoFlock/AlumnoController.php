<?php

namespace App\Controller\AlumnoFlock;

use App\Entity\AlumnoFlock\Alumno;
use App\Form\AlumnoFlock\AlumnoType;
use App\Filter\AlumnoFlock\AlumnoFilterType;
use App\Repository\AlumnoFlock\AlumnoRepository;
use App\Controller\AdminFlock\AdminController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/alumno_flock_alumno", name="alumno_flock_alumno")
 */
class AlumnoController extends AdminController
{
    protected $flock_name   = 'AlumnoFlock';
    protected $entity_namespace = '';
    protected $entity_name   = 'Alumno';
    protected $templates_path = 'alumno_flock/alumno';

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Alumno::class);
    }

    protected function createQuery()
    {
        $em = $this->getDoctrine()->getManager();

        $fullEntityNamespace = $this->getFullEntityNamespace();

        $query = $em
            ->getRepository($fullEntityNamespace)
            ->createQueryBuilder($this->getQueryMainAliasName())
            ->leftJoin('Alumno.professori', 'Professore')
            ->addSelect('Professore')
            ->orderBy("{$this->getQueryMainAliasName()}.id", 'DESC');
        //dd($this->getQueryMainAliasName());
        return $query;
    }

    protected function getNewEntity()
    {
        return new Alumno();
    }

    protected function getNewEntityType()
    {
        return AlumnoType::class;
    }

    protected function getNewEntityFilterType()
    {
        return AlumnoFilterType::class;
    }
}
