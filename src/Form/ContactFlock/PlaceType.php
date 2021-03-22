<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 21/11/14
 * Time: 11.40
 */

namespace App\Form\ContactFlock;


use App\Entity\ContactFlock\Place;
use App\Form\AdminFlock\Form\AdminAbstractType;
use App\Form\ContactFlock\ContactInfos\EmailType;
use App\Form\ContactFlock\ContactInfos\PhoneType;
use Doctrine\ORM\EntityRepository;
use Genemu\Bundle\FormBundle\Form\JQuery\Type\AutocompleteType;
use Infinite\FormBundle\Form\Type\PolyCollectionType;
use Sinervis\ContactBundle\DBAL\Types\PlaceSiteTypeType;
use Sinervis\ContactBundle\Form\Roles\RepresentativeType;
use Sinervis\ContactBundle\Form\Places\RegularSupplierType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AdminAbstractType
{
    protected $_add_site_fields = false;
    protected $_add_interested_relation = false;

    public function __construct()
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => Place::class,
            'cascade_validation' => true,
            'type' => null,
        ));

        if ($this->_add_interested_relation) {
            //$resolver->setDefault('validation_groups', array('Default','interested'));
        }
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('fullAddress', TextType::class, array(
                    'label' => $this->getLabel('fullAddress'),
                    'attr' => array(
                        'class' => 'geo-full-address sv-autocomplete-input',
                        'geo-name' => 'formattedAddress',
                        'widget_icon' => 'fa fa-chevron-down addresspicker-button',
                        'icon_position' => 'icon-append',
//                        'suffix' => "Scegli l'indirizzo corretto dalla lista",
                        'data-autocomplete-route' => 'utility_geocode'
                    ),
                    'required' => true,
                )
            )
            ->add('street', TextType::class, array(
                'label' => $this->getLabel('street'),
                'attr' => array('geo-name' => 'route'),
                'required' => false,
            ))
            ->add('number', TextType::class, array(
                'label' => $this->getLabel('number'),
                'attr' => array('geo-name' => 'street_number'),
                'required' => false,
            ))
            ->add('zip', TextType::class, array(
                'label' => $this->getLabel('zip'),
                'attr' => array('geo-name' => 'postal_code'),
                'required' => false,
            ))
            ->add('city', TextType::class, array(
                'label' => $this->getLabel('city'),
                'attr' => array('geo-name' => 'locality'),
                'required' => false,
            ))
            ->add('province', TextType::class, array(
                'label' => $this->getLabel('province'),
                'attr' => array('geo-name' => 'administrative_area_level_2'),
                'required' => false,
            ))
            ->add('region', TextType::class, array(
                'label' => $this->getLabel('region'),
                'attr' => array('geo-name' => 'administrative_area_level_1'),
                'required' => false,
            ))
            ->add('country', TextType::class, array(
                'label' => $this->getLabel('country'),
                'attr' => array('geo-name' => 'country'),
                'required' => false,
            ))
            ->add('latitude', TextType::class, array(
                'label' => $this->getLabel('latitude'),
                'attr' => array('geo-name' => 'latitude'),
                'required' => false,
            ))
            ->add('longitude', TextType::class, array(
                'label' => $this->getLabel('longitude'),
                'attr' => array('geo-name' => 'longitude'),
                'required' => false,
            ))
            // Andrea requested to remove this (mail on 22 Ottobre 2016 11:30)
            //->add('contactable',new ContactableType(), array('label'=>false))
        ;


        if ($this->_add_site_fields) {
            $builder
                ->add('siteType', ChoiceType::class, array(
                    'label' => $this->getLabel('siteType'),
                    'choices' => PlaceSiteTypeType::getChoices(),
                    'placeholder' => '',
                    'required' => false,
                ))
                ->add('siteName', TextType::class, array(
                    'label' => $this->getLabel('siteName'),
                    'required' => false,
                ));
        }
//    if($this->_add_interested_relation) {
//      $builder
//          ->add('customer','genemu_jqueryautocompleter_entity', array(
//            'query_builder' => function(EntityRepository $er) {return $er->createQueryBuilder('c');},
//            'label'=>$this->getLabel('customer'),
//            'class'=>'Sinervis\ContactBundle\Entity\Roles\Customer',
//            'route_name' => 'admin_roles_customer_via_ajax',
//            'required' => true,
//          ))
//          ->add('representatives','collection',array(
//              'label'=>false,
//              'type'=>new RepresentativeType(array('fields_map'=>$this->getFieldsMap('representatives'))),
//              'allow_add'=>true,
//              'allow_delete'=>true,
//              'by_reference' => false,
//              'attr' => array(
//                  'add_more_button_label' => 'Aggiungi Referente',
//                  'fields_map' => $this->getFieldsMap('representatives'),
//              )
//          ))
//        ;
//    }

    }

    public function getBlockPrefix()
    {
        return 'sinervis_contactbundle_place';
    }
} 
