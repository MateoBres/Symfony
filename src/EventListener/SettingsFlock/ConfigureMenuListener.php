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
//            $menu->addChild('Impostazioni', [
//                'uri' => '#',
//                'linkAttributes' => array('data-link-level' => 'first'),
//                'extras' => ['icon' => $this->icons->settings],
//                'route' => 'settings_flock_settings',
//            ]);

//        $menu['Impostazioni']->addChild('Indice', [
//            'route' => 'settings_flock_settings',
//            'extras' => ['icon' => $this->icons->details]
//        ]);
//        $this->addCreateMenuItem($menu['Impostazioni']['Indice'], 'settings_flock_settings');

            $menu->addChild('Impostazioni globali', [
                'route' => 'settings_flock_settings_show_globals',
                'extras' => ['icon' => $this->icons->settings]
            ]);
            $this->addCreateMenuItem($menu['Impostazioni']['Applicazione'], 'settings_flock_settings_edit_globals');
        }
    }
}