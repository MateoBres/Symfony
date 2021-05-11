<?php

namespace App\Filter\AlumnoFlock;

use App\Entity\AlumnoFlock\Alumno;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlumnoFilterType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('nome', null, [
                'label'=> $this->getLabel('nome')
            ])
            ->add('cognome', null, [
                'label'=> $this->getLabel('cognome')
            ])
            ->add('Cognome_professore', null, [
                'label'=> 'Cognome Professore',
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) { return null; }
                    $filterQuery->getQueryBuilder()
                        ->andWhere("Professore.cognome LIKE :professore")
                        ->setParameter('professore', '%'.$values['value'].'%');

                    return $filterQuery;
                },
            ])
            ->add('Materia', null, [
                'label'=> 'Materia',
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) { return null; }
                    $filterQuery->getQueryBuilder()
                        ->andWhere("Professore.materia LIKE :materia")
                        ->setParameter('materia', '%'.$values['value'].'%');

                    return $filterQuery;
                },
            ])
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
