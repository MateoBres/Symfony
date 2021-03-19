<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class OfficeTypeType extends AbstractEnumType
{
  const LEGAL            = 'legale';
  const OPERATIVE        = 'operativa';
  const ADMINISTRATIVE   = 'amministrativa';

  protected static $choices = array(
      self::LEGAL             => 'Legale',
      self::OPERATIVE         => 'Operativa',
      self::ADMINISTRATIVE    => 'Amministrativa'
  );
}

