<?php

namespace App\Form\ContactFlock\ContactInfos;

use App\Form\ContactFlock\ContactInfoType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class WebsiteType extends ContactInfoType
{
    protected $dataClass = 'App\\Entity\\ContactFlock\\ContactInfos\\Website';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', HiddenType::class, array(
                'data' => 'website',
            ))
            ->add('value', UrlType::class, array(
                'label' => false,
                'required' => false,
                'attr' => array(
                    'widget_icon' => 'fas fa-link',
                    'placeholder' => 'Website',
                    'class' => 'website-field value-field'
                ),
            ));
    }

    public function getBlockPrefix()
    {
        return 'sinervis_contactBundle_contactInfo_Website';
    }
} 