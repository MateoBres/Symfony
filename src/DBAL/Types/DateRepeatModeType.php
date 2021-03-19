<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 06/02/15
 * Time: 12.14
 */

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class DateRepeatModeType extends AbstractEnumType
{
  const DAILY     = 1;
  const WEEKLY    = 2;
  const MONTHLY   = 3;
  const YEARLY    = 4;

  protected static $choices = array(
      self::DAILY     => 'Giornaliero',
      self::WEEKLY    => 'Settimanale',
      self::MONTHLY   => 'Mensile',
      self::YEARLY    => 'Annuale',
  );
}