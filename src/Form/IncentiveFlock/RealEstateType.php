<?php

namespace App\Form\IncentiveFlock;

use App\Entity\IncentiveFlock\Intervention;
use App\Entity\IncentiveFlock\RealEstate;
use App\Form\TicketFlock\TicketType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RealEstateType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('municipality', null, [
                'label'=> $this->getLabel('municipality')
            ])
            ->add('section', null, [
                'label'=> $this->getLabel('section')
            ])
            ->add('paper', null, [
                'label'=> $this->getLabel('paper')
            ])
            ->add('particle', null, [
                'label'=> $this->getLabel('particle')
            ])
//            ->add('ticket', TicketType::class, [
//                'label'=> $this->getLabel('ticket.id')
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => RealEstate::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
