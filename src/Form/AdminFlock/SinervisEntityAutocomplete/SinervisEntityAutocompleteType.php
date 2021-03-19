<?php

namespace App\Form\AdminFlock\SinervisEntityAutocomplete;

use App\Form\AdminFlock\SinervisEntityAutocomplete\DataTransformer\EntityToIdTransformer;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Component\Routing\RouterInterface;

/**
 *
 */
class SinervisEntityAutocompleteType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $em;
    private $router;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManagerInterface $em, RouterInterface $router) {
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $transformer = new EntityToIdTransformer($this->em, $options);
        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['route_name'] = $options['route_name'] ? $options['route_name'] : 'autocomplete_ajax_list';
        $view->vars['route_params'] = json_encode($options['route_params']);
        $view->vars['show_all_on_focus'] = $options['show_all_on_focus'];
        $view->vars['autocomplete_filtermode'] = $options['autocomplete_filtermode'];
        $view->vars['entity_title'] = '';
        $view->vars['params'] = $options['params'];

        $entityId = $view->vars['value'];

        if ($entityId) {
            $entity = $this->em
            ->getRepository($options['class'])
            ->find($entityId);
            
            $view->vars['entity_title'] = $entity ? $entity->__toString() : '';
        }
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
                'fields_map' => null,
                'autocomplete_filtermode' => null,
                'params' => [],
            ))

            ->setRequired(array('class', 'route_name'))
        ;
    }

    private function checkOptionAvailability($optionName, $value)
    {
        if (is_null($value)) {
            throw new MissingOptionsException("The option '".$optionName."' is required for 'sv_entity_autocomplete' type");
        }
    }

    public function getParent()
    {
        return TextType::class;
    }


    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'sv_entity_autocomplete';
    }

    public function getBlockPrefix()
    {
        return 'sv_entity_autocomplete';
    }

}
