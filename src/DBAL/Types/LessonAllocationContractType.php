<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class LessonAllocationContractType extends AbstractEnumType
{
    const ASSIGNMENT = 'A';
    const ENROLLMENT = 'E';

    protected static $choices = [
        self::ASSIGNMENT => 'Assignment',
        self::ENROLLMENT => 'Enrollment',
    ];
}
