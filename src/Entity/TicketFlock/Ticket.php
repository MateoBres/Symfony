<?php

namespace App\Entity\TicketFlock;

use App\Entity\AdminFlock\TimestampableTrait;
use App\Entity\IncentiveFlock\Intervention;
use App\Repository\TicketFlock\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 */
class Ticket
{

    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tag;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $series;

    /**
     * @ORM\OneToMany(targetEntity=Intervention::class, orphanRemoval=true, mappedBy="ticket", cascade={"persist", "remove"})
     */
    private $interventions;

    public function __construct()
    {
        $this->interventions = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->tag;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getSeries(): ?int
    {
        return $this->series;
    }

    public function setSeries(?int $series): self
    {
        $this->series = $series;

        return $this;
    }

    /**
     * @return Collection|Intervention[]
     */
    public function getInterventions(): Collection
    {   //dd($this->interventions);

        return $this->interventions;
    }

    public function addIntervention(Intervention $intervention): self
    {
        if (!$this->interventions->contains($intervention)) {
            $this->interventions[] = $intervention;
            $intervention->setTicket($this);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): self
    {
        if ($this->interventions->removeElement($intervention)) {
            // set the owning side to null (unless already changed)
            if ($intervention->getTicket() === $this) {
                $intervention->setTicket(null);
            }
        }

        return $this;
    }
}
