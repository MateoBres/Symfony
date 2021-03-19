<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class ContactKindType extends AbstractEnumType
{
    const PERSON = 'p';
    const COMPANY = 'c';

    protected static $choices = array(
        self::PERSON => 'Persona',
        self::COMPANY => 'Azienda',
    );
}
