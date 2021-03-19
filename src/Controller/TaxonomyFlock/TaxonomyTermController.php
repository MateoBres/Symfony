<?php

namespace App\Controller\TaxonomyFlock;

use App\Entity\TaxonomyFlock\TaxonomyTerm;
use App\Form\TaxonomyFlock\TaxonomyTermType;
use App\Filter\TaxonomyFlock\TaxonomyTermFilterType;
use App\Repository\TaxonomyFlock\TaxonomyTermRepository;
use App\Controller\AdminFlock\AdminController;
use App\Security\AdminFlock\Authorization\Voter\AdminVoter;
use App\Service\AdminFlock\RouteHelper;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Yaml\Yaml;

/**
 * @Route("admin/taxonomy_flock_taxonomy_term", name="taxonomy_flock_taxonomy_term")
 */
class TaxonomyTermController extends AdminController
{
    protected $flock_name   = 'TaxonomyFlock';
    protected $entity_namespace = '';
    protected $entity_name   = 'TaxonomyTerm';
    protected $templates_path = 'taxonomy_flock/taxonomy_term';

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(TaxonomyTerm::class);
    }

    protected function createQuery()
    {
        $query = parent::createQuery();

        // add here your join
        //$query->leftJoin('{{ entity_class }}.childEntity','b')->addSelect('b');

        return $query;
    }

    protected function getNewEntity()
    {
        return new TaxonomyTerm();
    }

    protected function getNewEntityType()
    {
        return TaxonomyTermType::class;
    }

    protected function getNewEntityFilterType()
    {
        return TaxonomyTermFilterType::class;
    }

    /**
     * @Route("/vocabularies", name="_vocabularies")
     * @param Request $request
     * @param Reader $annotationReader
     * @param RouteHelper $routeHelper
     * @return Response
     * @throws ReflectionException
     */
    public function termIndex(Request $request, Reader $annotationReader, RouteHelper $routeHelper)
    {
        // create base query
        $query = $this->createQuery();

        $reflectionClass = new ReflectionClass(TaxonomyTerm::class);

        $discriminatorMap = $annotationReader->getClassAnnotation($reflectionClass, DiscriminatorMap::class);
        $discriminatorMapArr = (array) $discriminatorMap;
        $fullPathedsubEntities = $discriminatorMapArr['value'];
        unset($fullPathedsubEntities['TaxonomyTerm']);

        $vocabularyList = [];
        foreach ($fullPathedsubEntities as $fullPathedsubEntity) {
            $entity = new $fullPathedsubEntity;
            $configFile = $this->getPathToControllerConfigFile($entity);
            $config = Yaml::parse(file_get_contents($configFile));

            $repo = $this->getRepository();
            $vocabularyList[] = [
                'plural_name' => $config['plural_name'],
                'route_name' =>$routeHelper->getRouteName($entity),
                'num_terms' => $repo->getNumberOfTermsForVocabulary($entity)
            ];
        }

        // security check
        if (!$this->isGranted(AdminVoter::LIST_ALL, $this->getNewEntity())) {
            if ($this->isGranted(AdminVoter::LIST_OWNED, $this->getNewEntity())) {
                $this->addSecurityFilter($query);
            } else {
                throw new AccessDeniedException();
            }
        }

        return $this->render('taxonomy_flock/vocabularyIndex.html.twig', array(
            'entities'              => $vocabularyList,
            'filter_form'           => null,
            'fields'                => array('Nome Vocabolario', 'Num. voci'),
            'new_dummy_entity'      => $this->getNewEntity(), // this is used to check permission in is_granted('CREATE') call
            'table_class'           => 'index-'.$this->flock_name.'-'.str_replace('\\','-', $this->entity_name),
        ));
    }

    /**
     * @param $entity
     * @return string|string[]
     * @throws ReflectionException
     */
    private function getPathToControllerConfigFile($entity)
    {
        $reflector = new ReflectionClass($entity);

        $config_file = str_replace(DIRECTORY_SEPARATOR, '/', $reflector->getFileName());

        $config_file = str_replace('src/Entity', 'src/Resources/config', $config_file);
        $config_file = str_replace('.php', 'Controller.yaml', $config_file);

        $config_file = str_replace('/', DIRECTORY_SEPARATOR, $config_file);

        return $config_file;
    }

}
