<?php

namespace App\Filter\UserFlock;

use App\Entity\UserFlock\User;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\BooleanFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type\TextFilterType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilterType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('username', TextFilterType::class, [
                'label'=> $this->getLabel('username'),
                'condition_pattern' => FilterOperands::STRING_CONTAINS,
//                'attr' =>[
//                    'field_size' => '2'
//                ]
            ])
            ->add('email', TextFilterType::class, [
                'label'=> $this->getLabel('email'),
                'condition_pattern' => FilterOperands::STRING_CONTAINS,
//                'attr' =>[
//                    'field_size' => '2'
//                ]
            ])
            ->add('enabled', BooleanFilterType::class, [
                'label'=> $this->getLabel('enabled'),
                'placeholder' => 'Tutti',
                'choices'                => array(
                    'si' => 'y',
                    'no'  => 'n',
                ),
                'attr' =>[
                    'field_size' => '2'
                ]
            ])
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
