<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class AchievementStatusType extends AbstractEnumType
{
    const NONE = 0;
    const INCOMPLETE = 1;
    const SUFFICIENT = 2;
    const COMPLETE = 3;
    const BEYOND = 4;

    protected static $choices = [
        self::NONE => 'Non iniziato',
        self::INCOMPLETE => 'Non completato',
        self::SUFFICIENT => 'Sufficiente',
        self::COMPLETE => 'Completato',
        self::BEYOND => 'Sopra il massimo',
    ];
}
