<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class TeacherAssessmentStatusType extends AbstractEnumType
{
    const NOT_VALID = -1;
    const EXPIRED = 0;
    const VALID = 1;

    protected static $choices = array(
        self::NOT_VALID => 'Non valida',
        self::EXPIRED => 'Scaduta',
        self::VALID => 'Valida',
    );

}
