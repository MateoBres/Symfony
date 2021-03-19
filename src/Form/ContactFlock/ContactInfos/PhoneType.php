<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 10/11/14
 * Time: 11.42
 */

namespace App\Form\ContactFlock\ContactInfos;


use App\Form\ContactFlock\ContactInfoType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PhoneType extends ContactInfoType
{
    protected $dataClass = 'App\\Entity\\ContactFlock\\ContactInfos\\Phone';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', ChoiceType::class, array(
                'label' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Fisso' => 'fisso',
                    'Cell' => 'cellulare',
                    'Fax' => 'fax',
                ),
                'attr' => array('class' => 'inline-group phone-type-field'),
            ))
            ->add('value', TextType::class, array(
                'label' => false,
                'required' => false,
                'attr' => array(
                    'widget_icon' => 'fa fa-phone',
                    'placeholder' => 'Telefono *',
                    'class' => 'phone-field value-field'
                ),
            ));
    }

    public function getBlockPrefix()
    {
        return 'sinervis_contactBundle_contactInfo_Telefono';
    }
} 