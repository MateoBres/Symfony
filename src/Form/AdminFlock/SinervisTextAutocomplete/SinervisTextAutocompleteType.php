<?php

namespace App\Form\AdminFlock\SinervisTextAutocomplete;

use Sinervis\AdminBundle\Form\SinervisEntityAutocomplete\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Doctrine\ORM\EntityManager;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 *
 */
class SinervisTextAutocompleteType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['route_name'] = $options['route_name'] ? $options['route_name'] : 'autocomplete_ajax_list';
        $view->vars['route_params'] = $options['route_params'] ? json_encode($options['route_params']) : json_encode([]);
        $view->vars['show_all_on_focus'] = $options['show_all_on_focus'];
        $view->vars['autocomplete_filtermode'] = $options['autocomplete_filtermode'];
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults(array(
                'class' => function (Options $options, $previousValue) {
                    $this->checkOptionAvailability('class', $previousValue);
                    return $previousValue;
                },
                'route_name' => function (Options $options, $value) {
                    if (is_null($value)) {
                        return 'autocomplete_ajax_list';
                    }

                    return $value;
                },
                'route_params' => [],
                'show_all_on_focus' => true,
                'placeholder' => null,
                'query_builder' => null,
                'autocomplete_filtermode' => null,
            ))

            ->setRequired(array('class', 'route_name'))
        ;
    }

    private function checkOptionAvailability($optionName, $value)
    {
        if (is_null($value)) {
            throw new MissingOptionsException("The option '".$optionName."' is required for 'sv_text_autocomplete' type");
        }
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'sv_text_autocomplete';
    }
}
