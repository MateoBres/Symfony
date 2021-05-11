<?php

namespace App\Form\StudentFlock;

use App\Entity\ProfessorFlock\Professor;
use App\Entity\StudentFlock\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AdminAbstractType
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
            ->add('createdAt', null, [
                'label'=> $this->getLabel('createdAt')
            ])
            ->add('updatedAt', null, [
                'label'=> $this->getLabel('updatedAt')
            ])
            ->add('professors', EntityType::class, [
                'label'=> $this->getLabel('professors'),
                'class' => Professor::class,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('createdBy', null, [
                'label'=> $this->getLabel('createdBy')
            ])
            ->add('updatedBy', null, [
                'label'=> $this->getLabel('updatedBy')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
