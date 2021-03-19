<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 09/09/14
 * Time: 13.32
 */

namespace App\Event\AdminFlock;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\EventDispatcher\Event;


class ConfigureMenuEvent extends Event
{
    const CONFIGURE = 'sinervis_admin.menu_configure';

    private $factory;
    private $menu;
    private $authorization_checker;

    /**
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Knp\Menu\ItemInterface $menu
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu = null, AuthorizationCheckerInterface $authorization_checker)
    {
        $this->factory = $factory;
        $this->menu = $menu;
        $this->authorization_checker = $authorization_checker;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return \Symfony\Component\Security\Core\Authorization\AuthorizationChecker
     */
    public function getAuthorizationChecker()
    {
        return $this->authorization_checker;
    }
} 