<?php

namespace App\Entity\AlumnoFlock;

use App\Entity\AdminFlock\TimestampableTrait;
use App\Entity\ProfessoreFlock\Professore;
use App\Repository\AlumnoFlock\AlumnoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AlumnoRepository::class)
 */
class Alumno
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
    private $nome;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cognome;

    /**
     * @ORM\ManyToMany(targetEntity=Professore::class, inversedBy="alunni", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */

    private $professori;

    public function __construct()
    {
        $this->professori = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNome() . ' ' . $this->getCognome();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getCognome(): ?string
    {
        return $this->cognome;
    }

    public function setCognome(string $cognome): self
    {
        $this->cognome = $cognome;

        return $this;
    }

    /**
     * @return Collection|Professore[]
     */
    public function getProfessori(): Collection
    {
        return $this->professori;
    }

    public function addProfessori(Professore $professori): self
    {
        if (!$this->professori->contains($professori)) {
            $this->professori[] = $professori;
            $professori->addAlunni($this);
        }

        return $this;
    }

    public function removeProfessori(Professore $professori): self
    {
        if ($this->professori->removeElement($professori)) {
            $professori->removeAlunni($this);
        }

        return $this;
    }
}
