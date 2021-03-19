<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class CertificateTypeType extends AbstractEnumType
{
    const ECM = 'ECM';
    const PARTECIPAZIONE = 'PAR';
    const FREQUENZA = 'FRE';
    const FREQUENZA_TEST = 'FRT';
    const REGIONE_EMILIA_ROMAGNA = 'RER';

    protected static $choices = array(
        self::ECM => 'Attestato ECM',
        self::PARTECIPAZIONE => 'Attestato di partecipazione',
        self::FREQUENZA => 'Attestato di frequenza',
        self::FREQUENZA_TEST => 'Attestato di frequenza con test finale',
        self::REGIONE_EMILIA_ROMAGNA => 'Attestato Regione Emilia Romagna',
    );

    public static function getChoicesForCourse(): array
    {
        $choices = self::$choices;
        unset($choices[self::ECM]);
        return \array_flip($choices);
    }
}
