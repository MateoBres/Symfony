<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Repository\IncentiveFlock\Stages\ThermotechnicPlanningRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ThermotechnicPlanningRepository::class)
 */
class ThermotechnicPlanning extends Stage
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
    private $energyAudit;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $previous10LawReport;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $thermalDetailPlan;

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

    public function getEnergyAudit(): ?Document
    {
        return $this->energyAudit;
    }

    public function setEnergyAudit(?Document $energyAudit): self
    {
        $this->energyAudit = $energyAudit;

        return $this;
    }

    public function getPrevious10LawReport(): ?Document
    {
        return $this->previous10LawReport;
    }

    public function setPrevious10LawReport(?Document $previous10LawReport): self
    {
        $this->previous10LawReport = $previous10LawReport;

        return $this;
    }

    public function getThermalDetailPlan(): ?Document
    {
        return $this->thermalDetailPlan;
    }

    public function setThermalDetailPlan(?Document $thermalDetailPlan): self
    {
        $this->thermalDetailPlan = $thermalDetailPlan;

        return $this;
    }
}
