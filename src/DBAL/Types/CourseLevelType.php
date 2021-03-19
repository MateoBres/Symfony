<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class CourseLevelType extends AbstractEnumType
{
    const BEGINNER      = 1;
    const INTERMEDIATE  = 2;
    const ADVANCED      = 3;

    protected static $choices = array(
        self::BEGINNER      => 'Base',
        self::INTERMEDIATE  => 'Intermedio',
        self::ADVANCED      => 'Avanzato',
    );
}