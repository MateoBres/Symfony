<?php

namespace App\Form\AdminFlock;


use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataImportType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $params = $options['data']['params'] ?? [];

        $builder
            ->add('excelFile', FileType::class, [
                'label' => 'File Excel',
                'mapped' => false,
                'required' => false,
            ])
            ->add('validated', CheckboxType::class, [
                'mapped' => false,
                'data' => false,
                'attr' => [
                    'class' => 'data-validated'
                ]
            ])
            ->add('importId', HiddenType::class, [
                'data' => '',
                'attr' => [
                    'class' => 'import-id'
                ]
            ])
            ->add('params', HiddenType::class, [
                'data' => json_encode($params),
            ])
            ->add('additionalInfo', HiddenType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'additional-info'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(//            'data_class' => ,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'data_import_type';
    }
}