<?php

namespace App\Form\SettingsFlock;

use App\Entity\ContactFlock\Contact;
use App\Form\AdminFlock\Form\AdminAbstractType;
use App\Form\AdminFlock\SinervisEntityAutocomplete\SinervisEntityAutocompleteType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
//            ->add('mainContact', SinervisEntityAutocompleteType::class, [
//                'class' => Contact::class,
//                'route_name' => 'contact_flock_contact_ajax_list',
//                'show_all_on_focus' => true,
//                'required' => true,
//                'label' => $this->getLabel('mainContact'),
//                'placeholder' => 'Scegli un contatto principale',
//            ])
//            ->add('ecmAccreditationNumber', NumberType::class, [
//                'label' => $this->getLabel('ecmAccreditationNumber'),
//                'required' => true,
//            ])
            ->add('notificationEmails', CollectionType::class, [
                'label' => $this->getLabel('notificationEmails'),
                'entry_type' => TextType::class,
                'required' => false,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'attr' => [
                    'fields_map' => ['no_data_table' => true, 'no_table_header' => true],
                    'add_more_button_label' => 'email',
                ],
            ])
            ->add('questionnaireHeading', CKEditorType::class, [
                'label'=> false,
                'required' => false,
                'attr' => [
                    'class' => 'ckeditor-last-p-marginless'
                ],
            ])
            ->add('questionnaireThankingMsg', CKEditorType::class, [
                'label'=> false,
                'required' => false,
                'attr' => [
                    'class' => 'ckeditor-last-p-marginless'
                ],
            ])
        ;
    }
}