<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 06/11/14
 * Time: 10.28
 */

namespace App\Form\ContactFlock;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactInfoType extends AbstractType
{
    protected $dataClass = 'App\\Entity\\ContactInfo';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value');

        $builder->add('_type', HiddenType::class, array(
            'data' => $this->getBlockPrefix(),
            'mapped' => false
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'model_class' => $this->dataClass,
            'cascade_validation' => true,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'sinervis_contactbundle_contact_info';
    }
} 