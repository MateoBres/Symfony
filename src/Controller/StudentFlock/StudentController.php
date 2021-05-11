<?php

namespace App\Controller\StudentFlock;

use App\Entity\StudentFlock\Student;
use App\Form\StudentFlock\StudentType;
use App\Filter\StudentFlock\StudentFilterType;
use App\Repository\StudentFlock\StudentRepository;
use App\Controller\AdminFlock\AdminController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/student_flock_student", name="student_flock_student")
 */
class StudentController extends AdminController
{
    protected $flock_name   = 'StudentFlock';
    protected $entity_namespace = '';
    protected $entity_name   = 'Student';
    protected $templates_path = 'student_flock/student';

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Student::class);
    }

    protected function createQuery()
    {
        $em = $this->getDoctrine()->getManager();

        $fullEntityNamespace = $this->getFullEntityNamespace();

        $query = $em
            ->getRepository($fullEntityNamespace)
            ->createQueryBuilder($this->getQueryMainAliasName())
            ->leftJoin('Student.professors', 'Professor')
            ->addSelect('Professor')
            ->orderBy("{$this->getQueryMainAliasName()}.id", 'ASC');

        return $query;
    }

    protected function getNewEntity()
    {
        return new Student();
    }

    protected function getNewEntityType()
    {
        return StudentType::class;
    }

    protected function getNewEntityFilterType()
    {
        return StudentFilterType::class;
    }
}
