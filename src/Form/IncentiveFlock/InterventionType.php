<?php

namespace App\Form\IncentiveFlock;

use App\Entity\IncentiveFlock\Intervention;
use App\Entity\IncentiveFlock\RealEstate;
use App\Entity\TicketFlock\Ticket;
use App\Form\IncentiveFlock\Stages\ContactWithCustomerType;
use App\Form\IncentiveFlock\Stages\DataCollectionType;
use App\Form\TicketFlock\TicketType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('realEstate', RealEstateType::class, [
                'label'=> $this->getLabel('realEstate'),
                'fields_map' => $this->getFieldsMap()
            ])
            ->add('contactWithCustomer', ContactWithCustomerType::class, [
                'label'=> $this->getLabel('contactWithCustomer'),
                'attr' => ['class' => 'incentive-stage'],
                'required' => false
            ])
            ->add('dataCollection', DataCollectionType::class, [
                'label'=> $this->getLabel('dataCollection'),
                'attr' => ['class' => 'incentive-stage'],
                'required' => false
            ])
//            ->add('ticket', TicketType::class, [
//                'label'=> $this->getLabel('ticket'),
//                'attr' => ['class' => 'incentive-stage'],
//                'required' => false
//            ])
            ->add('ticket', EntityType::class, [
                'label'=> $this->getLabel('ticket'),
                'required' => true,
                'class' => Ticket::class,
                'choice_label' => 'tag',
                'expanded' => false,
                'multiple' => false
            ])
        ;
            //dd($builder)
//
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => Intervention::class,
            //'fields_map' => RealEstate::class
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
