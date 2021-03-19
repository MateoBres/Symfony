<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 17/02/15
 * Time: 12.35
 */

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class PersonGenderType extends AbstractEnumType
{
    const MALE = 'm';
    const FEMALE = 'f';

    protected static $choices = array(
        self::MALE => 'M',
        self::FEMALE => 'F',
    );

}