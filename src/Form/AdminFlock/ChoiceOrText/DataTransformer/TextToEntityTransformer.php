<?php

namespace App\Form\AdminFlock\ChoiceOrText\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

class TextToEntityTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $em;
    private $class;
    private $property;
    private $getPropertyValue;
    private $removeOrphans;
    private $prevPropertyValue;
    private $textToUpper;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, $options)
    {
        $this->em = $em;
        $this->class = $options['class'];
        $this->property = $options['property'];
        $this->removeOrphans = $options['removeOrphans'];
        $this->getPropertyValue = $options['property'] ? 'get' . $options['property'] : '__toString';
        $this->textToUpper = $options['textToUpper'];
    }

    /**
     * Transforms an object (entity) to a string (text).
     *
     * @param Entity|null $entity
     * @return string
     */
    public function transform($entity)
    {

        if (null === $entity) {
            return "";
        }

        $text = $entity->{$this->getPropertyValue}();
        $this->prevPropertyValue = $text;
        return $text;

    }

    /**
     * Transforms a string (text) to an entity.
     *
     * @param string $text
     *
     * @return Entity|null
     *
     * @throws TransformationFailedException if object is not found.
     */
    public function reverseTransform($text)
    {
        if ($this->textToUpper) {
            $text = strtoupper($text);
        }

        $repository = $this->em->getRepository($this->class);

        if ($this->removeOrphans) {
            $this->removeOrphans($text, $this->removeOrphans, $repository);
        }

        if (!$text) {
            return;
        }

        $entity = $repository->findOneBy(array($this->property => $text));

        if (null === $entity) {
            $entity = new $this->class();
            if (!method_exists($this->class, 'set' . $this->property)) {
                throw new \Exception("The method 'set" . ucfirst($this->property) . "' does not exist in $this->class");
            }

            $entity->{'set' . $this->property}($text);

            $this->em->persist($entity);
        }

        return $entity;
    }


    private function removeOrphans($text, $removeOrphans, $repository)
    {

        $mappedByFieldName = $removeOrphans['mappedByFieldName'];
        $mappedClassName = $removeOrphans['mappedClassName'];;

        // Check if the text is being removed or changed.
        if ((empty($text) && $this->prevPropertyValue) || ($this->prevPropertyValue && $this->prevPropertyValue != $text)) {

            $entity = $repository->findOneBy(array($this->property => $this->prevPropertyValue));
            $mappedEntities = $entity->{'get' . $mappedByFieldName}();

            if (count($mappedEntities) === 1) {
                //TODO: check if follwing line is necessary.
                $entity->{'remove' . $mappedClassName}($mappedEntities->current());
                $this->em->remove($entity);
            }
        }
    }
}