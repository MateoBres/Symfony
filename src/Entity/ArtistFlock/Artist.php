<?php

namespace App\Entity\ArtistFlock;

use App\Entity\AdminFlock\TimestampableInterface;
use App\Entity\AdminFlock\TimestampableTrait;
use App\Repository\ArtistFlock\ArtistRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Artist
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\ArtistFlock\ArtistRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"Artist" = "Artist",
 *   "Painter" = "App\Entity\ArtistFlock\Artists\Painter",
 *   "Sculptor" = "App\Entity\ArtistFlock\Artists\Sculptor" *
 * })
 * @ORM\EntityListeners({"App\EventListener\ArtistFlock\ArtistListener2"})
 * @ORM\HasLifecycleCallbacks()
 */

//
abstract class Artist implements TimestampableInterface
{

    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $speciality;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullName;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName)
    {
        $this->firstName = ucwords(strtolower($firstName));

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = ucwords(strtolower($lastName));

        return $this;
    }

    public function getSpeciality(): ?string
    {
        return $this->speciality;
    }

    public function setSpeciality(string $speciality): self
    {
        $this->speciality = ucfirst(strtolower($speciality));

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }


    public function setFullName(): self
    {
        $this->fullName = $this->getFirstName().' '.$this->getLastName();

        return $this;
    }
}
