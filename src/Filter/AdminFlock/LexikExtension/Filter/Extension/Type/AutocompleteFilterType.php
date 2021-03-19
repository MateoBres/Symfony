<?php

namespace App\Filter\AdminFlock\LexikExtension\Filter\Extension\Type;

use App\Form\AdminFlock\SinervisTextAutocomplete\SinervisTextAutocompleteType;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Filter type for entities.
 */
class AutocompleteFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $attributes = array(
            'conditionFieldName' => $options['queryInfo']['conditionFieldName'],
            'joinTables' => isset($options['queryInfo']['joinTables']) ? $options['queryInfo']['joinTables'] : array(),
            'rootTable' => isset($options['queryInfo']['rootTable']) ? $options['queryInfo']['rootTable'] : null
        );

        if (true === $options['operand_selector']) {
            $builder->add('condition_pattern', 'choice', $options['choice_options']);
        } else {
            $attributes['condition_pattern'] = $options['condition_pattern'];
        }


        $autocompleter_options = array(
            'class' => $options['class'],
            'trim' => true,
        );

        if ($options['query_builder']) {
            $autocompleter_options['query_builder'] = $options['query_builder'];
        } else {
            $autocompleter_options['route_name'] = $options['route_name'];
            $autocompleter_options['route_params'] = $options['route_params'];
            $autocompleter_options['query_builder'] = function (EntityRepository $er) {
                return $er->createQueryBuilder('entity');
            };
        }

        $autocompleter_options['autocomplete_filtermode'] = $options['autocomplete_filtermode'];

        $builder->add('autocomplete_field', SinervisTextAutocompleteType::class, $autocompleter_options);

        $builder->setAttribute('filter_options', $attributes);
    }


    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'operand_selector' => $options['operand_selector'],
        ));
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults(array(
                'required' => false,
                'condition_pattern' => FilterOperands::STRING_CONTAINS,
                'compound' => true,
                'operand_selector' => function (Options $options) {
                    return $options['condition_pattern'] == FilterOperands::OPERAND_SELECTOR;
                },
                'choice_options' => array(
                    'choices' => FilterOperands::getStringOperandsChoices(),
                    'required' => false,
                    'translation_domain' => 'LexikFormFilterBundle',
                ),
                'data_extraction_method' => 'sv_autocompleter',
                'route_name' => null,
                'route_params' => [],
                'query_builder' => null,
                'autocomplete_filtermode' => true,
            ))
            ->setAllowedValues(
                'condition_pattern', FilterOperands::getStringOperands(true)
            )
            ->setRequired(array('class', 'route_name', 'queryInfo'));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'filter_autocomplete';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'filter_autocomplete';
    }
}
