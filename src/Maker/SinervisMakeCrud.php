<?php

namespace App\Maker;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Common\Inflector\Inflector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
//use Symfony\Bundle\MakerBundle\Doctrine\EntityClassGenerator;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
//use Symfony\Bundle\MakerBundle\Renderer\FormTypeRenderer;
use App\Maker\Renderer\SinervisFormTypeRenderer;
use App\Maker\Renderer\SinervisFormFilterTypeRenderer;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;

class SinervisMakeCrud extends AbstractMaker
{
    private $doctrineHelper;
    private $formTypeRenderer;
    private $formFilterTypeRenderer;
    private $pathToSkeleton;


    public function __construct(DoctrineHelper $doctrineHelper, SinervisFormTypeRenderer $formTypeRenderer, SinervisFormFilterTypeRenderer $formFilterTypeRenderer)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->formTypeRenderer = $formTypeRenderer;
        $this->formFilterTypeRenderer = $formFilterTypeRenderer;
        $this->pathToSkeleton = __DIR__ . '/../Resources/skeleton/';
    }

    public static function getCommandName(): string
    {
        return 'sinervis:make:crud';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates CRUD for Sinervis entity class')
            ->addArgument(
                'entity-class',
                InputArgument::OPTIONAL,
                sprintf('The class name of the entity to create CRUD (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm()))
            )
            ->addOption(
                'skeleton-path',
                's',
                InputOption::VALUE_REQUIRED,
                sprintf('Skeleton path (sinervis | maker)'),
                'sinervis'
            );

        //$inputConfig->setArgumentAsNonInteractive('entity-class');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (null === $input->getArgument('entity-class')) {
            $argument = $command->getDefinition()->getArgument('entity-class');

            $entities = $this->doctrineHelper->getEntitiesForAutocomplete();

            $question = new Question($argument->getDescription());
            $question->setAutocompleterValues($entities);

            $value = $io->askQuestion($question);

            $input->setArgument('entity-class', $value);
        }
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists($input->getArgument('entity-class'), $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());

        $repositoryVars = [];

        if (null !== $entityDoctrineDetails->getRepositoryClass()) {
            $repositoryClassDetails = $generator->createClassNameDetails(
                '\\' . $entityDoctrineDetails->getRepositoryClass(),
                'Repository\\',
                'Repository'
            );

            $repositoryVars = [
                'repository_full_class_name' => $repositoryClassDetails->getFullName(),
                'repository_class_name' => $repositoryClassDetails->getShortName(),
                'repository_var' => lcfirst(Inflector::singularize($repositoryClassDetails->getShortName())),
            ];
        }

        $controllerClassDetails = $generator->createClassNameDetails(
            $entityClassDetails->getRelativeNameWithoutSuffix() . 'Controller',
            'Controller\\',
            'Controller'
        );

        //Form
        $iter = 0;
        do {
            $formClassDetails = $generator->createClassNameDetails(
                $entityClassDetails->getRelativeNameWithoutSuffix() . ($iter ?: '') . 'Type',
                'Form\\',
                'Type'
            );
            ++$iter;
        } while (class_exists($formClassDetails->getFullName()));

        //Filter
        $iter = 0;
        do {
            $formFilterClassDetails = $generator->createClassNameDetails(
                $entityClassDetails->getRelativeNameWithoutSuffix() . ($iter ?: '') . 'FilterType',
                //$this->getFormFilterNamespace($entityClassDetails).($iter ?: '').'FilterType',
                'Filter\\',
                'Type'
            );
            ++$iter;
        } while (class_exists($formFilterClassDetails->getFullName()));

        $entityVarPlural = lcfirst(Inflector::pluralize($entityClassDetails->getShortName()));
        $entityVarSingular = lcfirst(Inflector::singularize($entityClassDetails->getShortName()));

        $entityTwigVarPlural = Str::asTwigVariable($entityVarPlural);
        $entityTwigVarSingular = Str::asTwigVariable($entityVarSingular);

        $routeName = Str::asRouteName($controllerClassDetails->getRelativeNameWithoutSuffix());
        $templatesPath = Str::asFilePath($controllerClassDetails->getRelativeNameWithoutSuffix());

        $skeletonOption = $input->getOption('skeleton-path');
        $pathToSkeleton = $skeletonOption == 'sinervis' ? $this->pathToSkeleton : '';

        $generator->generateController(
            $controllerClassDetails->getFullName(),
            $pathToSkeleton . 'crud/controller/Controller.tpl.php',
            array_merge([
                'flock_name' => $this->getFlockName($entityClassDetails),
                'entity_namespace' => $this->getEntityNamespace($entityClassDetails),
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
                'form_full_class_name' => $formClassDetails->getFullName(),
                'form_filter_full_class_name' => $formFilterClassDetails->getFullName(),
                'form_class_name' => $formClassDetails->getShortName(),
                'route_path' => Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix()),
                'route_name' => $routeName,
                'templates_path' => $templatesPath,
                'entity_var_plural' => $entityVarPlural,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_var_singular' => $entityVarSingular,
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
            ],
                $repositoryVars
            )
        );

        //generate Form class
        $this->formTypeRenderer->render(
            $formClassDetails,
            // get only fileds name
            $entityDoctrineDetails->getFormFields(),
            //get all fields metadata
            //$entityDoctrineDetails->getDisplayFields()
            $entityClassDetails
        );

        //generate FormFilter class
        $this->formFilterTypeRenderer->render(
            $formFilterClassDetails,
            $entityDoctrineDetails->getFormFields(),
            $entityClassDetails
        );

        //TEMPLATE
        $templates = [
//            '_delete_form' => [
//                'route_name' => $routeName,
//                'entity_twig_var_singular' => $entityTwigVarSingular,
//                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
//            ],
//            '_form' => [],
            'edit' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
            ],
            'index' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'route_name' => $routeName,
            ],
            'new' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'route_name' => $routeName,
                'templates_path' => $templatesPath,
            ],
            'show' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'route_name' => $routeName,
            ],
        ];

        foreach ($templates as $template => $variables) {
            $generator->generateFile(
                'templates/' . $templatesPath . '/' . $template . '.html.twig',
                $pathToSkeleton . 'crud/templates/' . $template . '.tpl.php',
                $variables
            );
        }

        /***************************************/
        /*    Generate controller yaml file    */
        /***************************************/

        $configControllerFileName = 'src/Resources/config/' . $entityClassDetails->getRelativeName() . 'Controller.yaml';
        $generator->generateFile(
            str_replace('\\', '/', $configControllerFileName),
            $this->pathToSkeleton . 'crud/config/controller.tpl.php',
            [
                'sortby_field_prefix' => $this->getConfigSortByFieldPrefix($entityClassDetails),
                'entity_class' => $entityClassDetails->getShortName(),
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_twig_var_singular' => $entityTwigVarSingular,
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text(sprintf('Next: Check your new CRUD by going to <fg=yellow>%s/</>', Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix())));
    }

    /**
     * {@inheritdoc}
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Route::class,
            'router'
        );

        $dependencies->addClassDependency(
            AbstractType::class,
            'form'
        );

        $dependencies->addClassDependency(
            Validation::class,
            'validator'
        );

        $dependencies->addClassDependency(
            TwigBundle::class,
            'twig-bundle'
        );

        $dependencies->addClassDependency(
            DoctrineBundle::class,
            'orm-pack'
        );

        $dependencies->addClassDependency(
            CsrfTokenManager::class,
            'security-csrf'
        );

        $dependencies->addClassDependency(
            ParamConverter::class,
            'annotations'
        );
    }

    private function getFlockName(ClassNameDetails $entityClassDetails): string
    {
        $relativeClassName = $entityClassDetails->getRelativeName();
        return substr($relativeClassName, 0, strpos($relativeClassName, '\\'));
    }

    private function getEntityNamespace(ClassNameDetails $entityClassDetails): string
    {
        $relativeClassName = explode('\\', $entityClassDetails->getRelativeName());
        array_shift($relativeClassName); // removes flock name
        array_pop($relativeClassName); // removes entity name

        return implode('\\', $relativeClassName);
    }

    private function getConfigSortByFieldPrefix(ClassNameDetails $entityClassDetails)
    {
        $relativeClassName = explode('\\', $entityClassDetails->getRelativeName());
        array_shift($relativeClassName); // removes flock name

        return implode('_', $relativeClassName);
    }

    /*private function getFormFilterNamespace($entityClassDetails)
    {
        $relativeClassName = explode('\\', $entityClassDetails->getRelativeName(),-1);
        // ex: Form/ContactFlock/Roles/Test/Filter/CustomerFilterType.php
        return implode('\\', $relativeClassName).'\\Filter\\'.$entityClassDetails->getShortName();
    }*/
}