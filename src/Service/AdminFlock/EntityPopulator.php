<?php


namespace App\Service\AdminFlock;


use App\Entity\CourseFlock\Course;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class EntityPopulator
{
    private $em;
    private $annotationReader;
    private $refClass;

    public function __construct(EntityManagerInterface $em, Reader $annotationReader)
    {
        $this->em = $em;
        $this->annotationReader = $annotationReader;
    }

    public function populate($entity, $formName, Request $request, $fieldName)
    {
//        $formName = $this->getFormName($entityType);
        $submittedDataArr = $request->request->get($formName);

        if ($submittedDataArr && isset($submittedDataArr[$fieldName])) {
            $this->refClass = new \ReflectionClass($entity);
            $propertyAccessor = new PropertyAccessor();
                $submittedData = $submittedDataArr[$fieldName];
//            foreach ($submittedDataArr as $fieldName => $submittedData) {
                $value = $this->getValue($fieldName, $submittedData);
                $propertyAccessor->setValue($entity, $fieldName, $value);
//            }
        }
    }

    private function getValue($fieldName, $submittedData)
    {
        $propAnnotations = $this->getORMAnnotations($fieldName);

        foreach($propAnnotations as $propAnnotation) {
            if ($propAnnotation instanceof Mapping\ManyToOne || $propAnnotation instanceof Mapping\OneToOne) {
                return $this->em->getRepository($propAnnotation->targetEntity)->find($submittedData);
            } elseif ($propAnnotation instanceof Mapping\Column) {
                return $submittedData;
            }
        }
    }

    private function getORMAnnotations($fieldName)
    {
        $refProperty = $this->refClass->getProperty($fieldName);
        $propAnnotations = $this->annotationReader->getPropertyAnnotations($refProperty);

        foreach($propAnnotations as $key => $propAnnotation) {
            if (!$propAnnotation instanceof Mapping\Annotation) {
                unset($propAnnotations[$key]);
            }
        }

        return $propAnnotations;
    }

    private function getFormName($entityType)
    {
        $form = new $entityType();
        return $form->getBlockPrefix();
    }

}