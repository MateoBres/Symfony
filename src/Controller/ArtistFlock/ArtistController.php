<?php

namespace App\Controller\ArtistFlock;

use App\Entity\ArtistFlock\Artist;
use App\Entity\ArtistFlock\Artists\Painter;
use App\Entity\ArtistFlock\Artists\Sculptor;
use App\Form\ArtistFlock\ArtistType;
use App\Filter\ArtistFlock\ArtistFilterType;
use App\Repository\ArtistFlock\ArtistRepository;
use App\Controller\AdminFlock\AdminController;
use App\Service\AdminFlock\EntityLayoutConfigManager;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/artist", name="artist_flock_artist")
 */
class ArtistController extends AdminController
{
    protected $flock_name   = 'ArtistFlock';
    protected $entity_namespace = '';
    protected $entity_name   = 'Artist';
    protected $templates_path = 'artist_flock/Artist';
    protected $entityLayoutConfigManager;

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(Artist::class);
    }

    protected function createQuery()
    {
        $query = parent::createQuery();
        $expr = new Expr();

        // add here your join
        $query
            ->andWhere($expr->isInstanceOf($this->getQueryMainAliasName(), Painter::class))
            ->orWhere($expr->isInstanceOf($this->getQueryMainAliasName(), Sculptor::class));

        return $query;
    }

    protected function getNewEntity()
    {
        return new Painter();
    }

    protected function getNewEntityType()
    {
        return ArtistType::class;
    }

    protected function getNewEntityFilterType()
    {
        return ArtistFilterType::class;
    }

//
//    /**
//     * @required
//     * @var EntityLayoutConfigManager
//     */
//    public function setEntityLayoutConfigManager(EntityLayoutConfigManager $entityLayoutManager)
//    {
//        $this->entityLayoutConfigManager = $entityLayoutManager;
//    }
//
//
//    protected function preRender($contact, $operation)
//    {
////        $this->mergeRoleLayoutConfigsToMainLayoutConfig($contact, $operation);
//    }
//
//
//    private function mergeRoleLayoutConfigsToMainLayoutConfig($contact, $operation)
//    {
//        foreach ($contact->getRoles() as $role) {
//            $roleLayoutManager = $this->entityLayoutConfigManager->setEntity($role);
//
//            $fieldsetMachineName = 'fieldset_' . $roleLayoutManager->getEntityName();
//            $this->fieldset_titles += [$fieldsetMachineName => $this->getFieldsetTitle($roleLayoutManager)];
//
//            $entityName = lcfirst($roleLayoutManager->getEntityName());
//            $this->attacheRoleLayoutBlocksToMainLayoutBlocks($entityName, $fieldsetMachineName, $roleLayoutManager, $operation);
//
//            $this->fields_map += $roleLayoutManager->getFieldsMap($entityName);
//        }
//    }
//
//    private function attacheRoleLayoutBlocksToMainLayoutBlocks($entityName, $fieldsetMachineName, $roleLayoutManager, $operation)
//    {
//        $configBlocks = $roleLayoutManager->getBlocks('', $entityName);
//        if ($operation == 'new') {
//            $this->new_blocks += [$fieldsetMachineName => $configBlocks];
//        } elseif ($operation == 'edit') {
//            $this->edit_blocks += [$fieldsetMachineName => $configBlocks];
//        } elseif ($operation == 'show') {
//            $roleShowBlocks = $roleLayoutManager->getBlocks('show', $entityName);
//
//            $showBlocks = $roleShowBlocks['row1']['col1']['blocks'] ?? [];
//            $this->show_blocks['row1']['contact_roles']['blocks'][$entityName] = [
//                'title' => $roleLayoutManager->getEntityLabel(),
//                'blocks' => $showBlocks
//            ];
//        }
//    }
//
//    private function getFieldsetTitle($entityLayoutManager)
//    {
//        return $entityLayoutManager->getEntityLabel();
//    }

}
