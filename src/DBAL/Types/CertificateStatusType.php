<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class CertificateStatusType extends AbstractEnumType
{
    const NOT_ISSUED = 'NE';
    const VALID = 'VA';
    const NOT_ACHIEVED = 'NC';
    const EXPIRING = 'IS';
    const EXPIRED = 'SC';

    protected static $choices = array(
        self::NOT_ISSUED => 'Non Emesso',
        self::VALID => 'Valido',
        self::NOT_ACHIEVED => 'Non Conseguito',
        self::EXPIRING => 'In Scadenza',
        self::EXPIRED => 'Scaduto',
    );

}
