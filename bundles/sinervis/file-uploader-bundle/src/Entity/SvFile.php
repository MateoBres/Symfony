<?php


namespace Sinervis\FileUploaderBundle\Entity;

use App\Entity\AdminFlock\TimestampableTrait;
use App\Entity\TaxonomyFlock\Vocabulary\EducationalQualification;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class File
 * @ORM\Entity()
 */
class SvFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="name")
     */
    protected $name;

    /**
     * @ORM\Column(name="original_name")
     */
    protected $originalName;

    /**
     * @ORM\Column(name="mime_type", nullable=true)
     */
    protected $mimeType;

    /**
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    protected $size;

    /**
     * @ORM\Column(name="uri")
     */
    protected $uri;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="owner_class", nullable=true)
     */
    protected $ownerClass;

    /**
     * @ORM\Column(name="owner_property", nullable=true)
     */
    protected $ownerProperty;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $ownerId;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $softDelete;

    protected $oldFileNameToBeDeleted;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    public function __construct()
    {
        $this->softDelete = false;
    }

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    function __toString()
    {
        return $this->originalName;
    }

    function __clone()
    {
        $this->id = null;
        $this->createdAt = null;
    }

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): void
    {
        $this->originalName = $originalName;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(?string $uri): void
    {
        $this->uri = $uri;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getOwnerClass()
    {
        return $this->ownerClass;
    }

    /**
     * @param mixed $ownerClass
     */
    public function setOwnerClass($ownerClass): void
    {
        $this->ownerClass = $ownerClass;
    }

    /**
     * @return mixed
     */
    public function getOwnerProperty()
    {
        return $this->ownerProperty;
    }

    /**
     * @param mixed $ownerProperty
     */
    public function setOwnerProperty($ownerProperty): void
    {
        $this->ownerProperty = $ownerProperty;
    }


    /**
     * @return mixed
     */
    public function getOwnerId()
    {
        return $this->ownerId;
    }

    /**
     * @param mixed $ownerId
     */
    public function setOwnerId($ownerId): void
    {
        $this->ownerId = $ownerId;
    }

    /**
     * @return bool
     */
    public function isSoftDelete(): bool
    {
        return $this->softDelete;
    }

    /**
     * @param bool $softDelete
     */
    public function setSoftDelete(bool $softDelete): void
    {
        $this->softDelete = $softDelete;
    }

    /**
     * @return mixed
     */
    public function getOldFileNameToBeDeleted()
    {
        return $this->oldFileNameToBeDeleted;
    }

    /**
     * @param mixed $oldFileNameToBeDeleted
     */
    public function setOldFileNameToBeDeleted($oldFileNameToBeDeleted): void
    {
        $this->oldFileNameToBeDeleted = $oldFileNameToBeDeleted;
    }
}