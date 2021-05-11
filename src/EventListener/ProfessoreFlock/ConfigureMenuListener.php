<?php

namespace App\EventListener\ProfessoreFlock;

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

        $menu->addChild('Professori', [
            'route' => 'professore_flock_professore',
            'extras' => [
                'orderNumber' => 60,
                'icon' => 'fas fa-users'
            ],
        ]);
    }
}