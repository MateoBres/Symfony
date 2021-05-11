<?php

namespace App\EventListener\SettingsFlock;

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

        if ($event->getAuthorizationChecker()->isGranted('ROLE_SUPER_ADMIN') || $event->getAuthorizationChecker()->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Impostazioni globali', [
                'route' => 'settings_flock_settings_show_globals',
                'extras' => ['icon' => $this->icons->settings]
            ]);
//            dd($menu['Impostazioni']['Applicazione']);
            $this->addCreateMenuItem($menu['Impostazioni']['Applicazione'], 'settings_flock_settings_edit_globals');
        }
    }
}