<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 09/09/14
 * Time: 12.54
 */

namespace App\Service\AdminFlock;

use App\Event\AdminFlock\ConfigureMenuEvent;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuBuilder
{
    private $factory;
    private $authChecker;
    private $eventDispatcher;
    private $container;
    private $icons;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authChecker, EventDispatcherInterface $eventDispatcher, Container $container)
    {
        $this->factory = $factory;
        $this->authChecker = $authChecker;
        $this->eventDispatcher = $eventDispatcher;
        $this->container = $container;
        $this->icons = (object)$this->container->getParameter('icons');
    }

    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        $menu
            ->addChild('NavHeader', [
                'uri' => '#',
                'extras' => array('icon' => $this->icons->home, 'orderNumber' => -2000),
            ])
            ->setAttributes(array(
                'class' => 'nav-header',
                'path_to_logo' => 'build/images/sinervis/iot_system_logo.png',
                'path_to_short_logo' => 'build/images/sinervis/iot_system_short_logo.png'
            ));

        $menu->addChild('Home', [
            'route' => 'app_homepage',
            'extras' => array('icon' => $this->icons->home, 'orderNumber' => -1000)
        ]);

        if ($this->authChecker->isGranted('IS_AUTHENTICATED_FULLY') || $this->authChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->eventDispatcher->dispatch(new ConfigureMenuEvent($this->factory, $menu, $this->authChecker), ConfigureMenuEvent::CONFIGURE);
        }

        $this->reorderMenuItems($menu);

        return $menu;
    }

    private function reorderMenuItems($menu)
    {

        $menuOrderArray = array();

        $addLast = array();

        $alreadyTaken = array();

        foreach ($menu->getChildren() as $key => $menuItem) {

            if ($menuItem->hasChildren()) {
                $this->reorderMenuItems($menuItem);
            }

            $orderNumber = $menuItem->getExtra('orderNumber');

            if ($orderNumber != null) {
                if (!isset($menuOrderArray[$orderNumber])) {
                    $menuOrderArray[$orderNumber] = $menuItem->getName();
                } else {
                    $alreadyTaken[$orderNumber] = $menuItem->getName();
                    // $alreadyTaken[] = array('orderNumber' => $orderNumber, 'name' => $menuItem->getName());
                }
            } else {
                $addLast[] = $menuItem->getName();
            }
        }

        // sort them after first pass
        ksort($menuOrderArray);

        // handle position duplicates
        if (count($alreadyTaken)) {
            foreach ($alreadyTaken as $key => $value) {
                // the ever shifting target
                $keysArray = array_keys($menuOrderArray);

                $position = array_search($key, $keysArray);

                if ($position === false) {
                    continue;
                }

                $menuOrderArray = array_merge(array_slice($menuOrderArray, 0, $position), array($value), array_slice($menuOrderArray, $position));
            }
        }

        // sort them after second pass
        ksort($menuOrderArray);

        // add items without ordernumber to the end
        if (count($addLast)) {
            foreach ($addLast as $key => $value) {
                $menuOrderArray[] = $value;
            }
        }

        if (count($menuOrderArray)) {
            $menu->reorderChildren($menuOrderArray);
        }
    }
}