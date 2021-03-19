<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 09/12/14
 * Time: 11.41
 */

namespace App\Form\ContactFlock\Contacts;

use App\Entity\ContactFlock\Contacts\SimplePerson;
use App\Form\AdminFlock\Form\AdminAbstractType;
use App\Form\ContactFlock\ContactableType;
use App\Form\ContactFlock\ContactInfos\EmailType;
use App\Form\ContactFlock\ContactInfos\PhoneType;
use Infinite\FormBundle\Form\Type\PolyCollectionType;
use Sinervis\ContactBundle\Entity\Contacts\Person;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimplePersonType extends AdminAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('firstName', TextType::class, array(
                'label' => 'Nome',
                'required' => true,
            ))
            ->add('lastName', TextType::class, array(
                'label' => 'Cognome',
                'required' => true,
            ))
            ->add('infos', PolyCollectionType::class, array(
                'label' => false,
                'types' => array(
                    EmailType::class,
                    PhoneType::class,
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
            ));
        ;

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SimplePerson::class,
            'cascade_validation' => true,
        ));
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
} 