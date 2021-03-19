<?php

namespace App\Service\SettingsFlock;

use App\DBAL\Types\SettingsKindType;
use App\Entity\CommercialFlock\Contracts\Enrollment;
use App\Entity\CommercialFlock\Assignment\StudentAttendance;
use App\Entity\SettingsFlock\Settings;
use App\Entity\UserFlock\User;
use Doctrine\ORM\EntityManagerInterface;

class SettingsManager
{
    private $em;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function __call($funName, $arguments)
    {
        $settings = $this->getGlobalSettings();
        if (method_exists($settings, $funName)) {
            $result = $settings->{$funName}($arguments);
            $this->em->persist($settings);
            return $result;
        }
    }

    public function initialize()
    {
        $settings = $this->getGlobalSettings();
        if (!$settings) {
            $settings = new Settings();
            $settings->setType(SettingsKindType::GLOBALS);
            $this->em->persist($settings);
            $this->em->flush();
        }

        return $settings;
    }

    public function getGlobalSettings()
    {
        $repository = $this->em->getRepository(Settings::class);
        return $repository->getGlobalSettings();
    }

    public function getUserSettings()
    {
        $repository = $this->em->getRepository(Settings::class);
        return $repository->getUserSettings();
    }

    public function get(string $property)
    {
        $settings = $this->getGlobalSettings();

        if (method_exists($settings, $property)) {
            return $settings->{$property}();
        } else if (method_exists($settings, 'get' . ucfirst($property))) {
            return $settings->{'get' . ucfirst($property)}();
        }

        return null;
    }

    public function set(string $property, $value)
    {
        $settings = $this->getGlobalSettings();

        if (method_exists($settings, 'set' . ucfirst($property))) {
            $settings->{'set' . ucfirst($property)}($value);
            $this->em->persist($settings);
        }

        $this->em->flush();
    }

    public function getFromUser(string $property)
    {
        $settings = $this->getUserSettings();

        if (method_exists($settings, $property)) {
            return $settings->{$property}();
        } else if (method_exists($settings, 'get' . ucfirst($property))) {
            return $settings->{'get' . ucfirst($property)}();
        }

        return null;
    }

    public function setForUser(string $property, $value)
    {
        $settings = $this->getUserSettings();

        if (method_exists($settings, 'set' . ucfirst($property))) {
            $settings->{'set' . ucfirst($property)}($value);
            $this->em->persist($settings);
        }

        $this->em->flush();
    }
}