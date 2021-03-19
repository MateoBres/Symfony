<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class DaysOfWeekType extends AbstractEnumType
{
  const MON  = 'Mon';
  const TUE  = 'Tue';
  const WED  = 'Wed';
  const THU  = 'Thu';
  const FRI  = 'Fri';
  const SAT  = 'Sat';
  const SUN  = 'Sun';

  protected static $choices = array(
    self::MON     => 'Lun',
    self::TUE     => 'Mar',
    self::WED     => 'Mer',
    self::THU     => 'Gio',
    self::FRI     => 'Ven',
    self::SAT     => 'Sab',
    self::SUN     => 'Dom',
  );
}