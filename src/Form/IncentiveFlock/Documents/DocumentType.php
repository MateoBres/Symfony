<?php

namespace App\Form\IncentiveFlock\Documents;

use App\Entity\IncentiveFlock\Documents\Document;
use App\Form\AdminFlock\SinervisMarkUp\SinervisMarkUpType;
use Sinervis\FileUploaderBundle\Form\Type\SinervisFileType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentType extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('paper', SinervisFileType::class, [
                'label'=> false,
                'required' => false
            ])
            ->add('isPaperApproved', null, [
                'label'=> 'Approvato',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
