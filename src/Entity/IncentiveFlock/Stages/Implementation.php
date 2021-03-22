<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Repository\IncentiveFlock\Stages\ImplementationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImplementationRepository::class)
 */
class Implementation extends Stage
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
    private $usedMaterialTechnicalForm;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $providerOffers;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $previousPhotos;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $photosDuringWork;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $photosAfterWork;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $safetyDocumentation;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $buildingSiteDocumentation;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $endOfWork;

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

    public function getUsedMaterialTechnicalForm(): ?Document
    {
        return $this->usedMaterialTechnicalForm;
    }

    public function setUsedMaterialTechnicalForm(?Document $usedMaterialTechnicalForm): self
    {
        $this->usedMaterialTechnicalForm = $usedMaterialTechnicalForm;

        return $this;
    }

    public function getProviderOffers(): ?Document
    {
        return $this->providerOffers;
    }

    public function setProviderOffers(?Document $providerOffers): self
    {
        $this->providerOffers = $providerOffers;

        return $this;
    }

    public function getPreviousPhotos(): ?Document
    {
        return $this->previousPhotos;
    }

    public function setPreviousPhotos(?Document $previousPhotos): self
    {
        $this->previousPhotos = $previousPhotos;

        return $this;
    }

    public function getPhotosDuringWork(): ?Document
    {
        return $this->photosDuringWork;
    }

    public function setPhotosDuringWork(?Document $photosDuringWork): self
    {
        $this->photosDuringWork = $photosDuringWork;

        return $this;
    }

    public function getPhotosAfterWork(): ?Document
    {
        return $this->photosAfterWork;
    }

    public function setPhotosAfterWork(?Document $photosAfterWork): self
    {
        $this->photosAfterWork = $photosAfterWork;

        return $this;
    }

    public function getSafetyDocumentation(): ?Document
    {
        return $this->safetyDocumentation;
    }

    public function setSafetyDocumentation(?Document $safetyDocumentation): self
    {
        $this->safetyDocumentation = $safetyDocumentation;

        return $this;
    }

    public function getBuildingSiteDocumentation(): ?Document
    {
        return $this->buildingSiteDocumentation;
    }

    public function setBuildingSiteDocumentation(?Document $buildingSiteDocumentation): self
    {
        $this->buildingSiteDocumentation = $buildingSiteDocumentation;

        return $this;
    }

    public function getEndOfWork(): ?Document
    {
        return $this->endOfWork;
    }

    public function setEndOfWork(?Document $endOfWork): self
    {
        $this->endOfWork = $endOfWork;

        return $this;
    }
}
