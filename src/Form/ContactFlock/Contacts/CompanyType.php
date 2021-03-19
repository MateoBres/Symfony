<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 09/12/14
 * Time: 11.41
 */

namespace App\Form\ContactFlock\Contacts;

use App\Form\ContactFlock\ContactType;
use App\Form\ContactFlock\Places\OfficeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends ContactType
{
    protected $dataClass = 'App\\Entity\\ContactFlock\\Contacts\\Company';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('businessName', TextType::class, array(
                'label' => $this->getLabel('businessName'),
                'attr' => ['class' => 'businessname']
            ))
            ->add('vatId', TextType::class, array(
                'label' => $this->getLabel('vatId'),
                'required' => false,
            ))
            ->add('taxCode', TextType::class, array(
                'label' => $this->getLabel('taxCode'),
                'required' => false,
            ))
            ->add('ownedPlaces', CollectionType::class, array(
                'label' => $this->getLabel('CompanyOwnedPlaces'),
                //            'type'=>new OfficeType(array('fields_map'=>$this->getFieldsMap('CompanyOwnedPlaces'),'add_type_fields'=>true)),
                'entry_type' => OfficeType::class,
                'entry_options' => array(
                    'add_more_button_label' => 'Sede',
                    'fields_map' => $this->getFieldsMap('companyOwnedPlaces'),
                    '_add_type_fields' => true,
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => array(
                    'add_more_button_label' => 'Sede',
                    'fields_map' => $this->getFieldsMap('companyOwnedPlaces'),
                )
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ContactFlock\Contacts\Company',
            'cascade_validation' => true,
            'fields_map' => null,
            'security_context' => null,
            'additional_fields' => null,
            'origin' => null,
            'request_obj' => null,
            'disable_type' => null,
            'add_representatives_field' => true,
            'authorization_checker' => null,
            'hide_contact_type_field' => true,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'sinervis_contactbundle_contact_company';
    }
} 
