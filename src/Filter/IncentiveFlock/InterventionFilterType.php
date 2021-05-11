<?php

namespace App\Filter\IncentiveFlock;

use App\Entity\IncentiveFlock\Intervention;
use App\Entity\IncentiveFlock\RealEstate;
use App\Entity\TicketFlock\Ticket;
use App\Form\IncentiveFlock\RealEstateType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\EntityFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterventionFilterType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        parent::buildForm($builder, $options);
        $builder
            ->add('createdAt', null, [
                'label'=> $this->getLabel('createdAt')
            ])
            ->add('updatedAt', null, [
                'label'=> $this->getLabel('updatedAt')
            ])
            ->add('realEstate', null, [
                'label'=> $this->getLabel('realEstate')
            ])
            ->add('createdBy', null, [
                'label'=> $this->getLabel('createdBy')
            ])
            ->add('updatedBy', null, [
                'label'=> $this->getLabel('updatedBy')
            ])
            ->add('municipality', null, array(
                'label'=> $this->getLabel('municipality'),
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) {
                        return null;
                    }
                    $filterQuery->getQueryBuilder()
                        ->andWhere("realEstate.municipality LIKE :municipality")
                        ->setParameter('municipality', '%' . $values['value'] . '%');

                    return $filterQuery;
                },
            ))
            ->add('section', null, array(
                'label'=> $this->getLabel('section'),
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) {
                        return null;
                    }
                    $filterQuery->getQueryBuilder()
                        ->andWhere("realEstate.section LIKE :section")
                        ->setParameter('section', '%' . $values['value'] . '%');

                    return $filterQuery;
                },
            ))
            ->add('paper', null, array(
                'label'=> $this->getLabel('paper'),
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) {
                        return null;
                    }
                    $filterQuery->getQueryBuilder()
                        ->andWhere("realEstate.paper LIKE :paper")
                        ->setParameter('paper',  '%' . $values['value'] . '%');

                    return $filterQuery;
                },
            ))
            ->add('particle', null, array(
                'label'=> $this->getLabel('particle'),
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) {
                        return null;
                    }
                    $filterQuery->getQueryBuilder()
                        ->andWhere("realEstate.particle LIKE :particle")
                        ->setParameter('particle', '%' . $values['value'] . '%');
                    return $filterQuery;
                },
            ))
            ->add('ticket', EntityFilterType::class, array(
                'label'=> $this->getLabel('ticket'),
                'class' => Ticket::class,
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) {
                        return null;
                    }
                    $filterQuery->getQueryBuilder('Intervention')
                        ->leftJoin('Intervention.ticket', 'ticket')
                        ->addSelect('ticket')
                        ->andWhere("ticket.tag = :ticket")
                        ->setParameter('ticket',  $values['value']->getTag());

                    return $filterQuery;
                },
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'csrf_protection'   => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
