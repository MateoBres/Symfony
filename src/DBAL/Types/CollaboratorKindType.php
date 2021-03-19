<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class CollaboratorKindType extends AbstractEnumType
{
    const TEACHER = 1;
    const TUTOR = 2;

    protected static $choices = array(
        self::TEACHER => 'Docente',
        self::TUTOR => 'Tutor',
    );
}
