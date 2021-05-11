<?php

namespace App\Filter\TicketFlock;

use App\Entity\IncentiveFlock\Intervention;
use App\Entity\TicketFlock\Ticket;
use App\Filter\IncentiveFlock\InterventionFilterType;
use App\Form\IncentiveFlock\InterventionType;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\EntityFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TicketFilterType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        //dd($builder, $options);
        $builder
            ->add('tag', null, [
                'label'=> $this->getLabel('tag')
            ])
            ->add('series', null, [
                'label'=> $this->getLabel('series')
            ])
            ->add('relatedInterventions', null, array(
                'label'=> $this->getLabel('interventions'),
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) { return null; }
                    $filterQuery->getQueryBuilder()
                        ->andWhere("intervention = :intervention")
                        ->setParameter('intervention',  $values['value']);

                    return $filterQuery;
                },
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            //'data_class' => Ticket::class,
            //'fields_map' => RealEstate::class
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
