<?php

namespace App\Form\AlumnoFlock;

use App\Entity\AlumnoFlock\Alumno;
use App\Entity\ProfessoreFlock\Professore;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlumnoType extends AdminAbstractType
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
            ->add('createdAt', null, [
                'label'=> $this->getLabel('createdAt')
            ])
            ->add('updatedAt', null, [
                'label'=> $this->getLabel('updatedAt')
            ])
            ->add('professori', EntityType::class, [
                'label'=> $this->getLabel('professori'),
                'class' => Professore::class,
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
            'data_class' => Alumno::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
