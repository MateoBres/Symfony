<?php

namespace App\Form\TicketFlock;

use App\Entity\IncentiveFlock\Intervention;
use App\Entity\TicketFlock\Ticket;
use App\Form\IncentiveFlock\InterventionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('tag', null, [
                'label'=> $this->getLabel('tag')
            ])
            ->add('series', null, [
                'label'=> $this->getLabel('series')
            ])
            ->add('interventions', EntityType::class, [
                'class' => Intervention::class,
                'multiple' => true,
                'label'=> $this->getLabel('interventions'),
                'by_reference' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
