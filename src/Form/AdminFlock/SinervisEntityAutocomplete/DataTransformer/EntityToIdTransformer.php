<?php
namespace App\Form\AdminFlock\SinervisEntityAutocomplete\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToIdTransformer implements DataTransformerInterface
{
    private $manager;
    private $class;

    public function __construct(ObjectManager $manager, $options)
    {
        $this->manager = $manager;
        $this->class = $options['class'];
    }

    /**
     * Transforms an object (entity) to a string (number).
     *
     * @param  Issue|null $entity
     * @return string
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return;
        }

        return $entity->getId();
    }

    /**
     * Transforms a string (number) to an object (entity).
     *
     * @param  string $entityId
     * @return Issue|null
     * @throws TransformationFailedException if object (entity) is not found.
     */
    public function reverseTransform($entityId)
    {
        // no entity number? It's optional, so that's ok
        if (!$entityId) {
            return;
        }

        $entity = $this->manager
            ->getRepository($this->class)
            // query for the entity with this id
            ->find($entityId)
        ;

        if (null === $entity) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An entity with number "%s" does not exist!',
                $entityId
            ));
        }

        return $entity;
    }
}
