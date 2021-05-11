<?php

namespace App\Filter\ProfessorFlock;

use App\Entity\ProfessorFlock\Professor;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfessorFilterType extends AdminAbstractType
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
            ->add('teach', null, [
                'label'=> $this->getLabel('teach')
            ])
            ->add('alunni', null, [
                'label'=> 'Cognome alunno',
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) { return null; }
                    $filterQuery->getQueryBuilder()
                        ->andWhere("Student.surname LIKE :Student")
                        ->setParameter('Student', '%'.$values['value'].'%');

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
