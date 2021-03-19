<?php
namespace App\Form\AdminFlock\SinervisEntityAutocomplete\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToFieldTransformer implements DataTransformerInterface
{
    private $manager;
    private $class;
    private $field;

    public function __construct(ObjectManager $manager, $options)
    {
        $this->manager = $manager;
        $this->class = @$options['class'];
        $this->field = $options['field'];
    }

    /**
     * Transforms an object (entity) to a string.
     *
     * @param  Issue|null $entity
     * @return string
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return;
        }
        return $entity->{'get' . ucfirst($this->field) }();
    }

    /**
     * Transforms a string (number) to an object (entity).
     *
     * @param  string $value
     * @return Issue|null
     * @throws TransformationFailedException if object (entity) is not found.
     */
    public function reverseTransform($value)
    {
        // no entity number? It's optional, so that's ok
        if (!$value) {
            return;
        }

        $entity = $this->manager
            ->getRepository($this->class)
            // query for the entity with this id
            ->findOneBy([
                $this->field => $value
            ])
        ;

        if (null === $entity) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An entity with ' . $this->field. ' = "%s" does not exist!',
                $value
            ));
        }

        return $entity;
    }
}
