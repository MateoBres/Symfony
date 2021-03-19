<?php


namespace Sinervis\FileUploaderBundle\Annotations;


/**
 * SvUploadableField.
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 */
class SinervisUploadableField
{
    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    /**
     * @var string
     */
    public $destination;

    /**
     * @var string
     */
    public $maxFileSize;

    /**
     * @var array
     */
    public $allowedMimeTypes;

    /**
     * @var array
     */
    public $permissions;


    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    public function __construct()
    {
        $this->allowedMimeTypes = [];
        $this->permissions = [];
    }

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param mixed $destination
     */
    public function setDestination($destination): void
    {
        $this->destination = $destination;
    }

    /**
     * @return mixed
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * @param mixed $maxFileSize
     */
    public function setMaxFileSize($maxFileSize): void
    {
        $this->maxFileSize = $maxFileSize;
    }

    /**
     * @return array
     */
    public function getAllowedMimeTypes(): array
    {
        return $this->allowedMimeTypes;
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param array $permissions
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

}