<?php

namespace App\Entity\ProfessoreFlock;

use App\Entity\AdminFlock\TimestampableTrait;
use App\Entity\AlumnoFlock\Alumno;
use App\Repository\ProfessoreFlock\ProfessoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfessoreRepository::class)
 */
class Professore
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
     * @ORM\Column(type="string", length=255)
     */
    private $materia;

    /**
     * @ORM\ManyToMany(targetEntity=Alumno::class, mappedBy="professori", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $alunni;

    public function __construct()
    {
        $this->alunni = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getCognome() . ' - ' . $this->getMateria();
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

    public function getMateria(): ?string
    {
        return $this->materia;
    }

    public function setMateria(string $materia): self
    {
        $this->materia = $materia;

        return $this;
    }

    /**
     * @return Collection|Alumno[]
     */
    public function getAlunni(): Collection
    {
        return $this->alunni;
    }

//    public function setAlunni($alunni)
//    {
//        foreach($alunni as $alunno) {
//                $this->addAlunni($alunno);
//        }
//        foreach($this->getAlunni() as $alunno){
//            if(!$alunni->contains($alunno)){
//                $this->removeAlunni($alunno);
//            }
//        }
//
//    }

    public function addAlunno(Alumno $alunni): self
    {
        if (!$this->alunni->contains($alunni)) {
            $this->alunni[] = $alunni;
            $alunni->addProfessori($this);
        }

        return $this;
    }

    public function removeAlunno(Alumno $alunni): self
    {
        $this->alunni->removeElement($alunni);
        $alunni->removeProfessori($this);

        return $this;
    }
}
