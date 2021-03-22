<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Repository\IncentiveFlock\Stages\AssignmentOfClaimRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AssignmentOfClaimRepository::class)
 */
class AssignmentOfClaim extends Stage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $documentsOfAssignmentOfClaim;

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

    public function getDocumentsOfAssignmentOfClaim(): ?Document
    {
        return $this->documentsOfAssignmentOfClaim;
    }

    public function setDocumentsOfAssignmentOfClaim(?Document $documentsOfAssignmentOfClaim): self
    {
        $this->documentsOfAssignmentOfClaim = $documentsOfAssignmentOfClaim;

        return $this;
    }
}
