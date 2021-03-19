<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class CourseModuleAreaType extends AbstractEnumType
{
    const NONE      = 0;
    const LEGAL      = 1;
    const TECHNICAL  = 2;
    const PSYCHOLOGICAL = 3;

    protected static $choices = array(
        self::NONE      => 'Nessuna',
        self::LEGAL      => 'Area giuridica',
        self::TECHNICAL  => 'Area tecnica',
        self::PSYCHOLOGICAL      => 'Area psicologica',
    );
}