<?php

namespace App\EventListener\AlumnoFlock;

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

        $menu->addChild('Alumni', [
            'route' => 'alumno_flock_alumno',
            'extras' => [
                'orderNumber' => 50,
                'icon' => 'fas fa-users'
            ],
        ]);
    }
}