<?php

namespace App\Form\AdminFlock\SinervisCollectionType;

use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SinervisCollectionType extends CollectionType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        parent::buildForm($builder, $options);

        if ($options['allow_duplicate']) {
            if (null !== $options['skip_duplication']) {
                $builder->setAttribute('skip-duplication', implode(',', $options['skip_duplication']));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars = array_replace($view->vars, [
            'allow_duplicate' => $options['allow_duplicate'],
            'skip_duplication' => $options['skip_duplication'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefault('allow_duplicate', false)
            ->setDefault('skip_duplication', []);

        $resolver->setAllowedTypes('allow_duplicate', ['bool', 'callable']);
        $resolver->setAllowedTypes('skip_duplication', ['array']);
    }
}
