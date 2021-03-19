<?php

namespace App\Form\AdminFlock\ChoiceOrText;

use Doctrine\ORM\EntityManagerInterface;
use App\Form\AdminFlock\ChoiceOrText\DataTransformer\TextToEntityTransformer;
use App\Form\AdminFlock\SinervisTextAutocomplete\SinervisTextAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Doctrine\ORM\EntityManager;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * Filter type for entities.
 */
class ChoiceOrTextType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $transformer = new TextToEntityTransformer($this->em, $options);
        $builder->addModelTransformer($transformer);
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults(array(
                'attr' => array(),
                'removeOrphans' => false,
                'trim' => true,
                'textToUpper' => false,
                'property' => null,
            ))
            ->addNormalizer(
                'removeOrphans', function (Options $options, $value) {
                if (!empty($value)) {
                    if (isset($value['mappedByFieldName']) && isset($value['mappedClassName'])) {
                        $mappedByFieldName = trim($value['mappedByFieldName']);
                        $mappedClassName = trim($value['mappedClassName']);

                        if (!property_exists($options['class'], $mappedByFieldName)) {
                            throw new InvalidOptionsException("The property '$mappedByFieldName' does not exist in $options[class]");
                        }
                        if (!method_exists($options['class'], 'remove' . $mappedClassName)) {
                            throw new InvalidOptionsException("The method 'remove$mappedClassName' does not exist in $options[class]");
                        }

                        return $value;
                    }

                    throw new MissingOptionsException("The option 'removeOrphans' requires both 'mappedByFieldName' and 'mappedClassName'");
                }

                return false;
            })
            ->setRequired(array('class'));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return SinervisTextAutocompleteType::class;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'choice_or_text';
    }

    public function getBlockPrefix()
    {
        return 'choice_or_text';
    }
}
