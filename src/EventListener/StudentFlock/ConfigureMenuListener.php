<?php

namespace App\EventListener\StudentFlock;

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

        $menu->addChild('Alunni', [
            'route' => 'student_flock_student',
            'extras' => [
                'orderNumber' => 50,
                'icon' => 'fas fa-users'
            ],
        ]);
    }
}