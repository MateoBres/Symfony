<?php

namespace App\EventListener\AdminFlock;

use App\Event\AdminFlock\ConfigureMenuEvent;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseConfigureMenuListener
{
    /**
     * @var Container
     */
    protected $container;
    protected $icons;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->icons = (object)$this->container->getParameter('icons');
    }

    abstract public function onMenuConfigure(ConfigureMenuEvent $event);

    protected function addCreateMenuItem($menu, $baseRouteName)
    {
//        $menu->addChild('Crea', [
//            'route' => "{$baseRouteName}_new",
//            'extras' => array('icon' => 'icon-plus'),
//        ]);
    }

}