<?php

namespace App\Entity\ProfessorFlock;

use App\Entity\AdminFlock\TimestampableTrait;
use App\Entity\StudentFlock\Student;
use App\Repository\ProfessorFlock\ProfessorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfessorRepository::class)
 */
class Professor
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
     * @ORM\Column(type="string", length=255)
     */
    private $teach;

    /**
     * @ORM\ManyToMany(targetEntity=Student::class, mappedBy="professors", cascade={"persist", "remove"})
     */
    private $students;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullName;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName().' '.$this->getSurname().' - '.$this->getTeach();
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

    public function getTeach(): ?string
    {
        return $this->teach;
    }

    public function setTeach(string $teach): self
    {
        $this->teach = $teach;

        return $this;
    }

    /**
     * @return Collection|Student[]
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->addProfessor($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->removeElement($student)) {
            $student->removeProfessor($this);
        }

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
