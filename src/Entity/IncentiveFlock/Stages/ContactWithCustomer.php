<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Entity\IncentiveFlock\Intervention;
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

    /**
     * @ORM\OneToOne(targetEntity=Intervention::class, mappedBy="contactWithCustomer")
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

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): self
    {
        // unset the owning side of the relation if necessary
        if ($intervention === null && $this->intervention !== null) {
            $this->intervention->setContactWithCustomer(null);
        }

        // set the owning side of the relation if necessary
        if ($intervention !== null && $intervention->getContactWithCustomer() !== $this) {
            $intervention->setContactWithCustomer($this);
        }

        $this->intervention = $intervention;

        return $this;
    }
}
