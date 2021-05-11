<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 13/07/14
 * Time: 17.00
 */

namespace App\Controller\AdminFlock;

use App\Service\AdminFlock\RouteHelper;
use App\Service\SettingsFlock\SettingsManager;
use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Doctrine\ORM\QueryBuilder;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ReflectionClass;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Security\AdminFlock\Authorization\Voter\AdminVoter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Symfony\Component\Yaml\Yaml;
use App\Form\AdminFlock\Form\FormErrors;

abstract class AdminController extends AbstractController
{
    //test
    protected $flock_name = null;
    protected $entity_namespace = null;
    protected $entity_name = null;
    protected $route_prefix = null;
    protected $route_params = array();
    protected $singular_name = null;
    protected $plural_name = null;
    protected $limit_per_page = 50;
    protected $fields_map = array();
    protected $fieldset_titles = array();
    protected $is_multirole = false;
    protected $list_fields = array();
    protected $show_blocks = array();
    protected $edit_blocks = array();
    protected $new_blocks = array();
    protected $generic_entity = null;
    protected $stat_fields = array();
    protected $block_filters = array();
    protected $admin_voter;
    protected $paginator;
    protected $form_filter;
    protected $form_errors;
    protected $route_helper;
    protected $annotationReader;
    protected $icon;
    protected $settingsManager;
    protected $configFileName;

    public function __construct(AdminVoter $admin_voter, PaginatorInterface $paginator, FilterBuilderUpdater $formFilter, FormErrors $form_errors, RouteHelper $route_helper, AnnotationReader $annotationReader, SettingsManager $settingsManager)
    {
        $this->voter = $admin_voter;
        $this->paginator = $paginator;
        $this->form_filter = $formFilter;
        $this->form_errors = $form_errors;
        $this->route_helper = $route_helper;
        $this->annotationReader = $annotationReader;
        $this->settingsManager = $settingsManager;

        if (!$this->route_prefix) {

            $this->route_prefix = $this->getMainRoutePrefix($this->getNewEntity());
            //dd($this->route_prefix);
        }

        $reflector = new ReflectionClass($this);
        $config_file = str_replace(DIRECTORY_SEPARATOR, '/', $reflector->getFileName());

        $config_file = str_replace('src/Controller', 'src/Resources/config', $config_file);
        $config_file = str_replace('.php', '.yaml', $config_file);

        $config_file = str_replace('/', DIRECTORY_SEPARATOR, $config_file);

        /**
         * If config file not found try to guess the config file name using entity name.
         */
        if (!is_file($config_file)) {
            $shortClassName = $reflector->getShortName();
            $guessingClassName = $this->entity_name . 'Controller';
            $config_file = str_replace($shortClassName, $guessingClassName, $config_file);
        }

        if (is_file($config_file)) {
            // load configuration from file and merge with controller variables
            $config = Yaml::parse(file_get_contents($config_file));
            $this->tweakConfig($config);

            if (isset($config['singular_name'])) $this->singular_name = $config['singular_name'];
            if (isset($config['plural_name'])) $this->plural_name = $config['plural_name'];
            if (isset($config['icon'])) $this->icon = $config['icon'];
            if (isset($config['generic_entity'])) $this->generic_entity = $config['generic_entity'];
            if (isset($config['stat_fields'])) $this->stat_fields = $config['stat_fields'];
            //dd($this->fields_map);
//            dd($config['fields_map']);
            if (isset($config['fields_map'])) $this->fields_map = array_merge($this->fields_map, $config['fields_map']);

            if (isset($config['block_filters'])) $this->block_filters = array_merge($this->block_filters, $config['block_filters']);

            if (isset($config['fieldset_titles'])) $this->fieldset_titles = array_merge($this->fieldset_titles, $config['fieldset_titles']);
            if (isset($config['is_multirole'])) $this->is_multirole = $config['is_multirole'];

            if (isset($config['list_fields'])) $this->list_fields = array_merge($this->list_fields, $config['list_fields']);

            if (isset($config['blocks'])) { // default configuration for all blocks
                $this->show_blocks = $config['blocks'];
                $this->edit_blocks = $config['blocks'];
                $this->new_blocks = $config['blocks'];
            }

            // optional configuration for show, edit and new blocks
            if (isset($config['show_blocks'])) $this->show_blocks = $config['show_blocks'];
            if (isset($config['edit_blocks'])) $this->edit_blocks = $config['edit_blocks'];
            if (isset($config['new_blocks'])) $this->new_blocks = $config['new_blocks'];

            // find and attach the fields maps of sub fields of the type collection
            $path_to_src = str_replace('Controller/AdminFlock', '', __DIR__) . 'Resources/config/';
            $this->fields_map = $this->attachSubFieldsMaps($this->fields_map, $path_to_src);
        }
    }

    protected function tweakConfig(&$config): void
    {

    }

    protected function getConfiguration()
    {
        return array(
            'flock_name' => $this->flock_name,
            'entity_namespace' => $this->entity_namespace,
            'entity_name' => $this->entity_name,
            'route_prefix' => $this->route_prefix,
            'route_params' => $this->route_params,
            'singular_name' => $this->singular_name,
            'plural_name' => $this->plural_name,
            'icon' => $this->icon,
            'fields_map' => $this->fields_map,
            'fieldset_titles' => $this->fieldset_titles,
            'is_multirole' => $this->is_multirole,
            'generic_entity' => $this->generic_entity,
            'stat_fields' => $this->stat_fields,
            'list_fields' => $this->list_fields,
            'templates_path' => $this->templates_path,
            'block_filters' => $this->block_filters,
        );
    }

    private function attachSubFieldsMaps($fields_map, $path_to_src)
    {
        foreach ($fields_map as $key => $field_map) {
            if (isset($field_map['path_to_config'])) {
                $path_to_config = $path_to_src . $field_map['path_to_config'];
                $subConfig = Yaml::parse(file_get_contents($path_to_config));
                $fields_map[$key]['fields_map'] = $this->attachSubFieldsMaps($subConfig['fields_map'], $path_to_src);
            }
        }
        return $fields_map;
    }

    protected function fixFieldMapMainAlias($from, $to)
    {
        // replace all reference of $from with $to inside "sort_by" and search_alias" fields (used in specialized places controller)
        foreach ($this->fields_map as $key => $values) {
            if (isset($this->fields_map[$key]['sort_by'])) {
                $this->fields_map[$key]['sort_by'] = str_replace($from . '.', $to . '.', $this->fields_map[$key]['sort_by']);
            }
            if (isset($this->fields_map[$key]['search_alias'])) {
                $this->fields_map[$key]['search_alias'] = str_replace($from . '.', $to . '.', $this->fields_map[$key]['search_alias']);
            }
        }
    }

    abstract protected function getNewEntity();

    abstract protected function getNewEntityType();

    abstract protected function getNewEntityFilterType();

    abstract protected function getRepository();

    private function getMainRoutePrefix($entity)
    {
        return $this->route_helper->getRouteName($entity);
    }

    /**
     * @return mixed      the main alias name for the query
     */
    protected function getQueryMainAliasName()
    {
        if (empty($this->entity_namespace)) {
            return $this->entity_name;
        }

        return str_replace('\\', '_', $this->entity_namespace) . '_' . $this->entity_name;
    }

    /**
     * called by CreateQuery if the user has LIST_OWNED but not LIST_ALL permission
     * it should be overridden WITHOUT calling parent::addSecurityFilter
     *
     * @param $query
     */
    protected function addSecurityFilter(QueryBuilder $query)
    {
        // override this function with a less restrictive condition (do not call parent::addSecurityFilter)
        $query->andWhere($this->getQueryMainAliasName() . '.id < 0');
    }

    protected function getFullEntityNamespace()
    {
        if (empty($this->entity_namespace)) {
            return 'App\Entity\\' . $this->flock_name . '\\' . $this->entity_name;
        }

        return 'App\Entity\\' . $this->flock_name . '\\' . $this->entity_namespace . '\\' . $this->entity_name;
    }

    /**
     * Called before any action (index, create, new, show, edit, update, delete)
     */
    protected function preExecute()
    {

    }

    /**
     * Called before any action (index, create, new, show, edit, update, delete)
     */
    protected function tweakRenderVariables(Array &$renderVars, $operation): void
    {

    }

    /**
     * Called before calling 'render' function (new, show, edit)
     */
    protected function preRender($entity, $operation)
    {

    }

    /**
     * Called before calling 'render' function (new, show, edit)
     * @param $entity
     * @param $operation
     * @param Request|null $request
     */
    protected function setDefaultsForEntity($entity, $operation, ?Request $request)
    {

    }

    /**
     * Called by index action
     *
     * @param QueryBuilder $query the filtered but not yet executed query
     */
    protected function postApplyFilters(QueryBuilder $query, $isFormSubmitted)
    {

    }

    /**
     * called by index action, build the base query (with security check)
     *
     * @return mixed     the query object
     */
    protected function createQuery()
    {
        $em = $this->getDoctrine()->getManager();

        $fullEntityNamespace = $this->getFullEntityNamespace();

        $query = $em
            ->getRepository($fullEntityNamespace)
            ->createQueryBuilder($this->getQueryMainAliasName())
            ->orderBy("{$this->getQueryMainAliasName()}.id", 'DESC');

        return $query;
    }

    protected function setQuickFilters(QueryBuilder $query, Request $request)
    {
        // quick filter ($_GET['q'])
        if ($search_term = $request->get('q')) {
            $count = 0;
            $whereQuery = $query->expr()->orX();
            foreach ($this->fields_map as $field => $configuration) {
                if (isset($configuration['search_alias'])) {

                    $whereQuery->add($query->expr()->like($configuration['search_alias'], '?1'));
                    $count++;

                }
                //dd($configuration['search_alias']);
            }
            if ($count) {
                $query->andWhere($whereQuery);
                $query->setParameter('1', '%' . $search_term . '%');
            }
        }
    }

    protected function getFilterForm()
    {
//        dd($this->createForm($this->getNewEntityFilterType(), null, array(
//            'fields_map' => $this->fields_map
//        )));
        return $this->createForm($this->getNewEntityFilterType(), null, array(
            'fields_map' => $this->fields_map
        ));
    }

    protected function applyAdvanceFilters(Request $request, QueryBuilder $query, $filter_form)
    {
        if ($request->query->has($filter_form->getName())) {
            $filter_form->submit($request->query->get($filter_form->getName()));
            $this->form_filter->addFilterConditions($filter_form, $query);
        }
    }

    protected function applyPagination(Request $request, QueryBuilder $query)
    {
        // Paginator cannot support two FROM components or composite identifiers,
        // because it cannot predict the total count in the database.
        // The solution to that is simple and direct, you can provide the count manually through the hint on the query.
        $count = count($query->getQuery()->getScalarResult());
        $query->getQuery()->setHint('knp_paginator.count', $count);

        $pagination = $this->paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            $this->limit_per_page /*limit per page*/,
            array()
        );

        $pagination->setTemplate('admin_flock/crud/_pagination.html.twig');

        return $pagination;
    }

    protected function listExcelExport($request, $query, $renderVars): Response
    {
        /** @var $query QueryBuilder */

        $count = count($query->getQuery()->getScalarResult());
        $this->limit_per_page = $count;
        $renderVars['entities'] = $this->applyPagination($request, $query);

        $html = $this->render($this->templates_path . '/index.html.twig', $renderVars);

        $crawler = new Crawler($html->getContent());

        $table = '<table>';
        $table .= $crawler->filter('table.list-table')->html();
        $table .= '</table>';
        $tableStripped = strip_tags($table, '<table><tr><th><td><tbody><thead>');

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = @$reader->loadFromString($tableStripped);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $worksheet = $spreadsheet->getActiveSheet();
        $lastColumn = $worksheet->getHighestColumn();
        $worksheet->removeColumn($lastColumn);

        $lastColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn);
        foreach (range(1, $lastColumnIndex) as $col) {
            $worksheet->getStyle('A1:' . $lastColumn . '1' )->getFont()->setBold(true);
            $worksheet->getStyle('A1:' . $lastColumn . '1' )->getFont()->setSize(12);
            $worksheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $filename = $this->plural_name.' - '.date('m-d-Y').'.xlsx';

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $contentType);
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->setPrivate();
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->setCallback(function () use ($writer) {
            $writer->save('php://output');
        });

        return $response;
    }

    /**
     * @Route("/", name="")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        //$this->preExecute();

        // create base query
        $query = $this->createQuery();

        // apply quick filter (top main search field)
        $this->setQuickFilters($query, $request);

        // apply advanced filters (Lexik bundle)
        $filter_form = $this->getFilterForm();
        //dd($filter_form);
        $this->applyAdvanceFilters($request, $query, $filter_form);

        $this->postApplyFilters($query, $filter_form->isSubmitted());

        // security check
        if (!$this->isGranted(AdminVoter::LIST_ALL, $this->getNewEntity())) {
            if ($this->isGranted(AdminVoter::LIST_OWNED, $this->getNewEntity())) {
                $this->addSecurityFilter($query);
            } else {
                throw new AccessDeniedException();
            }
        }

        $renderVars = [
            'entities' => $this->applyPagination($request, $query),
            'route_prefix' => $this->route_prefix,
            'configuration' => $this->getConfiguration(),
            'debug_query' => $query->getDql(),
            'new_dummy_entity' => $this->getNewEntity(),
            'filter_form' => $filter_form->createView(),
            'table_class' => 'index-' . $this->flock_name . '-' . str_replace('\\', '-', $this->entity_name),
            'fields' => $this->list_fields,
        ];
        //dd($renderVars);
//        dd($renderVars['entities']->getItems()[0]->getProfessori()->count());
        $this->tweakRenderVariables($renderVars, 'index');

        if($request->get('excel-export')){
            return $this->listExcelExport($request, $query, $renderVars);
        }
        //dd($renderVars);
        return $this->render($this->templates_path . '/index.html.twig', $renderVars);
    }

    /**
     * @Route("/ajax", name="_ajax_list", options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function AjaxList(Request $request)
    {
        $response = [];
        $repository = $this->getDoctrine()->getManager()->getRepository($this->getFullEntityNamespace());

        if (method_exists($repository, 'findByTermForAutocompletion')) {
            $response = $repository->findByTermForAutocompletion($request->get('term'), (bool)$request->get('filtermode'));
        }

        return new JsonResponse($response);
    }

    private function _stringContain($string, $searchTerm)
    {
        return $searchTerm ? strpos($string, $searchTerm) !== false : true;
    }

    protected function redirectToShow($entity): Response
    {
        $queryStringsFromReferrer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
        $link = $this->generateUrl($this->route_prefix . '_show', array_merge(array('id' => $entity->getId()), $this->route_params));

        if (!empty($queryStringsFromReferrer)) {
            $link .= '?' . $queryStringsFromReferrer;
        }

        return $this->redirect($link);
    }

    /**
     * @Route("/{id}/show", name="_show")
     * @param $id
     * @return RedirectResponse|Response
     */
    public function show($id)
    {
        $this->preExecute();

        $entity = $this->getRepository()->find($id);
        //dd($entity->getInterventions()->first());
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'L\'elemento non è stato trovato!');
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        // security check
        if (false === $this->isGranted(AdminVoter::VIEW, $entity)) {
            $this->get('session')->getFlashBag()->add('error', 'Non hai i permessi per visualizzare questo ' . $this->singular_name);
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        $this->preRender($entity, 'show');

        $renderVars = [
            'entity' => $entity,
            'configuration' => $this->getConfiguration(),
            'blocks' => $this->show_blocks,
            'new_dummy_entity' => $this->getNewEntity(),
        ];

        //dd($this->templates_path . '/show.html.twig', $renderVars);
        $this->tweakRenderVariables($renderVars, 'show');
        //dd($renderVars['entity']);
//        dd($this->templates_path . '/show.html.twig');
        return $this->render($this->templates_path . '/show.html.twig', $renderVars);
    }

    /**
     * Creates a form from an entity.
     *
     * @param  $entity The entity
     *
     * @param Request|null $request
     * @param array $options
     * @return Form The form
     */
    protected function createFormWithCustomOptions($entity, Request $request = null, Array $options = array())
    {
        $entityType = $this->getNewEntityType();

        $defaultOptions = [
            'action' => $this->generateUrl($request->attributes->get('_route'), array_merge(array('id' => $entity->getId()), $this->route_params)),
            'method' => is_null($entity->getId()) ? 'POST' : 'PUT',
            'fields_map' => $this->fields_map,
        ];

        $form = $this->createForm($entityType, $entity, $options + $defaultOptions);

        return $form;
    }

    protected function onPostPersist(Request $request, $entity, $redirect_to_show, $extraData = []): ?Response
    {
        return null;
    }

    /**
     * @Route("/new", name="_new", options={"expose"=true})
     * @param Request $request
     * @param bool $redirect_to_show
     * @return RedirectResponse|Response
     */
    public function new(Request $request, $redirect_to_show = true)
    {
        $this->preExecute();//null

        $entity = $this->getNewEntity();
        //dd($entity);
        $this->setDefaultsForEntity($entity, 'new', $request);

        $form = $this->createFormWithCustomOptions($entity, $request);

        $form->handleRequest($request);

        // security check
        if (!$this->isGranted(AdminVoter::CREATE, $entity)) {
            $this->get('session')->getFlashBag()->add('error', 'Non hai i permessi per creare un ' . $this->singular_name);
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
//            dd($entity);
            $this->get('session')->getFlashBag()->add('success', 'L\'elemento è stato creato!');

            $response = $this->onPostPersist($request, $entity, $redirect_to_show);

            if($response)
                return $response;

            $saveAndNew = $request->request->get('save-and-new', null);
            if (isset($saveAndNew) && $saveAndNew === '1') {
                return $this->redirect($request->getRequestUri());
            }

            if ($redirect_to_show) {
                return $this->redirectToShow($entity);
            }

            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->get('session')->getFlashBag()->add('error', 'L\'elemento non è stato creato, correggi gli errori');
        }

        $this->preRender($entity, 'new');

        $renderVars = [
            'entity' => $entity,
            'entity_form' => $form->createView(),
            'form_errors' => $this->form_errors->getArray($form),
            'configuration' => $this->getConfiguration(),
            'blocks' => !empty($this->new_blocks) ? $this->new_blocks : $this->edit_blocks,
        ];

        $this->tweakRenderVariables($renderVars, 'new');
        //dd($renderVars);
        return $this->render($this->templates_path . '/new.html.twig', $renderVars);
    } //contact_flock/Contacts/Person/new.html.twig
    // extends '@SinervisAdmin/crud/new.html.twig'

    protected function onPostUpdate(Request $request, $entity, $redirect_to_show = false, $extraData = []): ?Response
    {
        return null;
    }

    /**
     * @Route("/{id}/edit", name="_edit")
     * @param Request $request
     * @param $id
     * @param bool $redirect_to_show
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function edit(Request $request, $id, $redirect_to_show = true)
    {
        $this->preExecute();

        $entity = $this->getRepository()->find($id);
        $entity->setUpdatedAt(new \DateTime());
//        dd($entity);
        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'L\'elemento non è stato trovato!');
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        // security check
        if (false === $this->isGranted(AdminVoter::EDIT, $entity)) {
            $this->get('session')->getFlashBag()->add('error', 'Non hai i permessi per modificare questo ' . $this->singular_name);
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        }

        $this->setDefaultsForEntity($entity, 'edit', $request);

        $editForm = $this->createFormWithCustomOptions($entity, $request);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            $entity = $this->getRepository()->find($entity->getId());

            $this->get('session')->getFlashBag()->add('success', 'L\'elemento è stato salvato!');

            $response = $this->onPostUpdate($request, $entity, $redirect_to_show);
            if($response)
                return $response;

            if ($redirect_to_show) {
                return $this->redirectToShow($entity);
            }

            return $this->redirect($this->generateUrl($this->route_prefix . '_edit',
                array_merge(array('id' => $entity->getId()), $this->route_params)));
        }

        $this->preRender($entity, 'edit');

        if ($editForm->isSubmitted() && !$editForm->isValid()) {
            $this->get('session')->getFlashBag()->add('error', 'L\'elemento non è stato salvato, correggi gli errori');
        }

        $renderVars = [
            'entity' => $entity,
            'entity_form' => $editForm->createView(),
            'form_errors' => $this->form_errors->getArray($editForm),
            'configuration' => $this->getConfiguration(),
            'blocks' => $this->edit_blocks,
        ];

        $this->tweakRenderVariables($renderVars, 'edit');
//        dd($renderVars);
        return $this->render($this->templates_path . '/edit.html.twig', $renderVars);
    }

    protected function onPostDelete(Request $request, $entity, $extraData = []): ?Response
    {
        return null;
    }

    /**
     * @Route("/{id}/delete", name="_delete")
     * @param Request $request
     * @param $id
     * @return Response|RedirectResponse
     */
    public function delete(Request $request, $id)
    {
        $this->preExecute();

        if (!$this->isCsrfTokenValid($this->entity_name . $id, $request->query->get('token'))) {
            $this->get('session')->getFlashBag()->add('error', 'Token non valido');
            return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
        } else {
            $em = $this->getDoctrine()->getManager();
            $entity = $this->getRepository()->find($id);

            if (!$entity) {
                $this->get('session')->getFlashBag()->add('error', 'L\'elemento non è stato trovato!');
                return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
            }

            // security check
            if (false === $this->isGranted(AdminVoter::DELETE, $entity)) {
                $this->get('session')->getFlashBag()->add('error', 'Non hai i permessi per eliminare questo ' . $this->singular_name);
                return $this->redirect($this->generateUrl($this->route_prefix, $this->route_params));
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'L\'elemento è stato eliminato');

            $response = $this->onPostDelete($request, $entity);
            if($response)
                return $response;

            $httpReferer = $request->headers->get('referer'); // origin url

            if ($httpReferer && stripos($httpReferer, '/show') === false && stripos($httpReferer, '/edit') === false) {
                $redirectUrl = $httpReferer;
            } else {
                $redirectUrl = $this->generateUrl($this->route_prefix, $this->route_params);
            }

            return $this->redirect($redirectUrl);
        }
    }
}