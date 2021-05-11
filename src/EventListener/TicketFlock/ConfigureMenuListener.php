<?php

namespace App\EventListener\TicketFlock;

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

        $menu->addChild('Ticket', [
            'route' => 'ticket_flock_ticket',
            'extras' => [
                'orderNumber' => 40,
                'icon' => 'fas fa-clipboard-list'
            ],
        ]);
    }
}