<?php

namespace App\EventListener\UserFlock;

use App\EventListener\AdminFlock\BaseConfigureMenuListener;
use App\Event\AdminFlock\ConfigureMenuEvent;


class ConfigureMenuListener extends BaseConfigureMenuListener
{
    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        if ($event->getAuthorizationChecker()->isGranted('ROLE_SUPER_ADMIN')) {

            $menu->addChild('Utenti', [
                'route' => 'admin_user',
                'extras' => [
                    'orderNumber' => 11,
                    'icon' => $this->icons->users
                ],
            ]);
        }
    }
}