<?php

namespace App\EventListener\ArtistFlock;

use App\Entity\ArtistFlock\Artist;
use App\Entity\ProfessorFlock\Professor;
use App\Entity\StudentFlock\Student;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ArtistListener
{
    public function prePersist(LifecycleEventArgs $args) :void
    {
        $entity = $args->getObject();
        if($entity instanceof Artist || $entity instanceof Student || $entity instanceof Professor){
            $this->handleEvent($entity);
        }
    }

    public function preUpdate(LifecycleEventArgs $args) :void
    {
        $entity = $args->getObject();
        if($entity instanceof Artist || $entity instanceof Student || $entity instanceof Professor){
            $this->handleEvent($entity);
        }
    }

    public function handleEvent($artist)
    {
        $artist->setFullName();
    }




}