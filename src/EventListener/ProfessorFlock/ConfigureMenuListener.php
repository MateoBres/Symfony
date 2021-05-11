<?php

namespace App\EventListener\ProfessorFlock;

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
            'route' => 'professor_flock_professor',
            'extras' => [
                'orderNumber' => 60,
                'icon' => 'fas fa-users'
            ],
        ]);
    }
}