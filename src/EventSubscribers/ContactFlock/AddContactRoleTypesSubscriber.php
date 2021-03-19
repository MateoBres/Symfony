<?php


namespace App\EventSubscribers\ContactFlock;


use App\Annotations\ContactRoleMap;
use App\Entity\ContactFlock\Contact;
use App\Service\AdminFlock\EntityLayoutConfigManager;
use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

//define('ENTITY_LAYOUT_CONFIG_PATH', __DIR__.'/../../Resources/config/ContactFlock/Roles');

class AddContactRoleTypesSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private $doctrineManagedEntityList;
    private $finder;

    public function __construct(EntityManagerInterface $entityManager, EntityLayoutConfigManager $finder)
    {
        $this->entityManager = $entityManager;
        $this->doctrineManagedEntityList = $this->entityManager->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
        $this->finder = $finder;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    /**
     * PRE SET DATA
     */
    public function onPreSetData(FormEvent $event)
    {
        $contact = $event->getData();
        $form = $event->getForm();

        if ($contact) {
            $contactRoleNames = $contact->getRoleNames() ?? [];
            $this->addRolesToContact($contact, $contactRoleNames);
            $this->addContactRoleTypes($form, $contactRoleNames ?? []);

            $event->setData($contact);
        }
    }

    /**
     * PRE SUBMIT
     */
    public function onPreSubmit(FormEvent $event)
    {
        $contact = $event->getData();
        $form = $event->getForm();

        $this->addContactRoleTypes($form, $contact['roleNames'] ?? []);
    }

    /**
     * POST SUBMIT
     */
    public function onPostSubmit(FormEvent $event)
    {
        $contact = $event->getData();

        if ($contact) {
            $activeRoleNames = $contact->getRoleNames();
            foreach ($contact->getRoles() as $contactRole) {
                $reflector = new \ReflectionClass($contactRole);
                if (!in_array($reflector->getShortName(), $activeRoleNames)) {
                    $contact->removeRole($contactRole);
                }
            }
        }

        $event->setData($contact);
    }

    private function addRolesToContact(Contact $contact, $contactRoleNames)
    {
        foreach ($contactRoleNames as $roleName) {
            if ($className = $this->getFullNamespacedClassName($roleName)) {
                $role = $contact->hasRole($className);

                if (!$role) {
                    $role = new $className();
                }

                $contact->addRole($role);
                $contact->{'set'.$roleName}($role);
            }
        }
    }

    private function addContactRoleTypes(FormInterface $form, array $contactRoleNames = [])
    {
        foreach ($contactRoleNames as $roleName) {
            if ($className = $this->getFullNamespacedClassName($roleName)) {
                $formType = $this->getFullNamespacedFormName($className);
                $fieldName = lcfirst($roleName);

                $form->add($fieldName, $formType, [
                    'fields_map' => $this->finder->setEntity($className)->getFieldsMap(),
                    'embedded' => false
                ]);
            }
        }
    }

    private function getFullNamespacedClassName($entityName): ?String
    {
        $fullNamespacedRoleNames = preg_grep('/Roles\\\\'.$entityName.'$/', $this->doctrineManagedEntityList);

        if ($fullNamespacedRoleNames) {
            return current($fullNamespacedRoleNames);
        }

        return null;
    }

    private function getFullNamespacedFormName($className): String
    {
        $formName = str_replace('App\Entity\\', 'App\\Form\\', $className);
        return $formName.'Type';
    }
}