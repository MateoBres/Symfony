<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Repository\IncentiveFlock\Stages\TechnicalAssertionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TechnicalAssertionRepository::class)
 */
class TechnicalAssertion extends Stage
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
    private $technicalAssertion;

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

    public function getTechnicalAssertion(): ?Document
    {
        return $this->technicalAssertion;
    }

    public function setTechnicalAssertion(?Document $technicalAssertion): self
    {
        $this->technicalAssertion = $technicalAssertion;

        return $this;
    }
}
