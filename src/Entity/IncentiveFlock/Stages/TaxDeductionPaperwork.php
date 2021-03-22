<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Repository\IncentiveFlock\Stages\TaxDeductionPaperworkRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaxDeductionPaperworkRepository::class)
 */
class TaxDeductionPaperwork extends Stage
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
    private $portalPaperworkOfENEA;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $otherPortalPaperwork;

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

    public function getPortalPaperworkOfENEA(): ?Document
    {
        return $this->portalPaperworkOfENEA;
    }

    public function setPortalPaperworkOfENEA(?Document $portalPaperworkOfENEA): self
    {
        $this->portalPaperworkOfENEA = $portalPaperworkOfENEA;

        return $this;
    }

    public function getOtherPortalPaperwork(): ?Document
    {
        return $this->otherPortalPaperwork;
    }

    public function setOtherPortalPaperwork(?Document $otherPortalPaperwork): self
    {
        $this->otherPortalPaperwork = $otherPortalPaperwork;

        return $this;
    }
}
