<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class SettingsKindType extends AbstractEnumType
{
    const GLOBALS = 0;
    const USER = 1;

    protected static $choices = array(
        self::GLOBALS => 'Globali',
        self::USER => 'Utente',
    );

}
