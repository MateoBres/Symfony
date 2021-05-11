<?php

namespace App\Filter\StudentFlock;

use App\Entity\StudentFlock\Student;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentFilterType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('name', null, [
                'label'=> $this->getLabel('name')
            ])
            ->add('surname', null, [
                'label'=> $this->getLabel('surname')
            ])
            ->add('Cognome_professore', null, [
                'label'=> 'Cognome Professore',
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) { return null; }
                    $filterQuery->getQueryBuilder()
                        ->andWhere("Professor.surname LIKE :professor")
                        ->setParameter('professor', '%'.$values['value'].'%');

                    return $filterQuery;
                },
            ])
            ->add('Materia', null, [
                'label'=> 'Materia',
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) { return null; }
                    $filterQuery->getQueryBuilder()
                        ->andWhere("Professor.teach LIKE :teach")
                        ->setParameter('teach', '%'.$values['value'].'%');

                    return $filterQuery;
                },
            ])
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'csrf_protection'   => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
