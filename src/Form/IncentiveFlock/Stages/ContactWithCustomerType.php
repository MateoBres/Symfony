<?php

namespace App\Form\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Intervention;
use App\Entity\IncentiveFlock\Stages\ContactWithCustomer;
use App\Form\IncentiveFlock\Documents\DocumentType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactWithCustomerType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('estimate', DocumentType::class, [
                'label'=> 'Preventivo',
                'attr' => ['class' => 'incentive-stage-model'],
                'required' => false
            ])
            ->add('dutyAssignment', DocumentType::class, [
                'label'=> 'Disciplinare di incarico',
                'attr' => ['class' => 'incentive-stage-model'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => ContactWithCustomer::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
