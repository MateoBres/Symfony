<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class HallArrangementType extends AbstractEnumType
{
    const ONLY_CHAIRS = 1;
    const CHAIRS_AND_TABLES = 2;

    protected static $choices = array(
        self::ONLY_CHAIRS => 'Solo Sedie',
        self::CHAIRS_AND_TABLES => 'Sedie e Tavoli',
    );
}
