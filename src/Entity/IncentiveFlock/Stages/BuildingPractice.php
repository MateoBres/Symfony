<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Repository\IncentiveFlock\Stages\BuildingPracticeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BuildingPracticeRepository::class)
 */
class BuildingPractice extends Stage
{
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
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $buildingPractice;

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

    public function getBuildingPractice(): ?Document
    {
        return $this->buildingPractice;
    }

    public function setBuildingPractice(?Document $buildingPractice): self
    {
        $this->buildingPractice = $buildingPractice;

        return $this;
    }
}
