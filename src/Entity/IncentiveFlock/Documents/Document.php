<?php

namespace App\Entity\IncentiveFlock\Documents;

use App\Entity\AdminFlock\TimestampableInterface;
use App\Entity\AdminFlock\TimestampableTrait;
use App\Entity\IncentiveFlock\Stages\AssignmentOfClaim;
use App\Repository\IncentiveFlock\Documents\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Sinervis\FileUploaderBundle\Annotations\SinervisUploadable;
use Sinervis\FileUploaderBundle\Annotations\SinervisUploadableField;
use Sinervis\FileUploaderBundle\Entity\SvFile;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *   "Document" = "Document",
 * })
 * @SinervisUploadable()
 */
class Document implements TimestampableInterface
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
     * @ORM\ManyToOne(targetEntity=SvFile::class, cascade={"all"})
     * @SinervisUploadableField(destination="/data")
     */
    private $paper;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPaperApproved;

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

    public function getPaper(): ?SvFile
    {
        return $this->paper;
    }

    public function setPaper(?SvFile $paper): self
    {
        $this->paper = $paper;

        return $this;
    }

    public function getIsPaperApproved(): ?bool
    {
        return $this->isPaperApproved;
    }

    public function setIsPaperApproved(bool $isPaperApproved): self
    {
        $this->isPaperApproved = $isPaperApproved;

        return $this;
    }

}
