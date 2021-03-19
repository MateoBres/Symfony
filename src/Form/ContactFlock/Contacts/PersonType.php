<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 09/12/14
 * Time: 11.41
 */

namespace App\Form\ContactFlock\Contacts;

use App\DBAL\Types\PersonGenderType;
use App\DBAL\Types\ProfessionalPositionType;
use App\Entity\ContactFlock\GenericProfession;
use App\Form\AdminFlock\ChoiceOrText\ChoiceOrTextType;
use App\Form\ContactFlock\ContactType;
use App\Form\ContactFlock\PersonEcmProfessionType;
use App\Form\ContactFlock\Places\HouseType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends ContactType
{
    protected $dataClass = 'App\\Entity\\ContactFlock\\Contacts\\Person';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

//        $user = $this->getTokenStorage()->getToken()->getUser() ?? null;

        $builder
            ->add('firstName', TextType::class, array(
                'label' => $this->getLabel('firstName'),
                'required' => true,
                'attr' => array(
                    'class' => 'cfiscale cf-firstname'
                )
            ))
            ->add('lastName', TextType::class, array(
                'label' => $this->getLabel('lastName'),
                'required' => true,
                'attr' => array(
                    'class' => 'cfiscale cf-lastname'
                )
            ))
            ->add('gender', ChoiceType::class, array(
                'choices' => PersonGenderType::getChoices(),
                'label' => $this->getLabel('gender'),
                'expanded' => true,
                'attr' => array('class' => 'inline-group cfiscale cf-gender'),
                'choice_value' => function ($currentChoiceKey) {
                    if (empty($currentChoiceKey)) {
                        return null;
                    }
                    return $currentChoiceKey == 'm' ? 'M' : 'F';
                },
            ))
            ->add('birthDate', DateType::class, array(
                'label' => $this->getLabel('birthDate'),
                'required' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => array(
                    'class' => 'date-of-birth cfiscale cf-dob'
                )
            ))
            ->add('birthCity', TextType::class, array(
//                'class' => null,
                'label' => $this->getLabel('birthCity'),
//                'route_name' => 'utility_geocode',
                'required' => false,
                'attr' => array(
                    'geo-name' => 'formattedAddress',
                    'class' => 'cfiscale cf-city text-type-autocomplete-input',
                    'data-autocomplete-route' => 'utility_geocode'
                )
            ))
            ->add('birthProvince', TextType::class, array(
                'label' => $this->getLabel('birthProvince'),
                'required' => false,
                'attr' => array(
                    'class' => 'cfiscale cf-province'
                )
            ))
            ->add('taxCode', TextType::class, array(
                'label' => $this->getLabel('taxCode'),
                'required' => false,
                'attr' => array(
                    'class' => 'tax-code',
                    'widget_icon' => 'fa fa-barcode',
                    'suffix' => '<div class="cf progress progress-micro"><div class="progress-bar progress-bar-primary" role="progressbar"></div></div>',
                )
            ))
            ->add('vatId', TextType::class, array(
                'label' => $this->getLabel('vatId'),
                'required' => false,
            ))
            ->add('ownedPlaces', CollectionType::class, array(
                'label' => $this->getLabel('PersonOwnedPlaces'),
//                'entry_type' => new HouseType(array('fields_map' => $this->getFieldsMap('PersonOwnedPlaces'))),
                'entry_type' => HouseType::class,
                'entry_options' => array(
                    'add_more_button_label' => 'Abitazione',
                    'fields_map' => $this->getFieldsMap('PersonOwnedPlaces'),
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => array(
                    'add_more_button_label' => 'Abitazione',
                    'fields_map' => $this->getFieldsMap('PersonOwnedPlaces'),
                ),
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
            'data_class' => 'App\Entity\ContactFlock\Contacts\Person',
            'cascade_validation' => true,
            'additional_fields' => null,
            'origin' => null,
            'request_obj' => null,
            'hide_contact_type_field' => true,
            'add_representatives_field' => true,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'sinervis_contactbundle_contact_person';
    }
} 
