<?php

namespace App\DataFixtures;

use App\DBAL\Types\CertificateTypeType;
use App\DBAL\Types\CourseEcmType;
use App\Entity\ContactFlock\Roles\RegionalPartner;
use App\Entity\CourseFlock\Course;
use App\Entity\CourseFlock\CourseEdition;
use App\Entity\CourseFlock\CourseEditionModule;
use App\Entity\CourseFlock\CourseModule;
use App\Entity\CourseFlock\CourseVenue;
use App\Entity\CourseFlock\TrainingPlans\FundingPlan;
use App\Entity\CourseFlock\TrainingPlans\MarketPlan;
use App\Entity\CourseFlock\TrainingPlans\ResolutionPlan;
use App\Entity\CourseFlock\TrainingPlans\TrainingPlan;
use App\Entity\QuestionnaireFlock\Questionnaire;
use App\Entity\TaxonomyFlock\Vocabulary\CourseCategory;
use App\EventListener\CourseFlock\TrainingPlanListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SettingsFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array(
            RoleFixtures::class,
        );
    }

    protected function execute()
    {
        $mainContact = $this->getReference('MAIN_CONTACT');

        $settings = $this->settingsManager->initialize();
        $settings->setMainContact($mainContact);
        $settings->setEcmAccreditationNumber('5368');

        $this->manager->persist($settings);
    }

}
