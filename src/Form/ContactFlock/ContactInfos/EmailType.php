<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 10/11/14
 * Time: 11.41
 */

namespace App\Form\ContactFlock\ContactInfos;

use App\Form\ContactFlock\ContactInfoType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EmailType extends ContactInfoType
{
    protected $dataClass = 'App\\Entity\\ContactFlock\\ContactInfos\\Email';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', HiddenType::class, array(
                'data' => 'email',
            ))
            ->add('value', TextType::class, array(
                'label' => false,
                'required' => false,
                'attr' => array(
                    'widget_icon' => 'fas fa-envelope',
                    'placeholder' => 'Email *',
                    'class' => 'email-field value-field'
                ),
            ));
    }

    public function getBlockPrefix()
    {
        return 'sinervis_contactBundle_contactInfo_Email';
    }
} 