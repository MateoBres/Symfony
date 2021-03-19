<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 29/12/14
 * Time: 9.54
 */

namespace App\Form\ContactFlock\Places;

use App\DBAL\Types\OfficeTypeType;
use App\Form\ContactFlock\PlaceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfficeType extends PlaceType
{
    protected $_add_type_fields = false;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['_add_type_fields']) {
            $builder
                ->add('type', ChoiceType::class, array(
                    'label' => 'Tipo',
                    'choices' => OfficeTypeType::getChoices(),
                    'attr' => array(
                        'class' => "office-type ",
                    ),
                ))
            ;
        }

        parent::buildForm($builder, $options);
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ContactFlock\Places\Office',
            'add_more_button_label' => null,
            'fields_map' => null,
            '_add_type_fields' => null,
        ));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'sinervis_contactbundle_places_office';
    }
}