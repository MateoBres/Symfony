<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Maker\Renderer;

use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;


class SinervisFormTypeRenderer
{
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function render(ClassNameDetails $formClassDetails, array $formFields, ClassNameDetails $boundClassDetails = null, array $constraintClasses = [])
    {
        $fieldTypeUseStatements = [];
        $fields = [];
        foreach ($formFields as $name => $fieldTypeOptions) {

            $fieldTypeOptions = $fieldTypeOptions ?? ['type' => null, 'options_code' => null];

            $fieldTypeOptions['options_code'] = "'label'=> \$this->getLabel('$name')";

            if (isset($fieldTypeOptions['type'])) {
                $fieldTypeUseStatements[] = $fieldTypeOptions['type'];
                $fieldTypeOptions['type'] = Str::getShortClassName($fieldTypeOptions['type']);
            }

            $fields[$name] = $fieldTypeOptions;
        }

        $this->generator->generateClass(
            $formClassDetails->getFullName(),
            __DIR__ . '/../../Resources/skeleton/form/FormType.tpl.php',
            [
                'bounded_full_class_name' => $boundClassDetails ? $boundClassDetails->getFullName() : null,
                'bounded_class_name' => $boundClassDetails ? $boundClassDetails->getShortName() : null,
                'form_fields' => $fields,
                'field_type_use_statements' => $fieldTypeUseStatements,
                'constraint_use_statements' => $constraintClasses,
            ]
        );
    }
}
