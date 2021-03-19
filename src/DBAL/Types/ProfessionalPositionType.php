<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class ProfessionalPositionType extends AbstractEnumType
{
    const FREELANCE = 'f';
    const EMPLOYEE = 'e';

    protected static $choices = array(
        self::FREELANCE => 'Libero Professionista',
        self::EMPLOYEE => 'Dipendente',
    );
}
