<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class StateOfProgressType extends AbstractEnumType
{
    const NONE = 0;
    const CONCLUDED = 1;
    const IN_PROGRESS = 2;
    const SCHEDULED = 3;
    const CANCELLED = 4;

    protected static $choices = array(
        self::NONE => 'Non definito',
        self::CONCLUDED => 'Concluso',
        self::IN_PROGRESS => 'In Svolgimento',
        self::SCHEDULED => 'Programmato',
        self::CANCELLED => 'Annullato',
    );

}
