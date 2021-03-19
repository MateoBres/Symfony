<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 04/12/14
 * Time: 11.58
 */

namespace App\Form\ContactFlock;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactableType extends AbstractType
{
  private $printLabel = true;

  public function __construct(array $options = array())
  {   
      $this->printLabel = isset($options['printLabel']) ? $options['printLabel'] : true;
      $resolver = new OptionsResolver();
      $this->configureOptions($resolver);
      //$this->options = $resolver->resolve($options);
  }

  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('infos','infinite_form_polycollection', array(
            'label'=> $this->printLabel ? 'Recapiti' : false,
            'types'=> array(
                'sinervis_contactBundle_contactInfo_Email',
                'sinervis_contactBundle_contactInfo_Telefono',
            ),
            'allow_add'=>true,
            'allow_delete'=>true,
            'by_reference' => false,
            'required' => false,
        ))
    ;
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
        'data_class' => 'Sinervis\ContactBundle\Entity\Contactable',
        'cascade_validation' => true,
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'sinervis_contactbundle_contactable';
  }
} 