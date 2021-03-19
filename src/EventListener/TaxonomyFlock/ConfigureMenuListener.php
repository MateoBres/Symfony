<?php

namespace App\EventListener\TaxonomyFlock;

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

//        $menu->addChild('Tassonomie', [
//            'uri' => '#',
//            'linkAttributes' => array('data-link-level' => 'first'),
//            'extras' => array('icon' => $this->icons->taxonomy),
//            'route' => 'taxonomy_flock_taxonomy_term_vocabularies',
//        ]);

    }
}