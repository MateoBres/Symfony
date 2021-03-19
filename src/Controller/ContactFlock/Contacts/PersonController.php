<?php


namespace App\Controller\ContactFlock\Contacts;


use App\Controller\AdminFlock\AdminController;
use App\Controller\ContactFlock\ContactController;
use App\Entity\ContactFlock\Contact;
use App\Entity\ContactFlock\Contacts\Company;
use App\Entity\ContactFlock\Contacts\Person;
use App\Entity\ContactFlock\Roles\Customer;
use App\Filter\ContactFlock\Roles\StudentFilterType;
use App\Form\ContactFlock\Contacts\PersonType;
use App\Form\ContactFlock\ContactType;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/contact_person", name="contact_flock_contact_person")
 */
class PersonController extends ContactController
{
    protected $flock_name = 'ContactFlock';
    protected $entity_namespace = 'Contacts';
    protected $entity_name = 'Person';
    protected $templates_path = 'contact_flock/Contacts/Person';

//    protected function createQuery()
//    {
//        $query = parent::createQuery();
//        $expr = new Expr();
//
//        // add here your join
//        $query
//            ->andWhere($expr->isInstanceOf('Contact', Person::class))
//            ->orWhere($expr->isInstanceOf('Contact', Company::class));
//
//        return $query;
//    }

    protected function getNewEntity()
    {
        $contact = new Person();

        $roleName = $_REQUEST['role'] ?? null;
        if ($roleName) {
            $contact->addRoleName($roleName);
        }

        return $contact;
    }

    protected function getNewEntityType()
    {
        return PersonType::class;
    }

    protected function getNewEntityFilterType()
    {
        return StudentFilterType::class;
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Person::class);
    }


}
