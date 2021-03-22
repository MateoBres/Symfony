<?php

namespace App\Entity\IncentiveFlock\Stages;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Repository\IncentiveFlock\Stages\SeismicRoutineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeismicRoutineRepository::class)
 */
class SeismicRoutine extends Stage
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
    private $seismicRetrofittingProject;

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

    public function getSeismicRetrofittingProject(): ?Document
    {
        return $this->seismicRetrofittingProject;
    }

    public function setSeismicRetrofittingProject(?Document $seismicRetrofittingProject): self
    {
        $this->seismicRetrofittingProject = $seismicRetrofittingProject;

        return $this;
    }
}
