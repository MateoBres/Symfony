<?php

namespace App\Entity\ArtistFlock\Artists;

use App\Entity\ArtistFlock\Artist;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArtistFlock\Artists\PainterRepository")
 */
class Painter extends Artist

{

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tecnic;


    public function getTecnic(): ?string
    {
        return $this->tecnic;
    }

    public function setTecnic(string $tecnic): self
    {
        $this->tecnic = ucfirst(strtolower($tecnic));

        return $this;
    }
}
