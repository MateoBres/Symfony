<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class CourseEcmType extends AbstractEnumType
{
    const RES = 'RES';
    const FAD = 'FAD';
    const FSC = 'FSC';

    protected static $choices = array(
        self::RES => 'Residenziale',
        self::FAD => 'Formazione a Distanza',
        self::FSC => 'Formazione sul Campo',
    );
}


