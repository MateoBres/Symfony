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

        $builder
            ->add('username', null, [
                'label'=> $this->getLabel('username')
            ])
            ->add('email', null, [
                'label'=> $this->getLabel('email')
            ])
            ->add('enabled', null, [
                'label'=> $this->getLabel('enabled'),
                'disabled' => $user && $user->getId() === 1,
                'attr' => [
                    'class' => 'user-enabled'
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => $this->getLabel('roles'),
                'choices' => array(
//                    'SUPER ADMIN' => 'ROLE_SUPER_ADMIN',
                    'ADMIN' => 'ROLE_ADMIN',
                ),
                'disabled' => $user && $user->getId() === 1,
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'user-roles'
                ]
            ])
        ;

        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                [$this, 'onPreSetData']
            )
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                [$this, 'onPreSubmit']
            )
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                [$this, 'onPostSubmit']
            );
    }

    public function onPreSetData(FormEvent $event): void
    {
        $user = $event->getData();
        $form = $event->getForm();

        $form->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'options' => array(
                'translation_domain' => 'FOSUserBundle',
                'attr' => array(
                    'autocomplete' => 'new-password',
                ),
            ),
            'required' => !$user || $user->getId() === null,
            'first_options' => array('label' => 'Password'),
            'second_options' => array('label' => 'Ripeti password'),
            'invalid_message' => 'Le password non coincidono',
            'attr' => [
                'class' => 'password-wrapper'
            ]
        ));

        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles(), true);

        if ($isAdmin) {
            $user->setQuizPermissions([]);
            $event->setData($user);
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();

        $isAdmin = isset($data['roles']) && in_array('ROLE_ADMIN', $data['roles'], true);
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $user = $event->getData();
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles(), true);
        if ($isAdmin) {
            $user->setQuizPermissions([]);
            $event->setData($user);
        }
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
