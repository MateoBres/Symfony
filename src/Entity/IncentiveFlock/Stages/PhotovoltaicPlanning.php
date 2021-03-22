<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Repository\IncentiveFlock\Stages\PhotovoltaicPlanningRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhotovoltaicPlanningRepository::class)
 */
class PhotovoltaicPlanning extends Stage
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
    private $photovoltaicPlanning;

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

    public function getPhotovoltaicPlanning(): ?Document
    {
        return $this->photovoltaicPlanning;
    }

    public function setPhotovoltaicPlanning(?Document $photovoltaicPlanning): self
    {
        $this->photovoltaicPlanning = $photovoltaicPlanning;

        return $this;
    }
}
