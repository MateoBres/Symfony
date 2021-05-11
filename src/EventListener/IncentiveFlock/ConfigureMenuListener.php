<?php

namespace App\EventListener\IncentiveFlock;

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

        $menu->addChild('Interventi', [
            'route' => 'incentive_flock_intervention',
            'extras' => [
                'orderNumber' => 30,
                'icon' => 'fas fa-tools'
            ],
        ]);
    }
}