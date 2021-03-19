<?php


namespace App\Controller\ContactFlock\Contacts;


use App\Controller\AdminFlock\AdminController;
use App\Controller\ContactFlock\ContactController;
use App\Entity\ContactFlock\Contact;
use App\Entity\ContactFlock\Contacts\Company;
use App\Entity\ContactFlock\Contacts\Person;
use App\Filter\ContactFlock\Roles\StudentFilterType;
use App\Form\ContactFlock\Contacts\CompanyType;
use App\Form\ContactFlock\Contacts\PersonType;
use App\Form\ContactFlock\ContactType;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/contact_company", name="contact_flock_contact_company")
 */
class CompanyController extends ContactController
{
    protected $flock_name = 'ContactFlock';
    protected $entity_namespace = 'Contacts';
    protected $entity_name = 'Company';
    protected $templates_path = 'contact_flock/Contacts/Company';

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
        $contact = new Company();

        $roleName = $_REQUEST['role'] ?? null;
        if ($roleName) {
            $contact->addRoleName($roleName);
        }

        return $contact;
    }

    protected function getNewEntityType()
    {
        return CompanyType::class;
    }

    protected function getNewEntityFilterType()
    {
        return StudentFilterType::class;
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Company::class);
    }


}
