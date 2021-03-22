<?php

namespace App\Form\ContactFlock;

use App\Annotations\ContactRoleMap;
use App\DBAL\Types\ContactKindType;
use App\Entity\ContactFlock\Contact;
use App\EventSubscribers\ContactFlock\AddContactRoleTypesSubscriber;
use App\Form\AdminFlock\Form\AdminAbstractType;
use App\Form\ContactFlock\ContactInfos\EmailType;
use App\Form\ContactFlock\ContactInfos\PhoneType;
use App\Form\ContactFlock\ContactInfos\WebsiteType;
use App\Form\UserFlock\UserType;
use App\Service\AdminFlock\EntityLayoutConfigManager;
use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Infinite\FormBundle\Form\Type\PolyCollectionType;
use ReflectionException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ContactType extends AdminAbstractType
{
    private $addContactRoleTypesubscriber;
    private $annotationReader;
    private $layoutConfigManager;

    public function __construct(AddContactRoleTypesSubscriber $addContactRoleTypesubscriber, AnnotationReader $annotationReader, EntityLayoutConfigManager $layoutConfigManager)
    {
        $this->addContactRoleTypesubscriber = $addContactRoleTypesubscriber;
        $this->annotationReader = $annotationReader;
        $this->layoutConfigManager = $layoutConfigManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws ReflectionException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $contact = $options['data'] ?? null;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            $form
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
                ))
                ->add('user', UserType::class, array(
//                    'security_context' => $this->security_context,
                    'fields_map' => $this->getFieldsMap(),
                ))
            ;
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'model_class' => $this->dataClass,
            'cascade_validation' => true,
            'additional_fields' => null,
            'origin' => null,
            'request_obj' => null,
            'hide_contact_type_field' => true,
            'type' => null,
        ));
    }

    public function getBlockPrefix()
    {
        return 'sinervis_contactbundle_contact';
    }

}
