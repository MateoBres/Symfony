<?php

namespace App\Entity\IncentiveFlock;

use App\Entity\AdminFlock\TimestampableInterface;
use App\Entity\AdminFlock\TimestampableTrait;
use App\Repository\IncentiveFlock\InterventionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InterventionRepository::class)
 */
class Intervention implements TimestampableInterface
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
     * @ORM\OneToOne(targetEntity=RealEstate::class, inversedBy="intervention", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $realEstate;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRealEstate(): ?RealEstate
    {
        return $this->realEstate;
    }

    public function setRealEstate(RealEstate $realEstate): self
    {
        $this->realEstate = $realEstate;

        return $this;
    }
}
