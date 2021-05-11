<?php

namespace App\Entity\IncentiveFlock;

use App\Entity\AdminFlock\TimestampableInterface;
use App\Entity\AdminFlock\TimestampableTrait;
use App\Repository\IncentiveFlock\RealEstateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RealEstateRepository::class)
 */
class RealEstate implements TimestampableInterface
{
    use TimestampableTrait;

    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=127)
     * @Assert\NotBlank(message="Comune non dovrebbe essere vuoto.")
     */
    private $municipality;

    /**
     * @ORM\Column(type="string", length=127)
     * @Assert\NotBlank(message="Sezione non dovrebbe essere vuoto.")
     */
    private $section;

    /**
     * @ORM\Column(type="string", length=127)
     * @Assert\NotBlank(message="Foglio non dovrebbe essere vuoto.")
     */
    private $paper;

    /**
     * @ORM\Column(type="string", length=127)
     * @Assert\NotBlank(message="Particella non dovrebbe essere vuoto.")
     */
    private $particle;

    /**
     * @ORM\OneToOne(targetEntity=Intervention::class, mappedBy="realEstate", cascade={"persist", "remove"})
     */
    private $intervention;



    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    public function __toString():string
    {
        return $this->getParticle();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMunicipality(): ?string
    {
        return $this->municipality;
    }

    public function setMunicipality(string $municipality): self
    {
        $this->municipality = $municipality;

        return $this;
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setSection(string $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getPaper(): ?string
    {
        return $this->paper;
    }

    public function setPaper(string $paper): self
    {
        $this->paper = $paper;

        return $this;
    }

    public function getParticle(): ?string
    {
        return $this->particle;
    }

    public function setParticle(string $particle): self
    {
        $this->particle = $particle;

        return $this;
    }

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(Intervention $intervention): self
    {
        // set the owning side of the relation if necessary
        if ($intervention->getRealEstate() !== $this) {
            $intervention->setRealEstate($this);
        }

        $this->intervention = $intervention;

        return $this;
    }
}
