<?php

namespace App\Entity\StudentFlock;

use App\Entity\AdminFlock\TimestampableTrait;
use App\Entity\ProfessorFlock\Professor;
use App\Repository\StudentFlock\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 */
class Student
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $surname;

    /**
     * @ORM\ManyToMany(targetEntity=Professor::class, inversedBy="students", cascade={"persist", "remove"})
     */
    private $professors;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullName;

    public function __construct()
    {
        $this->professors = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName().' '.$this->getSurname();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @return Collection|Professor[]
     */
    public function getProfessors(): Collection
    {
        return $this->professors;
    }

    public function addProfessor(Professor $professor): self
    {
        if (!$this->professors->contains($professor)) {
            $this->professors[] = $professor;
        }

        return $this;
    }

    public function removeProfessor(Professor $professor): self
    {
        $this->professors->removeElement($professor);

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(): self
    {
        $this->fullName = $this->getName().' '.$this->getSurname();

        return $this;
    }
}
