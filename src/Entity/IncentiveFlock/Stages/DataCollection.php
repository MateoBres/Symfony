<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Entity\IncentiveFlock\Intervention;
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

    /**
     * @ORM\OneToOne(targetEntity=Intervention::class, mappedBy="dataCollection", cascade={"persist", "remove"})
     */
    private $intervention;

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

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): self
    {
        // unset the owning side of the relation if necessary
        if ($intervention === null && $this->intervention !== null) {
            $this->intervention->setDataCollection(null);
        }

        // set the owning side of the relation if necessary
        if ($intervention !== null && $intervention->getDataCollection() !== $this) {
            $intervention->setDataCollection($this);
        }

        $this->intervention = $intervention;

        return $this;
    }
}
