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
use Rares\ImageCropBundle\Form\Type\CropImageType;
use ReflectionException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\ContactFlock\Roles\RepresentativeType;
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

        if (!$options['embedded'] && $contact) {
            $builder
                ->add('roleNames', ChoiceType::class, array(
                    'label' => false,
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'choices' => $this->getAllowedRoles($contact),
                    'attr' => array('class' => 'js-role-names')
                ))
                ->add('notes', TextareaType::class, [
                    'label' => $this->getLabel('notes'),
                    'required' => false,
                    'attr' => [
                        'rows' => 7
                    ]
                ])
                ->add('imageFile', CropImageType::class, [
                    'label' => $this->getLabel('imageFile'),
                    'required' => false,
                    'allow_delete' => true,
                    'delete_label' => 'Spunta per eliminare',
                    'download_link' => false,
                    'download_label' => 'Scarica ' . $this->getLabel('imageFile'),
                    'attr' => [
                        'upload_label' => 'Carica file immagine',
                        'title' => 'gif, jpg o png',
                    ]
                ]);
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            $options = $form->getConfig()->getOptions();

            $isContactTypeDisabled = ($data && $data->getId());

            if (!$options['embedded']) {
                $form
                    ->add('type', ChoiceType::class, array(
                        'label' => $this->getLabel('type'),
                        'expanded' => true,
                        'disabled' => $isContactTypeDisabled,
                        'multiple' => false,
                        'data' => $data ? $data->getType() : 'p',
                        'choices' => ContactKindType::getChoices(),
                        'required' => true,
                        'attr' => array('class' => 'contact_type_choice inline-group ' . ($isContactTypeDisabled ? 'radio radio-disable' : ''),
                            'readonly' => 'readonly'
                        ),
                    ));
            }

            $form
                ->add('infos', PolyCollectionType::class, array(
                    'label' => false,
                    'types' => array(
                        EmailType::class,
                        PhoneType::class,
                        WebsiteType::class,
                    ),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'required' => false,
                ))
                ->add('user', UserType::class, array(
//                    'security_context' => $this->security_context,
                    'fields_map' => $this->fields_map,
                ))
            ;

            if (isset($options['origin']) && $options['origin'] == 'contact') {
                $form->add('createUser', ChoiceType::class, array(
                    'label' => 'Crea Utente',
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'choices' => array(
                        '' => 'User',
                    ),
                    'data' => $data->getUser() !== null ? array('User') : array(),
                    // 'required' => false,
                    'attr' => array('class' => 'role-name')
                ));

                if ($data->getUser() == null && isset($options['request_obj']) && isset($options['request_obj']['createUser'])) {
                    $data->setUser(new User());
                } elseif ($this->requestObj !== null && !isset($options['request_obj']['createUser'])) {
                    $data->setUser(null);
                }

                if ($data->getUser()) {
                    $form->add('user', UserType::class, array(
                        'security_context' => $this->security_context,
                        'fields_map' => $this->fields_map,
                        'origin' => 'contact'
                    ));
                }
            }
        });

//        $builder->addEventSubscriber($this->addContactRoleTypesubscriber);
    }

    /**
     * @param Contact $contact
     * @return array
     * @throws ReflectionException
     */
    private function getAllowedRoles(Contact $contact)
    {
        $allowedRoleMap = $this->annotationReader->getClassAnnotation(
            new \ReflectionClass($contact),
            ContactRoleMap::class
        )->value;

        $allowedRoles = [];
        foreach ($allowedRoleMap as $shortEntityName => $fullNamespacedEntity) {
            $entityLabel = $this->layoutConfigManager->setEntity($fullNamespacedEntity)->getEntityLabel();
            $allowedRoles[$entityLabel] = $shortEntityName;
        }

        return $allowedRoles;
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
