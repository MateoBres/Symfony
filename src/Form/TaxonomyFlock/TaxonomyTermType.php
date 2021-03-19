<?php

namespace App\Form\TaxonomyFlock;

use App\Entity\TaxonomyFlock\TaxonomyTerm;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxonomyTermType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('term', null, [
                'label'=> $this->getLabel('term')
            ])
            ->add('machineTerm', null, [
                'label'=> $this->getLabel('machineTerm')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => TaxonomyTerm::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
