<?php

namespace App\Tests\Functional\CourseFlock;

use App\Tests\Functional\BaseTestCase;

class TrainingPlanControllerTest extends BaseTestCase
{
    const SUB_MENU_SELECTOR = 'a.piani-link';
    const FORM_ERRORS_CLASS = '.entity_form-errors';
    const CREATE_LINK_CLASS = '.crea-link';
    const MAIN_MENU_SELECTOR = "li[data-target='#sub-corsi']";
    const ENTITY_MENU_NODE = "li[data-target='#sub-piani']";
    const FORM_ROOT_NAME = 'courseflock_training_plans_training_plan';
    const BASE_ROUTE_PATH = '/admin/course_flock_training_plans_training_plan/';
    const REPOSITORY_NAMESPACE = 'App\Entity\CourseFlock\TrainingPlans\TrainingPlan';

    public function testAsAdminUserICanDeleteATrainingPlanFromListPage()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/FundingAgencyFixtures.yaml",
                $this->baseDir."/tests/fixtures/Functional/CourseFlock/TrainingPlanFixtures.yaml",
            ]
        );

        $tPlanDBCount = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityListPage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::BASE_ROUTE_PATH);
        
        $crawler = $this->client->refreshCrawler();

        $editLinkNode = $crawler->filter('tbody > tr a.delete-entity');
        
        $this->client->click($editLinkNode->link());
        
        $this->client->waitFor('button.swal2-confirm');
        $this->mouse->clickTo('button.swal2-confirm');

        $this->assertSame(self::$baseUri.self::BASE_ROUTE_PATH, $this->client->getCurrentURL());

        $this->client->waitFor('tbody.table-striped');
        $crawler = $this->client->refreshCrawler();

        $listCount = $crawler->filter('tbody.table-striped tr')->count();
        $this->assertSame($tPlanDBCount-1, $listCount);
    }

    public function testAsAdminUserICanSeeTenTrainingPlanInTheList()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/FundingAgencyFixtures.yaml",
                $this->baseDir."/tests/fixtures/Functional/CourseFlock/TrainingPlanFixtures.yaml",
            ]
        );

        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityListPage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::BASE_ROUTE_PATH);

        $crawler = $this->client->refreshCrawler();

        $listCount = $crawler->filter('tbody.table-striped tr')->count();
        $this->assertSame(10, $listCount);
    }

    /**
     * @dataProvider creationProvider
     */
    public function testAsAdminUserICanCreatePlansWithFillingRequiredFields($planConfig, $fields)
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS, $planConfig['baseRoutePath']
        );

        $form = $this->getAdminForm();
        $formRootName = $form->getElement()->getAttribute('name');

        $planType = $planConfig['type'];
        $baseRoutePath = $planConfig['baseRoutePath'];

        $crawler = $this->client->refreshCrawler();
        $planChoice = $crawler->filterXpath(
            "//*[@id='{$formRootName}_type']//input[@value='{$planType}']"
        );

        if(!$planChoice->getAttribute('checked')) {
            $planChoice->click();
            $this->client->waitFor('#content');

            $this->assertSame(self::$baseUri . $baseRoutePath . 'new', $this->client->getCurrentURL());

            $form = $this->getAdminForm();
            $formRootName = $form->getElement()->getAttribute('name');
        }

        foreach($fields as $name => $value){
            $form->get($formRootName."[$name]")->setValue($value);
        }

        $this->client->submit($form);
        $this->client->waitFor('#content');

        $crawler = $this->client->refreshCrawler();

        foreach($fields as $name => $value){
            $this->assertSame($value, $crawler->filter("td#{$name}-value")->text("Field {$name} not found"));
        }

    }

    public function creationProvider()
    {
        return [
            'Create Funding Plan' => [
                'planConfig' => [
                    'type' => 'f',
                    'baseRoutePath' =>'/admin/course_flock_training_plans_funding_plan/'],
                'fields' => [
                    'name' => 'Piano finanziato'
                ]
            ],
            'Create Resolution Plan' =>[
                'planConfig' => [
                    'type' => 'r',
                    'baseRoutePath' =>'/admin/course_flock_training_plans_resolution_plan/'],
                'fields' => [
                    'name' => 'Delibera',
                    'resolution' => 'N° 1234',
                ]
            ],
            'Create Market Plan' => [
                'planConfig' => [
                    'type' => 'm',
                    'baseRoutePath' =>'/admin/course_flock_training_plans_market_plan/'],
                'fields' => [
                    'name' => 'Piano a mercato'
                ]
            ],
        ];
    }
}
