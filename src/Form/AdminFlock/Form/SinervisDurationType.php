<?php

namespace App\Form\AdminFlock\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;


class SinervisDurationType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $fieldSpecifiClass = 'sv-duration-input';
        if (isset($view->vars['attr']['class'])) {
            $view->vars['attr']['class'] = $view->vars['attr']['class'] .' '. $fieldSpecifiClass;
        } else {
            $view->vars['attr'] += ['class' => $fieldSpecifiClass];
        }
        $view->vars['attr'] += ['widget_icon' => 'fa fa-hourglass-start'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->addModelTransformer(new CallbackTransformer(
                // transform seconds into hours and minutes
                function ($durationInSecs) {
                    if ($durationInSecs) {
                        return sprintf("%02d:%02d", floor($durationInSecs/3600), ($durationInSecs/60)%60);
                    }
                    return;
                },
                function ($durationStr) {
                    $durationStr = trim($durationStr);

                    // transform text 'H:i' into corresponding seconds
                    if (!empty($durationStr)) {
                        @list($hours, $mins) = explode(':', $durationStr);
                        if(is_numeric($hours) && is_numeric($mins)) {
                            return ($hours * 3600) + ($mins * 60);
                        }
                    }

                    return 0;
                }
            ))
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                
                $field = $event->getForm();
                $normData = $event->getForm()->getNormData();

                if(!preg_match("/[0-9]+:[0-5]{1}[0-9]{1}/", $normData)){
                    $field->clearErrors();
                    $field->addError(new FormError('La durata del modulo deve essere nel formato ore:minuti'));
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
