<?php


namespace Sinervis\FileUploaderBundle\Form\Type;

use Sinervis\FileUploaderBundle\Form\DataTransformer\FileTransformer;
use Sinervis\FileUploaderBundle\Util\MetadataReader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Doctrine\Common\Collections\ArrayCollection;

class SinervisFileType extends AbstractType
{
    protected $em;
    protected $metadataReader;


    public function __construct(EntityManagerInterface $em, MetadataReader $metadataReader)
    {
        $this->em = $em;
        $this->metadataReader = $metadataReader;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allow_delete' => true,
            'error_bubbling' => false,
            'data_class' => 'Sinervis\FileUploaderBundle\Entity\SvFile',
            'fields_map' => [],
        ]);

        $resolver->setAllowedTypes('allow_delete', 'bool');
        $resolver->setAllowedTypes('error_bubbling', 'bool');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => ['class' => 'sinervis-file-name']
            ])
            ->add('originalName', null, [
                'attr' => ['class' => 'sinervis-file-original-name']
            ])
            ->add('mimeType', null, [
                'attr' => ['class' => 'sinervis-file-mimetype']
            ])
            ->add('size', null, [
                'attr' => ['class' => 'sinervis-file-size']
            ])
            ->add('uri', null, [
                'attr' => ['class' => 'sinervis-file-uri']
            ])
            ->add('createdAt')
            ->add('softDelete', null, [
                'attr' => ['class' => 'sinervis-file-soft-delete']
            ])
        ;

        $fileFieldClass = 'fake-file-field';
        if (isset($options['attr']['class'])) {
            $options['attr']['class'] .= ' ' . $fileFieldClass;
        } else {
            $options['attr']['class'] = ' ' . $fileFieldClass;
        }

        $isRequiredField = isset($options['required']) && $options['required'] === true;
        $isPartOfCollection = $builder->getPropertyPath() !== null;
        $notBlankMsg = "{$options['label']} non dovrebbe essere vuoto";

        $builder
            ->add('file', Type\FileType::class, [
                'mapped' => false,
                'required' => $options['required'],
                'label' => $options['label'],
                'attr' => $options['attr'],
                'translation_domain' => $options['translation_domain'],
            ])
            ->add('stunt', null, [
                'mapped' => false,
                'required' => $options['required'],
                'constraints' => $isRequiredField && !$isPartOfCollection ? [new NotBlank(['message' => $notBlankMsg])] : null,
                'attr' => [
                    'class' => 'file-field-stunt ' . $options['attr']['class'] ?? '',
                    'readonly' => true,
                    'placeholder' => 'Nessun file selezionato'
                ]
            ])
        ;

        $builder->addModelTransformer(new FileTransformer($this->em));
    }


    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $object = $form->getParent()->getData();
        $dataClass = $form->getParent()->getConfig()->getDataClass();
        $propertyName = $form->getName();

        if ($object instanceof \Traversable || is_array($object)) {
            $dataClass = $form->getParent()->getParent()->getConfig()->getDataClass();
            $propertyName = $form->getParent()->getName();
        }

        $this->metadataReader->verifyCorrectUploaderAnnotationUsage($dataClass, $propertyName);

        $view->vars['object'] = $object;
        $view->vars['data_class'] = $dataClass;
        $view->vars['property_name'] = $propertyName;
    }

    public function getBlockPrefix(): string
    {
        return 'sv_file';
    }

    protected function resolveUriOption($uriOption, $object, FormInterface $form)
    {
        if (true === $uriOption) {
            return $this->storage->resolveUri($object, $form->getName());
        }

        if (\is_callable($uriOption)) {
            return $uriOption($object, $this->storage->resolveUri($object, $form->getName()));
        }

        return $uriOption;
    }
}