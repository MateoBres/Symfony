<?php

namespace App\Form\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Intervention;
use App\Entity\IncentiveFlock\Stages\ContactWithCustomer;
use App\Entity\IncentiveFlock\Stages\DataCollection;
use App\Form\IncentiveFlock\Documents\DocumentType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataCollectionType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('condominiumDataCollectionForm', DocumentType::class, [
                'label'=> 'Scheda raccolta dati condominio',
                'attr' => ['class' => 'incentive-stage-model'],
                'required' => false
            ])
            ->add('singleFamilyDataCollectionForm', DocumentType::class, [
                'label'=> 'Scheda raccolta dati edificio unifamiliare',
                'attr' => ['class' => 'incentive-stage-model'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => DataCollection::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
