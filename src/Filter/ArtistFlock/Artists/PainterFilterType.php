<?php

namespace App\Filter\ArtistFlock\Artists;

use App\Entity\ArtistFlock\Artists\Painter;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\DateRangeFilterType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PainterFilterType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('firstName', null, [
                'label'=> $this->getLabel('firstName'),
                'required'=>false
            ])
            ->add('lastName', null, [
                'label'=> $this->getLabel('lastName'),
                'required'=>false
            ])
            ->add('speciality', null, [
                'label'=> $this->getLabel('speciality'),
                'required'=>false
            ])
            ->add('tecnic', null, [
                'label'=> $this->getLabel('tecnic'),
                'required'=>false
            ])
            ->add('createdAt', DateRangeFilterType::class, array(
                'label' => $this->getLabel('createdAt'),
                'left_date_options' => array('widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'html5' => false),
                'right_date_options' => array('widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'html5' => false),
                'attr' => [
                    'field_size' => '6',
                    'class' => 'datepicker'
                ]
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
