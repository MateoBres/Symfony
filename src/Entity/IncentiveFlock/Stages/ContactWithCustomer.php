<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Repository\IncentiveFlock\Stages\ContactWithCustomerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContactWithCustomerRepository::class)
 */
class ContactWithCustomer extends Stage
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
    private $estimate;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, cascade={"persist", "remove"})
     */
    private $dutyAssignment;

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

    public function getEstimate(): ?Document
    {
        return $this->estimate;
    }

    public function setEstimate(?Document $estimate): self
    {
        $this->estimate = $estimate;

        return $this;
    }

    public function getDutyAssignment(): ?Document
    {
        return $this->dutyAssignment;
    }

    public function setDutyAssignment(?Document $dutyAssignment): self
    {
        $this->dutyAssignment = $dutyAssignment;

        return $this;
    }
}
