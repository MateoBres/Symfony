<?php

namespace App\EventListener\ArtistFlock;

use App\Entity\ArtistFlock;
use App\EventListener\AdminFlock\BaseConfigureMenuListener;
use App\Event\AdminFlock\ConfigureMenuEvent;
use App\Security\AdminFlock\Authorization\Voter\AdminVoter;

class ConfigureMenuListener extends BaseConfigureMenuListener
{
    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('Artisti', [
            'route' => 'artist_flock_artist',
            'extras' => [
                'icon' => 'fas fa-users',
                'orderNumber' => 70,
                'routes' => [
                    ['pattern' => '(artist_flock)']
                ]
            ]]
        );
//        dd($menu);
        $menu['Artisti']->addChild('Pittori', [
            'uri' => '#',
            'linkAttributes' => ['data-link-level' => 'first'],
            'extras' => ['icon' => 'fas fa-palette'],
            'route' => 'artist_flock_artist_painter',
        ]);
        $menu['Artisti']->addChild('Scultori', [
            'uri' => '#',
            'linkAttributes' => ['data-link-level' => 'first'],
            'extras' => ['icon' => 'fas fa-archway'],
            'route' => 'artist_flock_artist_sculptor',
        ]);
        //dd($menu);

        //$this->addCreateMenuItem($menu['Artisti']['Applicazione'], 'artist_flock_painter');

//        $menu->addChild('Operatori', [
//            'orderNumber' => 20,
//            'route' => 'contact_flock_roles_worker',
//            'extras' => ['icon' => $this->icons->worker],
//        ]);
//        $this->addCreateMenuItem($menu['Contatti']['Operatori'], 'contact_flock_roles_worker');

//        if ($event->getAuthorizationChecker()->isGranted(AdminVoter::LIST_OWNED, new Facility())) {
//            $menu->addChild('Cliniche', [
//                'route' => 'contact_flock_places_facility',
//                'extras' => [
//                    'orderNumber' => 12,
//                    'icon' => $this->icons->facility
//                ],
//            ]);
//        }

    }

}