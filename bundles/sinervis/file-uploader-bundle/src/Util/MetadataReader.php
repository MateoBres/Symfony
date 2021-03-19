<?php


namespace Sinervis\FileUploaderBundle\Util;

use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Sinervis\FileUploaderBundle\Annotations\SinervisUploadable;
use Sinervis\FileUploaderBundle\Annotations\SinervisUploadableField;
use Sinervis\FileUploaderBundle\Exception\NotUploadableException;
use Symfony\Contracts\Cache\CacheInterface;

class MetadataReader
{
    protected $annotationReader;
    protected $cache;

    public function __construct(AnnotationReader $annotationReader, CacheInterface $cache)
    {
        $this->annotationReader = $annotationReader;
        $this->cache = $cache;
    }

    public function isUploadable(string $class, ?string $mapping = null): bool
    {
        return $this->cache->get('class_uploadable_'.md5($class) , function () use ($class) {
            $reflectionClass = new \ReflectionClass($class);
            $metadata = $this->annotationReader->getClassAnnotation($reflectionClass, SinervisUploadable::class);

            if (null === $metadata) {
                return false;
            }

            return true;
        });
    }

    public function getUploadableFields(string $class, ?string $mapping = null): array
    {
        return $this->cache->get('class_uploadable_fields_'.md5($class), function () use ($class) {
            $reflectionClass = new \ReflectionClass($class);

            $uploadableFields = [];
            foreach ($reflectionClass->getProperties() as $property) {
                $propertyMetadata = $this->annotationReader->getPropertyAnnotation($property, SinervisUploadableField::class);
                if (null !== $propertyMetadata) {
                    $uploadableFields[] = $property;
                }
            }

            return $uploadableFields;
        });
    }

    public function getUploadableFieldNames(string $class): array
    {
        return $this->cache->get('class_uploadable_field_names_'.md5($class), function () use ($class) {
            $uploadableFields = $this->getUploadableFields($class);
            $fieldNames = [];
            foreach ($uploadableFields as $uploadableField) {
                $fieldNames[] = $uploadableField->getName();
            }

            return $fieldNames;
        });
    }

    public function verifyCorrectUploaderAnnotationUsage(string $class, string $propertyName): void
    {
        if (!$this->isUploadable($class)) {
            throw new NotUploadableException(\sprintf('The class "%s" is not uploadable. If you use annotations to configure SinervisFileUploaderBundle, you probably just forgot to add `@SinervisUploadable` on top of your entity. If you don\'t use annotations, check that the configuration files are in the right place. In both cases, clearing the cache can also solve the issue.', $class));
        }

        $uploadableFieldNames = $this->getUploadableFieldNames($class);
        if (!in_array($propertyName, $uploadableFieldNames, true)) {
            throw new NotUploadableException(\sprintf('The field "%s" is not uploadable. If you use annotations to configure SinervisFileUploaderBundle, you probably just forgot to add `@SinervisUploadableField()` on top of the field. If you don\'t use annotations, check that the configuration files are in the right place. In both cases, clearing the cache can also solve the issue.', $propertyName));
        }
    }


    public function getUploadableFieldPropertyAnnotation(string $classname, string $propertyName, $annotationClass)
    {
        return $this->cache->get('uploadable_field_prop_annotation_'.md5($classname.$propertyName), function () use ($classname, $propertyName, $annotationClass) {
            $property = new \ReflectionProperty($classname, $propertyName);
            return $this->annotationReader->getPropertyAnnotation($property, $annotationClass);
        });
    }
}