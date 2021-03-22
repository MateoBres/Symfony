<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Repository\IncentiveFlock\Stages\DataCollectionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DataCollectionRepository::class)
 */
class DataCollection extends Stage
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
    private $condominiumDataCollectionForm;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $singleFamilyDataCollectionForm;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $plan;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $cadastralForm;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $energyDiagnosisDataCollectionForm;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $costCap;

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

    public function getCondominiumDataCollectionForm(): ?Document
    {
        return $this->condominiumDataCollectionForm;
    }

    public function setCondominiumDataCollectionForm(?Document $condominiumDataCollectionForm): self
    {
        $this->condominiumDataCollectionForm = $condominiumDataCollectionForm;

        return $this;
    }

    public function getSingleFamilyDataCollectionForm(): ?Document
    {
        return $this->singleFamilyDataCollectionForm;
    }

    public function setSingleFamilyDataCollectionForm(?Document $singleFamilyDataCollectionForm): self
    {
        $this->singleFamilyDataCollectionForm = $singleFamilyDataCollectionForm;

        return $this;
    }

    public function getPlan(): ?Document
    {
        return $this->plan;
    }

    public function setPlan(?Document $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    public function getCadastralForm(): ?Document
    {
        return $this->cadastralForm;
    }

    public function setCadastralForm(?Document $cadastralForm): self
    {
        $this->cadastralForm = $cadastralForm;

        return $this;
    }

    public function getEnergyDiagnosisDataCollectionForm(): ?Document
    {
        return $this->energyDiagnosisDataCollectionForm;
    }

    public function setEnergyDiagnosisDataCollectionForm(?Document $energyDiagnosisDataCollectionForm): self
    {
        $this->energyDiagnosisDataCollectionForm = $energyDiagnosisDataCollectionForm;

        return $this;
    }

    public function getCostCap(): ?Document
    {
        return $this->costCap;
    }

    public function setCostCap(?Document $costCap): self
    {
        $this->costCap = $costCap;

        return $this;
    }
}
