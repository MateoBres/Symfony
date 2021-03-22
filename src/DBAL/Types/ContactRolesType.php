<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class ContactRolesType extends AbstractEnumType
{
    const PROJECT_ADMINISTRATIVE_DIRECTOR = 'pad';
    const BUILDING_SUPERINTENDENT = 'bs';
    const SINGLE_FAMILY_CLIENT = 'sfc';
    const PLANIMETRIC_SURVEY_AND_CADASTRAL_PROFESSIONAL = 'pscp';
    const BUILDING_PRACTICE_PROFESSIONAL = 'bpp';
    const SEISMIC_PROFESSIONAL = 'sp';
    const METRIC_CALCULATING_PROFESSIONAL = 'mcp';
    const THERMO_TECHNIC_PROFESSIONAL = 'ttp';
    const CONSTRUCTION_MANAGER = 'cm';
    const SECURITY_MANAGER = 'sm';
    const TEST_DRIVER = 'td';
    const TECHNICAL_AXISVERTOR = 'ta';
    const FISCAL_AXISVERTOR = 'fa';

    protected static $choices = array(
        self::PROJECT_ADMINISTRATIVE_DIRECTOR => 'Responsabile amministrativo di commessa',
        self::BUILDING_SUPERINTENDENT => 'Amministratore di condominio',
        self::SINGLE_FAMILY_CLIENT => 'Cliente unifamiliare',
        self::PLANIMETRIC_SURVEY_AND_CADASTRAL_PROFESSIONAL => 'Professionista che fa i rilievi planimetrici e catastali',
        self::BUILDING_PRACTICE_PROFESSIONAL => 'Professionista che fa le pratiche edilizie',
        self::SEISMIC_PROFESSIONAL => 'Professionista che fa la parte sismica',
        self::METRIC_CALCULATING_PROFESSIONAL => 'Professionista che fa i computi metrici',
        self::THERMO_TECHNIC_PROFESSIONAL => 'Professionista che fa la parte termotecnica',
        self::CONSTRUCTION_MANAGER => 'Direttore dei lavori',
        self::SECURITY_MANAGER => 'Responsabile della sicurezza',
        self::TEST_DRIVER => 'Collaudatore',
        self::TECHNICAL_AXISVERTOR => 'Asseveratore tecnico',
        self::FISCAL_AXISVERTOR => 'Asseveratore fiscale',
    );
}