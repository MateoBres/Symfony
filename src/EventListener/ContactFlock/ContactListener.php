<?php

namespace App\EventListener\ContactFlock;

use App\Entity\ContactFlock\Contact;
use App\Entity\ContactFlock\Contacts\Person;
use App\Entity\ContactFlock\Contacts\SimplePerson;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ReflectionException;

class ContactListener
{
    /**
     * @ORM\PostLoad()
     * @param Contact $contact
     * @param LifecycleEventArgs $event
     * @throws ReflectionException
     */
    public function postLoad(Contact $contact, LifecycleEventArgs $event)
    {
        foreach ($contact->getRoles() as $role) {
            $reflector = new \ReflectionClass($role);
            $roleShortName = $reflector->getShortName();

            $contact->addRoleName($roleShortName);
            if (method_exists($contact, 'set' . $roleShortName)) {
                $contact->{'set' . $roleShortName}($role);
            }
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(Contact $contact, LifecycleEventArgs $event)
    {
        $this->setCleanFullNameValue($contact);
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(Contact $contact, LifecycleEventArgs $event)
    {
        $this->setCleanFullNameValue($contact);
    }

    private function setCleanFullNameValue(Contact $contact)
    {
        if ($contact instanceof Person || $contact instanceof SimplePerson) {
            $contact->setFullNameCanonical($contact->getFullName());
            $names = explode(' ', $contact->getFullName());
        } else {
            $contact->setFullNameCanonical($contact->getBusinessName());
            $names = explode(' ', $contact->getBusinessName());
        }

        $cleanNames = array();
        foreach ($names AS $name) {
            $cleanNames[] = preg_replace('/[^a-zA-Z0-9]/', "", $name);
        }

        $contact->setCleanFullName(implode(' ', $cleanNames));
    }

    private function updateInvoiceDate(Contact $contact)
    {
        $invoiceData = $contact->fetchComposedInvoiceData();
        $contact->setInvoiceData($invoiceData);
    }
}