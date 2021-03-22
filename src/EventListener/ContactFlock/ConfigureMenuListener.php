<?php

namespace App\EventListener\ContactFlock;

use App\Entity\ContactFlock\Places\Facility;
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

        $menu->addChild('Contatti', [
            'route' => 'contact_flock_contact',
            'extras' => [
                'icon' => $this->icons->contacts,
                'orderNumber' => 20,
                'routes' => [
                    ['pattern' => '(contact_flock)'],
                ],
            ],
        ]);

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