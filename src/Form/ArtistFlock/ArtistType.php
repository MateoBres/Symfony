<?php

namespace App\Form\ArtistFlock;

use App\Entity\ArtistFlock\Artist;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArtistType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('firstName', null, [
                'label'=> $this->getLabel('firstName')
            ])
            ->add('lastName', null, [
                'label'=> $this->getLabel('lastName')
            ])
            ->add('speciality', null, [
                'label'=> $this->getLabel('speciality')
            ])
            ->add('createdAt', null, [
                'label'=> $this->getLabel('createdAt')
            ])
            ->add('updatedAt', null, [
                'label'=> $this->getLabel('updatedAt')
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
            'data_class' => Artist::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
