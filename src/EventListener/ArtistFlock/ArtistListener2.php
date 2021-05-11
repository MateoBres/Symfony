<?php


namespace App\EventListener\ArtistFlock;

use App\Entity\ArtistFlock\Artist;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ArtistListener2
{

    public function preUpdate(Artist $artist, LifecycleEventArgs $event): void
    {
        $artist->setFullName();
    }
}