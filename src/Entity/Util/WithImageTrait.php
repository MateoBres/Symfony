<?php

namespace App\Entity\Util;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use DateTimeImmutable;

/**
 * Note: using class MUST have @Vich\Uploadable() annotation
 * and call initWithImageTrait() in constructor
 *
 * Trait WithImageTrait
 * @package App\Entity\Util
 */
trait WithImageTrait
{
    /**
     * ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     * @var EmbeddedFile
     */
    private $image;

    /**
     * Vich\UploadableField(mapping="entity_images", fileNameProperty="image.name", size="image.size", mimeType="image.mimeType", originalName="image.originalName", dimensions="image.dimensions")
     * @var File|null
     */
    private $imageFile;

    /*
     * **MUST** be called in class constructor
     */
    private function initWithImageTrait()
    {
        $this->image = new EmbeddedFile();
    }

    /**
     * @param File|UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null)
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile && property_exists($this, 'updatedAt')) {
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImage(EmbeddedFile $image): void
    {
        $this->image = $image;
    }

    public function getImage(): ?EmbeddedFile
    {
        return $this->image;
    }
}