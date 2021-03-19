<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class ExcelDataType extends AbstractEnumType
{
    const TEXT = 'text';
    const NUMBER = 'number';
    const DATE = 'date';
    const TIME = 'time';
    const DATETIME = 'datetime';
    const BOOLEAN = 'bool';
    const LIST = 'list';

    protected static $choices = array(
        self::TEXT => 'Testo',
        self::NUMBER => 'Numero',
        self::DATE => 'Data',
        self::TIME => 'Ora',
        self::DATETIME => 'Data ora',
        self::BOOLEAN => 'S/N',
        self::LIST => 'Lista',
    );
}
