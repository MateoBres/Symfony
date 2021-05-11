<?php

namespace App\Form\ProfessoreFlock;

use App\Entity\AlumnoFlock\Alumno;
use App\Entity\ProfessoreFlock\Professore;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfessoreType extends AdminAbstractType
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
            ->add('createdAt', null, [
                'label'=> $this->getLabel('createdAt')
            ])
            ->add('updatedAt', null, [
                'label'=> $this->getLabel('updatedAt')
            ])
            ->add('alunni', EntityType::class, [
                'label'=> $this->getLabel('alunni'),
                'class' => Alumno::class,
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false
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
            'data_class' => Professore::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
