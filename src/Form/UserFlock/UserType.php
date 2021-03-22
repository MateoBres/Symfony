<?php

namespace App\Form\UserFlock;

use App\Entity\QuestionnaireFlock\QualityQuestionnaire;
use App\Entity\QuestionnaireFlock\SecurityQuestionnaire;
use App\Entity\UserFlock\User;
use App\Form\AdminFlock\Form\AdminAbstractType;
use App\Form\AdminFlock\SinervisEntityAutocomplete\SinervisEntityAutocompleteType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AdminAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $user = $options['data'] ?? null;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            $form
                ->add('username', null, [
                    'label'=> $this->getLabel('username')
                ])
                ->add('email', null, [
                    'label'=> $this->getLabel('email')
                ])

                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'options' => array(
                        'translation_domain' => 'FOSUserBundle',
                        'attr' => array(
                            'autocomplete' => 'new-password',
                        ),
                    ),
                    'required' => $user->getId() === null,
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Ripeti password'),
                    'invalid_message' => 'Le password non coincidono',
                    'attr' => [
                        'class' => 'password-wrapper'
                    ]
                ))
                ->add('enabled', null, [
                    'label'=> $this->getLabel('enabled'),
                    'disabled' => $user && $user->getId() === 1,
                    'attr' => [
                        'class' => 'user-enabled'
                    ]
                ])
            ;
        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
