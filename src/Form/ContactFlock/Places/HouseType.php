<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 29/12/14
 * Time: 9.54
 */

namespace App\Form\ContactFlock\Places;


use App\Form\ContactFlock\PlaceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HouseType extends PlaceType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ContactFlock\Places\House',
            'add_more_button_label' => null,
            'fields_map' => null,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sinervis_contactbundle_places_house';
    }
}