<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 07/11/14
 * Time: 10.32
 */

namespace App\Form\AdminFlock\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


abstract class AdminAbstractType extends AbstractType
{
    protected $entityManager;
    protected $tokenStorage;
    protected $authChecker;
    protected $fields_map;
    protected $embedded;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->authChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->fields_map = $options['fields_map'] ?? array();
        $this->embedded = $options['embedded'] ?? null;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'fields_map' => null,
            'embedded' => null,
            'csrf_protection' => true,
        ));
    }

    public function getLabel($fieldName)
    {
        return isset($this->fields_map[$fieldName]['label']) ? $this->fields_map[$fieldName]['label'] : null;
    }

    public function setLabels(FormBuilderInterface $builder)
    {
        foreach ($builder as $field) {
            if (isset($this->fields_map[$field->getName()]['label'])) {
                $options = $field->getOptions();                                    // get the options
                $type = $field->getType()->getName();                               // get the name of the type
                $options['label'] = $this->fields_map[$field->getName()]['label'];  // change the label
                try {
                    $builder->add($field->getName(), $type, $options);                // replace the field
                } catch (Exception $e) {
                }
            }
        }
    }

    public function getFieldsMap($fieldName = NULL)
    {
        if ($fieldName) {
            if (isset($this->fields_map[$fieldName]['fields_map'])) {
                return $this->fields_map[$fieldName]['fields_map'];
            }

            return array();
        }

        return $this->fields_map;
    }

    public function getSecurityContext()
    {
        return $this->security_context;
    }

    public function getEntityManager()
    {
        return $this->entity_manager;
    }

    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * @return AuthorizationCheckerInterface
     */
    public function getAuthChecker(): AuthorizationCheckerInterface
    {
        return $this->authChecker;
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string The prefix of the template block name
     * ex: ContactFlock\Roles\Test\Customer -> contactflock_roles_test_customer_{fieldname}
     */
    public function getBlockPrefix()
    {
        return StringUtil::fqcnToBlockPrefix(get_class($this));
    }
} 
