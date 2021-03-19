<?php


namespace App\Controller\ContactFlock;


use App\Controller\AdminFlock\AdminController;
use App\Entity\ContactFlock\Contact;
use App\Entity\ContactFlock\Contacts\Company;
use App\Entity\ContactFlock\Contacts\Person;
use App\Filter\ContactFlock\Roles\StudentFilterType;
use App\Form\ContactFlock\ContactType;
use App\Service\AdminFlock\EntityLayoutConfigManager;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/contact", name="contact_flock_contact")
 */
class ContactController extends AdminController
{
    protected $flock_name = 'ContactFlock';
    protected $entity_namespace = '';
    protected $entity_name = 'Contact';
    protected $templates_path = 'contact_flock/Contact';
    protected $entityLayoutConfigManager;

    protected function createQuery()
    {
        $query = parent::createQuery();
        $expr = new Expr();

        // add here your join
        $query
            ->andWhere($expr->isInstanceOf($this->getQueryMainAliasName(), Person::class))
            ->orWhere($expr->isInstanceOf($this->getQueryMainAliasName(), Company::class));

        return $query;
    }

    protected function getNewEntity()
    {
        return new Person();
    }

    protected function getNewEntityType()
    {
        return ContactType::class;
    }

    protected function getNewEntityFilterType()
    {
        return StudentFilterType::class;
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Contact::class);
    }

    /**
     * @required
     * @var EntityLayoutConfigManager
     */
    public function setEntityLayoutConfigManager(EntityLayoutConfigManager $entityLayoutManager)
    {
        $this->entityLayoutConfigManager = $entityLayoutManager;
    }


    protected function preRender($contact, $operation)
    {
        $this->mergeRoleLayoutConfigsToMainLayoutConfig($contact, $operation);
    }

    private function mergeRoleLayoutConfigsToMainLayoutConfig($contact, $operation)
    {
        foreach($contact->getRoles() as $role) {
            $roleLayoutManager = $this->entityLayoutConfigManager->setEntity($role);

            $fieldsetMachineName = 'fieldset_'.$roleLayoutManager->getEntityName();
            $this->fieldset_titles += [$fieldsetMachineName => $this->getFieldsetTitle($roleLayoutManager)];

            $entityName = lcfirst($roleLayoutManager->getEntityName());
            $this->attacheRoleLayoutBlocksToMainLayoutBlocks($entityName, $fieldsetMachineName, $roleLayoutManager, $operation);

            $this->fields_map += $roleLayoutManager->getFieldsMap($entityName);
        }
    }

    private function attacheRoleLayoutBlocksToMainLayoutBlocks($entityName, $fieldsetMachineName, $roleLayoutManager, $operation)
    {
        $configBlocks = $roleLayoutManager->getBlocks('', $entityName);
        if ($operation == 'new') {
            $this->new_blocks += [$fieldsetMachineName => $configBlocks];
        } elseif ($operation == 'edit') {
            $this->edit_blocks += [$fieldsetMachineName => $configBlocks];
        } elseif ($operation == 'show') {
            $roleShowBlocks = $roleLayoutManager->getBlocks('show', $entityName);

            $showBlocks = $roleShowBlocks['row1']['col1']['blocks'] ?? [];
            $this->show_blocks['row1']['contact_roles']['blocks'][$entityName] = [
                'title' => $roleLayoutManager->getEntityLabel(),
                'blocks' => $showBlocks
            ];
        }
    }

    private function getFieldsetTitle($entityLayoutManager)
    {
        return $entityLayoutManager->getEntityLabel();
    }
}
