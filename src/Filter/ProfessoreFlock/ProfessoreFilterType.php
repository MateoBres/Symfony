<?php

namespace App\Filter\ProfessoreFlock;

use App\Entity\ProfessoreFlock\Professore;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfessoreFilterType extends AdminAbstractType
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
            ->add('materia', null, [
                'label'=> $this->getLabel('materia')
            ])
            ->add('alunni', null, [
                'label'=> $this->getLabel('alunni'),
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) { return null; }
                    $filterQuery->getQueryBuilder()
                        ->andWhere("Alumno.cognome LIKE :intervention")
                        ->setParameter('intervention', '%'.$values['value'].'%');

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
