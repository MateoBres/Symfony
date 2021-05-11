<?php

namespace App\Form\ProfessorFlock;

use App\Entity\ProfessorFlock\Professor;
use App\Entity\StudentFlock\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfessorType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('name', null, [
                'label'=> $this->getLabel('name'),
                'attr' => [
                    'prefix' => htmlentities("I<ndicare se • Valore pessimo= >30 • Valore scarso 29 <x<=15 • Valore medio 15<x< =7 • Valore buono 7<x<= 5 • Ottimo < 5")
                ]
            ])
            ->add('surname', null, [
                'label'=> $this->getLabel('surname')
            ])
            ->add('teach', null, [
                'label'=> $this->getLabel('teach')
            ])
            ->add('students', EntityType::class, [
                'label'=> $this->getLabel('students'),
                'class' => Student::class,
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => Professor::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
