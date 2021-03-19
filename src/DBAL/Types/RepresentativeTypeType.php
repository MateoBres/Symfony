<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 17/02/15
 * Time: 13.56
 */

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class RepresentativeTypeType extends AbstractEnumType
{
  const ADMINISTRATIVE  = 'amministrativo';
  const COMMERCIAL      = 'commerciale';
  const OPERATIVE       = 'operativo';
  const LEGAL           = 'legale';
  const RESPONSIBLE     = 'responsabile';
  const SECURITY_MANAGER= 'security_manager';
  const DIREZIONE       = 'direzione';

  protected static $choices = array(
      self::ADMINISTRATIVE      => 'Amministrativo',
      self::COMMERCIAL          => 'Commerciale',
      self::OPERATIVE           => 'Operativo',
      self::LEGAL               => 'Legale',
      self::RESPONSIBLE         => 'Responsabile',
      self::SECURITY_MANAGER    => 'Security Manager',
      self::DIREZIONE           => 'Direzione',
  );
}