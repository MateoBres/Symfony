<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 26/03/15
 * Time: 12.20
 */

namespace App\Twig\AdminFlock;

use App\DBAL\Types\QuestionnaireKindType;
use App\Entity\CommercialFlock\Contracts\Enrollment;
use App\Entity\QuestionnaireFlock\Questionnaire;
use App\Service\AdminFlock\EntityInfoHelper;
use App\Service\AdminFlock\RouteHelper;
use App\Service\SettingsFlock\SettingsManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use App\Security\AdminFlock\Authorization\Voter\AdminVoter;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;

class AdminTwigExtension extends AbstractExtension
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $auth_checker;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $token_generator;

    /**
     * @var RouteHelper
     */
    private $route_helper;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityInfoHelper
     */
    private $entityInfoHelper;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var SettingsManager
     */
    private $settingsManager;

    public function __construct(
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $auth_checker,
        RouterInterface $router,
        CsrfTokenManagerInterface $token_generator,
        RouteHelper $route_helper,
        ContainerInterface $container,
        EntityInfoHelper $entityInfoHelper,
        Environment $twig,
        SettingsManager $settingsManager
    )
    {
        $this->em = $em;
        $this->auth_checker = $auth_checker;
        $this->router = $router;
        $this->token_generator = $token_generator;
        $this->route_helper = $route_helper;
        $this->container = $container;
        $this->entityInfoHelper = $entityInfoHelper;
        $this->twig = $twig;
        $this->settingsManager = $settingsManager;

        $this->icons = (object)$this->container->getParameter('icons');
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('link_to_show', array($this, 'linkToShowFunction'), array('is_safe' => array('html'))),
            new TwigFunction('link_to_action_show', array($this, 'actionShowFunction'), array('is_safe' => array('html'))),
            new TwigFunction('link_to_action_edit', array($this, 'actionEditFunction'), array('is_safe' => array('html'))),
            new TwigFunction('link_to_action_compile_questionnaire', array($this, 'actionCompileQuestionnaireFunction'), array('is_safe' => array('html'))),
            new TwigFunction('link_to_action_delete', array($this, 'actionDeleteFunction'), array('is_safe' => array('html'))),
            new TwigFunction('link_to_action_cancel', array($this, 'actionCancelFunction'), array('is_safe' => array('html'))),
            new TwigFunction('print_file_icon', array($this, 'printFileIconFunction'), array('is_safe' => array('html'))),
            new TwigFunction('print_contact_role_by_user_name', array($this, 'printContactRoleByUserNameFunction'), array('is_safe' => array('html'))),
            new TwigFunction('has_permission_on_obj', array($this, 'hasPermissionOnObjFunction')),
            new TwigFunction('has_field_permission', array($this, 'hasFieldPermissionFunction')),
            new TwigFunction('get_entity_class', array($this, 'getEntityClassFunction')),
            new TwigFunction('array_unique', array($this, 'arrayUniqueFunction')),
            new TwigFunction('route_exists', array($this, 'routeExistsFunction')),
            new TwigFunction('get_new_instance', array($this, 'getNewInstanceFunction')),
            new TwigFunction('get_route_by_entity', array($this, 'getRouteByEntityFunction')),
            new TwigFunction('instance_of', array($this, 'instanceOfFunction')),
            new TwigFunction('set_array_element', array($this, 'setArrayElementFunction')),
            new TwigFunction('get_global_setting', array($this, 'getGlobalSetting')),
            new TwigFunction('get_user_setting', array($this, 'getUserSetting')),
            new TwigFunction('get_user_setting', array($this, 'getEntityIconFunction')),
            new TwigFunction('get_entity_icon', array($this, 'getEntityIconFunction')),
            new TwigFunction('get_entity_description', array($this, 'getEntityDescriptionFunction')),
            new TwigFunction('get_route_token', array($this, 'getRouteToken')),
        );
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('to_autocomplete', array($this, 'toAutocompleteFilter')),
            new TwigFilter('to_patterned_autocomplete', array($this, 'toPatternedAutocompleteFilter')),
            new TwigFilter('secToHoursMins', array($this, 'secToHoursMinsFilter')),
            new TwigFilter('sv_boolean', array($this, 'svBooleanFilter')),
            new TwigFilter('sv_currency', array($this, 'svCurrencyFilter')),
            new TwigFilter('sv_percentage', array($this, 'svPercentageFilter')),
            new TwigFilter('sv_decimal', array($this, 'svDecimalFilter')),
            new TwigFilter('sv_duration', array($this, 'svDurationFilter')),
            new TwigFilter('addslashes', array($this, 'addslashesFilter')),
            new TwigFilter('wipe_me', array($this, 'wipeMeFilter')),
            new TwigFilter('lcfirst', array($this, 'lcfirstFilter')),
            new TwigFilter('apply_filter', array($this, 'applyFilter'), array('needs_environment' => true, 'needs_context' => true)),
            new TwigFilter('numberToWord', array($this, 'numberToWordFilter')),
            new TwigFilter('labelizeCertificateStatus', array($this, 'labelizeCertificateStatusFilter')),
            new TwigFilter('sortby', array($this, 'sortByFilter')),
            new TwigFilter('date', array($this, 'dateFilter')),
            new TwigFilter('pad', array($this, 'padFilter')),
            new TwigFilter('userPermission', array($this, 'userPermissionFilter')),
        );
    }

    /**
     * @param $object
     * @param array $options
     * @return string
     */
    public function linkToShowFunction($object, $options = array())
    {
        if (!$object || !is_object($object))
            return '';

        // options
        $getter = isset($options['getter']) && method_exists($object, $options['getter']) ? $options['getter'] : '__toString';
        $route_params = isset($options['route_params']) ? $options['route_params'] : array();

        if (isset($options['short_name']) && $options['short_name'] && method_exists($object, 'getShortName')) {
            $getter = 'getShortName';
        }

        // get value to display
        $value = $object->$getter();
        $longValue = $object->__toString();

        // check permission
        if (!$this->auth_checker->isGranted(AdminVoter::VIEW, $object))
            return $value; // the user has no permission to follow this link, return only object value

        $html = $value;
        $route = $options['forced_route'] ?? $this->route_helper->getRouteName($object, 'show');

        $icon = $this->entityInfoHelper->getEntityIcon($object);
        $title = $this->entityInfoHelper->getEntityDescription($object);

        if ($route) // add link only if route is defined in $this->data array
        {
            $html = '<a class="link-to" data-toggle="tooltip" title="' . ($title ? 'Vai a <b>' . $title . '</b>' . ($getter == 'getShortName' ? ':<br>' . $longValue : '') : '') . '" href="' . $this->router->generate($route, array_merge($route_params, array('id' => $object->getId()))) . '">' . ($icon ? $icon . ' ' : '') . $value . '</a>';
        }

        return $html;
    }

    public function getEntityIconFunction($object)
    {
        return $this->entityInfoHelper->getEntityIcon($object);
    }

    public function getEntityDescriptionFunction($object)
    {
        return $this->entityInfoHelper->getEntityDescription($object);
    }

    public function getRouteToken(string $route)
    {
        return $this->route_helper->getToken($route);
    }

    /**
     * @param $object
     * @param $configuration
     * @param array $options
     * @return string
     */
    public function actionShowFunction($object, $configuration, $options = array())
    {
        if (!$object)
            return '';

        // check permission
        if (!$this->auth_checker->isGranted(AdminVoter::VIEW, $object))
            return '';  // the user has no permission to view this object, return empty string

        // options
        if (!empty($configuration) && isset($configuration['singular_name'])) {
            $name = $configuration['singular_name'];
        } else {
            $name = $options['display_name'] ?: 'dettagli';
        }
        $route_params = isset($options['route_params']) ? $options['route_params'] : array();
        //$route = $this->route_helper->getMainRouteNameFromControllerAnnotation($object);
        $route = $options['forced_route'] ?? $this->route_helper->getRouteName($object, 'show');
        $icon = $options['icon'] ?? 'fas fa-eye';

        $html = '';
        if ($route) // show link only if route is defined in $this->data array
        {
            $html = '<a class="link-to" id="' . $object->getId() . '" href="' . $this->router->generate($route, array_merge($route_params, array('id' => $object->getId()))) . '" rel="tooltip" title="Dettagli ' . $name . '" class="entity-show tabledit-edit-button  waves-effect waves-light"><span class="' . $icon . '"></span></a>';
        }

        return $html;
    }

    private function getEntityActionRoute($configuration, $action)
    {
        return $configuration['route_prefix'] . '_' . $action;
    }

    /**
     * @param $object
     * @param array $options
     * @return string
     */
    public function actionEditFunction($object, $configuration, $options = array())
    {
        if (!$object)
            return '';

        // check permission
        if (!$this->auth_checker->isGranted(AdminVoter::EDIT, $object))
            return '';  // the user has no permission to view this object, return empty string

        // options
        if (!empty($configuration) && isset($configuration['singular_name'])) {
            $name = $configuration['singular_name'];
        } else {
            $name = $options['display_name'] ?: 'dettagli';
        }
        $route_params = isset($options['route_params']) ? $options['route_params'] : array();
        //$route = $this->route_helper->getMainRouteNameFromControllerAnnotation($object);
        $route = $options['forced_route'] ?? $this->route_helper->getRouteName($object, 'edit');

        $html = '';
        if ($route) {
            $html = '<a class="link-to" id="' . $object->getId() . '" href="' . $this->router->generate($route, array_merge($route_params, array('id' => $object->getId()))) . '" rel="tooltip" title="Modifica ' . $name . '" class="entity-edit tabledit-edit-button  waves-effect waves-light"><span class="fas fa-pencil-alt"></span></a>';
        }

        return $html;
    }

    /**
     * @param $object
     * @param array $options
     * @return string
     */
    public function actionCompileQuestionnaireFunction($object, $options = array())
    {
        if (!$object)
            return '';

        // check permission
        if (!$this->auth_checker->isGranted(AdminVoter::EDIT, $object))
            return '';  // the user has no permission to view this object, return empty string

        $route_params = isset($options['route_params']) ? $options['route_params'] : array();
        $class = isset($options['class']) ? $options['class'] : '';
        $icon = isset($options['icon']) ? $options['icon'] : '';
        $label = isset($options['label']) ? $options['label'] : '';
        $type = isset($options['type']) ? $options['type'] : QuestionnaireKindType::COURSE_ASSESSMENT;

        $completedQuestionnaireClass = QuestionnaireKindType::getCompletedQuestionnaireEntityClass($type);
        $completedQuestionnaireDummy = new $completedQuestionnaireClass();
        $route = $options['forced_route'] ?? $this->route_helper->getRouteName($completedQuestionnaireDummy, 'create');

        $html = '';
        if ($route) {
            $html = '<a class="' . $class . '" id="' . $object->getId() . '" href="' . $this->router->generate($route, array_merge($route_params, array('id' => $object->getId()))) . '" rel="tooltip" title="Compila questionario"><span class="' . $icon . '"></span>' . ($label ? ' ' . $label : '') . '</a>';
        }

        return $html;
    }

    /**
     * @param $object
     * @param array $options
     * @return string
     */
    public function actionDeleteFunction($object, $configuration, $options = array())
    {
        if (!$object)
            return '';

        // check permission
        if (!$this->auth_checker->isGranted(AdminVoter::DELETE, $object))
            return '';  // the user has no permission to view this object, return empty string

        // options
        $name = $configuration['singular_name'] ?: $options['display_name'];
        $route_params = isset($options['route_params']) ? $options['route_params'] : array();
        list($bundleName, $entityName) = explode('\\Entity\\', get_class($object));

        if (isset($options['entity_name'])) {
            $entityName = $options['entity_name'];
        }

        if (isset($options['bundle_name'])) {
            $bundleName = $options['bundle_name'];
        }

        //$route = $this->route_helper->getMainRouteNameFromControllerAnnotation($object);
        $route = $options['forced_route'] ?? $this->route_helper->getRouteName($object, 'delete');
        $entityName = $options['forced_entity_name'] ?? $configuration['entity_name'];

        $disabled = isset($options['disabled']) && $options['disabled'] == true ? 'disabled' : '';
        $html = '';
        if ($route) {
            $token = $this->token_generator->refreshToken($entityName . $object->getId());
            $deleteUrl = $this->router->generate($route, array_merge($route_params, array('id' => $object->getId(), 'token' => $token->getValue())));
            $html = '<a id="' . $object->getId() . '" rel="tooltip" href="' . $deleteUrl . '" data-method="POST" title="Elimina ' . $name . '"class="delete-entity danger tabledit-edit-button waves-effect waves-light ' . $disabled . '"><span class="fas fa-trash"></span></a>';
        }

        return $html;
    }

    /**
     * @param $object
     * @param array $options
     * @return string
     */
    public function actionCancelFunction($object, $configuration, $options = array())
    {
        if (!$object)
            return '';

        // check permission
        if (!$this->auth_checker->isGranted(AdminVoter::EDIT, $object))
            return '';  // the user has no permission to edit this object, return empty string

        // options
        $name = $configuration['singular_name'] ?: $options['display_name'];
        $route_params = isset($options['route_params']) ? $options['route_params'] : array();

        //$route = $this->route_helper->getMainRouteNameFromControllerAnnotation($object);
        $route = $this->route_helper->getRouteName($object, 'cancel');

        $html = '';
        if ($route) // cancel link only if route is defined in $this->data array
        {
            if ($object->getState() == 1) {
                $html = '<span class="glyphicon glyphicon-ban-circle" rel="tooltip" title="Anulla disabilitato"></span>';
            } else {
                if ($object->getState() == 4) {
                    $action = 'restore';
                    $actionLabel = 'Ripristina ';
                    $icon = 'glyphicon-backward';
                } else {
                    $action = 'cancel';
                    $actionLabel = 'Annulla ';
                    $icon = 'glyphicon-ban-circle';
                }

                $html = '<a href="' . $this->router->generate($route, array_merge($route_params, array('id' => $object->getId(), 'action' => $action))) . '" rel="tooltip" title="' . $actionLabel . $name . '"><span class="glyphicon ' . $icon . '"></span></a>';
            }
        }

        return $html;
    }

    /**
     * @param null $element
     * @param string $class
     * @return string
     */
    public function printFileIconFunction($element = null, $class = '')
    {
        if (!is_object($element)) {
            $path_parts = pathinfo($element);
            $extension = empty($path_parts['extension']) ? $path_parts['basename'] : $path_parts['extension'];
            switch ($extension) {
                case 'jpg':
                case 'png':
                    return '<i class="' . $class . ' fa fa-file-image-o"></i>';

                case 'txt':
                    return '<i class="' . $class . ' fa fa-file-text-o"></i>';

                case 'xls':
                    return '<i class="' . $class . ' fa fa-file-excel-o"></i>';

                case 'doc':
                    return '<i class="' . $class . ' fa fa-file-word-o"></i>';

                case 'pdf':
                    return '<i class="' . $class . ' fa fa-file-pdf-o"></i>';

                case 'mp4':
                case 'wma':
                    return '<i class="' . $class . ' fa fa-file-video-o"></i>';

                default:
                    return '<i class="' . $class . ' fa fa-file"></i>';
            }
        }

        return '';
    }

    public function arrayUniqueFunction($arr)
    {
        $items = new ArrayCollection();
        foreach ($arr as $key => $item) {
            if (!$items->contains($item)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param $userName
     * @param array $options
     * @return string
     */
    public function printContactRoleByUserNameFunction($userName = '', $options = array())
    {
        $user = $this->em->getRepository('SinervisUserBundle:User')->getUserByUserName($userName);

        if ($user) {
            $contact = $user->getContact();
            if ($contact && count($contact->getRoles()) == 1) {
                $roleName = $contact->getRoles()->first()->getRoleName();
                $outputFormat = '<i class="fa fa-lg %s fa-fw" rel="tooltip" title="%s - %s">';

                if ($roleName == 'Cliente') {
                    return sprintf($outputFormat, 'fa-user', $roleName, $userName);
                } elseif ($roleName == 'Fornitore') {
                    return sprintf($outputFormat, 'fa-truck', $roleName, $userName);
                }
            }
        }

        return '';
    }

    public function getName()
    {
        return 'admin_twig_extension';
    }

    public function hasPermissionOnObjFunction($object, $opeation)
    {
        if (!$object || !is_object($object)) {
            return False;
        }

        return $this->auth_checker->isGranted(
            constant('App\Security\AdminFlock\Authorization\Voter\AdminVoter::' . $opeation),
            $object
        );
    }

    public function hasFieldPermissionFunction($object, $fieldName, $fieldConf)
    {
        //$permissionGranted = true;
        $permissionGranted = $this->auth_checker->isGranted(AdminVoter::VIEW, $object);

        if (array_key_exists('permission', $fieldConf)) {
            $permissionGranted = $this->auth_checker->isGranted('ROLE_ADMIN') || $this->auth_checker->isGranted($fieldConf['permission'], $object);
        }

        return $permissionGranted;
    }

    public function getEntityClassFunction($object, $mode = 'short')
    {
        if ($object) {
            if ($mode == 'complete') {
                return get_class($object);
            }

            $entityName = explode('\\Entity\\', get_class($object))[1];

            if ($mode == 'short') {
                $nameArr = explode('\\', $entityName);
                return array_pop($nameArr);
            } else {
                return $entityName;
            }
        }
    }

    function routeExistsFunction($name)
    {
        return (null === $this->router->getRouteCollection()->get($name)) ? false : true;
    }

    function getNewInstanceFunction($namespace)
    {
        return new $namespace;
    }

    function getRouteByEntityFunction($entity)
    {
        if (!is_object($entity)) {
            return null;
        }

        return $this->route_helper->getRouteName($entity);
    }

    public function getGlobalSetting(string $property)
    {
        return $this->settingsManager->get($property);
    }

    public function getUserSetting(string $property)
    {
        return $this->settingsManager->getFromUser($property);
    }


    /**
     * FILTER FUNCTIONS
     */

    public function applyFilter(\Twig_Environment $env, $context = array(), $value, $filters)
    {
        $fs = new Filesystem();

        //set the needed path
        $template_dir_path = $env->getCache() . '/apply_filter';
        $template_file_name = $filters . '.html.twig';
        $template_path = $template_dir_path . '/' . $template_file_name;

        //create dir for templates in twig cache
        if (!$fs->exists($template_dir_path))
            $fs->mkdir($template_dir_path);

        if (!$fs->exists($template_path)) {
            //write the new template if first call
            $template = sprintf('{{ value|%s }}', $filters);
            file_put_contents($template_path, $template);
        }

        //store the old loader (not sure that is necessary)
        $old_loader = $env->getLoader();

        //use file loader
        $loader = new \Twig_Loader_Filesystem($template_dir_path);
        $env->setLoader($loader);

        $rendered = $env->render($template_file_name, array("value" => $value));

        //reload the previous loader
        $env->setLoader($old_loader);

        return $rendered;
    }

    public function toAutocompleteFilter($string)
    {
        return '%5Bautocomplete_field%5D=%7B%22label%22%3A%22dummy%22%2C%22value%22%3A' . $string . '%7D';
    }

    public function toPatternedAutocompleteFilter($string)
    {
        return '%5Bautocomplete_field%5D=' . urlencode($string);
        //return '%5Bautocomplete_field%5D=%7B%22label%22%3A%22dummy%22%2C%22value%22%3A'.$string.'%7D';
    }

    public function svBooleanFilter($value)
    {
        return $value ? '<span class="bool-yes">Sì</span>' : '<span class="bool-no">No</span>';
    }

    public function svCurrencyFilter($value)
    {
        if (!is_null($value)) {
            return '€ ' . number_format($value, 2, ',', '.');
        }

        return;
    }

    public function svPercentageFilter($value)
    {
        if (!empty($value)) {
            $value = number_format($value, 2, ',', '.');
            return $value . '%';
        }

        return '';
    }

    public function svDecimalFilter($value)
    {
        return str_replace('.', ',', $value);
    }

    public function svDurationFilter($value)
    {
        return $this->secToHoursMinsFilter($value);
    }

    public function secToHoursMinsFilter($seconds)
    {
        return sprintf("%02d:%02d", floor($seconds / 3600), ($seconds / 60) % 60);
    }

    public function addslashesFilter($value)
    {
        return addslashes($value);
    }

    public function wipeMeFilter($value)
    {
        return '';
    }

    public function lcfirstFilter($value)
    {
        return lcfirst($value);
    }

    public function numberToWordFilter($value)
    {
        if (!is_numeric($value)) {
            return '';
        }

        $f = new \NumberFormatter("it", \NumberFormatter::SPELLOUT);
        return $f->format($value);
    }

    public function instanceOfFunction($entity, $className)
    {
        return get_class($entity) === $className;
    }

    public function setArrayElementFunction($data, $key, $value)
    {
        if (!is_array($data)) {
            return $data;
        }
        $data[$key] = $value;
        return $data;
    }

    public function labelizeCertificateStatusFilter($status)
    {
        return '<span class="">' . $status . '</span>';
    }

    /**
     * @param $content
     * @param null $field
     * @param string $direction
     * @return array
     */
    public function sortByFilter($content, $field = null, $direction = 'ASC')
    {
        if ($content instanceof Collection) {
            $content = $content->toArray();
        }

        if (!is_array($content)) {
            throw new \InvalidArgumentException('Variable passed to the sortBy filter is not an array');
        } elseif (count($content) < 1) {
            return $content;
        } elseif ($field === null) {
            throw new Exception('No sort by parameter passed to the sortBy filter');
        } elseif (!$this->isSortable(current($content), $field)) {
            throw new Exception('Entries passed to the sortBy filter do not have the field "' . $field . '"');
        } else {
            @usort($content, function($a, $b) use ($field, $direction) {
                $flip = (strtolower(trim($direction)) === 'desc') ? -1 : 1;
                return ($this->getValueFromObjectOrArray($a, $field) <=> $this->getValueFromObjectOrArray($b, $field)) * $flip;
            });
        }

        return $content;
    }

    public function padFilter($input, $length, $string = '0', $type = STR_PAD_LEFT)
    {
        return str_pad($input, $length, $string, $type);
    }

    private function getValueFromObjectOrArray($item, $field)
    {
        return (is_array($item) ? $item[$field] :
            (method_exists($item, 'get' . ucfirst($field)) ? $item->{'get' . ucfirst($field)}() : $item->$field)
        );
    }

    private function isSortable($item, $field)
    {
        return (is_array($item) ? array_key_exists($field, $item) :
            (is_object($item) ? isset($item->$field) || property_exists($item, $field) : false)
        );
    }

    public function dateFilter($timestamp, $format = 'F j, Y H:i', $fallback = '')
    {
        $result = $fallback;
        if ($timestamp !== null) {
            return twig_date_format_filter($this->twig, $timestamp, $format);
        }
        return $result;
    }
}
