<?php

namespace App\Entity\IncentiveFlock;

use App\Entity\AdminFlock\TimestampableInterface;
use App\Entity\AdminFlock\TimestampableTrait;
use App\Entity\IncentiveFlock\Stages\ContactWithCustomer;
use App\Entity\IncentiveFlock\Stages\DataCollection;
use App\Entity\TicketFlock\Ticket;
use App\Repository\IncentiveFlock\InterventionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InterventionRepository::class)
 */
class Intervention implements TimestampableInterface
{
    use TimestampableTrait;

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
     * @ORM\OneToOne(targetEntity=RealEstate::class, inversedBy="intervention", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $realEstate;

    /**
     * @ORM\OneToOne(targetEntity=ContactWithCustomer::class, inversedBy="intervention", cascade={"persist", "remove"})
     */
    private $contactWithCustomer;

    /**
     * @ORM\OneToOne(targetEntity=DataCollection::class, inversedBy="intervention", cascade={"persist", "remove"})
     */
    private $dataCollection;

    /**
     * @ORM\ManyToOne(targetEntity=Ticket::class, inversedBy="interventions", cascade={"persist", "remove"})
     */
    private $ticket;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    public function __toString()
    {
        return 'Intervento '. strval($this->getId());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRealEstate(): ?RealEstate
    {
        return $this->realEstate;
    }

    public function setRealEstate(RealEstate $realEstate): self
    {
        $this->realEstate = $realEstate;

        return $this;
    }

    public function getContactWithCustomer(): ?ContactWithCustomer
    {
        return $this->contactWithCustomer;
    }

    public function setContactWithCustomer(?ContactWithCustomer $contactWithCustomer): self
    {
        $this->contactWithCustomer = $contactWithCustomer;

        return $this;
    }

    public function getDataCollection(): ?DataCollection
    {
        return $this->dataCollection;
    }

    public function setDataCollection(?DataCollection $dataCollection): self
    {
        $this->dataCollection = $dataCollection;

        return $this;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }
}
