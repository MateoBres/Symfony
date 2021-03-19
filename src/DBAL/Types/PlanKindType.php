<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class PlanKindType extends AbstractEnumType
{
    const FUNDING = 'f';
    const RESOLUTION = 'r';
    const MARKET = 'm';

    protected static $choices = [
        self::FUNDING => 'Finanziato',
        self::RESOLUTION => 'Delibere',
        self::MARKET => 'Mercato',
    ];

    protected static $codes = [
        self::FUNDING => 'FN',
        self::RESOLUTION => 'DL',
        self::MARKET => 'ML',
    ];

    public static function getCode(string $type)
    {
        return self::$codes[$type] ?? '';
    }

    public static function getTypeFromCode(string $code)
    {
        foreach (self::$codes as $k => $c) {
            if ($c = $code) return $k;
        }
        return '';
    }
}
