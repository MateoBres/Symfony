<?= "<?php\n" ?>

namespace <?= $namespace ?>;

<?php if ($bounded_full_class_name): ?>
use <?= $bounded_full_class_name ?>;
<?php endif ?>
<?php /*foreach ($field_type_use_statements as $className): ?>
use <?= $className ?>;
<?php endforeach;*/ ?>
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\AdminFlock\Form\AdminAbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
<?php foreach ($constraint_use_statements as $className): ?>
use <?= $className ?>;
<?php endforeach; ?>

class <?= $class_name ?> extends AdminAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
<?php foreach ($form_fields as $form_field => $typeOptions): ?>
<?php if (null === $typeOptions['type'] && !$typeOptions['options_code']): ?>
            ->add('<?= $form_field ?>')
<?php elseif (null !== $typeOptions['type'] && !$typeOptions['options_code']): ?>
            ->add('<?= $form_field ?>', <?= $typeOptions['type'] ?>::class)
<?php else: ?>
            ->add('<?= $form_field ?>', <?= $typeOptions['type'] ? ($typeOptions['type'].'::class') : 'null' ?>, [
                <?= $typeOptions['options_code']."\n" ?>
            ])
<?php endif; ?>
<?php endforeach; ?>
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'csrf_protection'   => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return parent::getBlockPrefix();
    }
}
