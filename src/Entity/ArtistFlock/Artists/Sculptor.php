<?php

namespace App\Entity\ArtistFlock\Artists;

use App\Entity\ArtistFlock\Artist;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArtistFlock\Artists\SculptorRepository")
 */
class Sculptor extends Artist
{


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $material;


    public function getMaterial(): ?string
    {
        return $this->material;
    }

    public function setMaterial(string $material): self
    {
        $this->material = ucfirst(strtolower($material));

        return $this;
    }
}
