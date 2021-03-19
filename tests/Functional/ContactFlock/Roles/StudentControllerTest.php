<?php

namespace App\Tests\Functional\ContactFlock\Roles;

use App\Tests\Functional\BaseTestCase;

class StudentControllerTest extends BaseTestCase
{
    const SUB_MENU_SELECTOR = 'a.allievi-link';
    const FORM_ERRORS_CLASS = '.entity_form-errors';
    const CREATE_LINK_CLASS = '.crea-link';
    const MAIN_MENU_SELECTOR = "li[data-target='#sub-contatti']";
    const ENTITY_MENU_NODE = "ul#sub-allievi";
    const FORM_ROOT_NAME = 'contactflock_roles_student';
    const BASE_ROUTE_PATH = '/admin/contact_flock_roles_student/';
    const REPOSITORY_NAMESPACE = 'App\Entity\ContactFlock\Roles\Student';

    public function testAsAdminUserICanDeleteAStudentFromListPage()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/StudentFixtures.yaml",
            ]
        );

        $studentDBCount = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityListPage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::BASE_ROUTE_PATH);

        $crawler = $this->client->refreshCrawler();

        $editLinkNode = $crawler->filter('tbody > tr a.delete-entity');
        $editLinkNode->attr('id');
        
        $this->client->click($editLinkNode->link());
        
        $this->client->waitFor('button.swal2-confirm');
        $this->mouse->clickTo('button.swal2-confirm');

        $this->assertSame(self::$baseUri.self::BASE_ROUTE_PATH, $this->client->getCurrentURL());

        $this->client->waitFor('tbody.table-striped');
        $crawler = $this->client->refreshCrawler();

        $listCount = $crawler->filter('tbody.table-striped tr')->count();
        $this->assertSame($studentDBCount-1, $listCount);
    
    }

    public function testAsAdminUserICanSeeTenStudentInTheList()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/StudentFixtures.yaml",
            ]
        );

        $studentDBCount = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityListPage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::BASE_ROUTE_PATH);

        $crawler = $this->client->refreshCrawler();

        $listCount = $crawler->filter('tbody.table-striped tr')->count();
        $this->assertSame($studentDBCount, $listCount);
    }

    public function testAsAdminUserICanEditAStudentData()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/StudentFixtures.yaml",
            ]
        );
               
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityListPage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::BASE_ROUTE_PATH);

        $crawler = $this->client->refreshCrawler();

        $editLinkNode = $crawler->filter('tbody > tr a.entity-edit');
        $entityId = $editLinkNode->attr('id');
        
        $this->client->click($editLinkNode->link());
        $this->client->waitFor('#content');

        $this->assertSame(self::$baseUri.self::BASE_ROUTE_PATH.$entityId.'/edit', $this->client->getCurrentURL());

        $form = $this->getAdminForm();

        $form[self::FORM_ROOT_NAME.'[contact][firstName]']->setValue('Fernando');
        $form[self::FORM_ROOT_NAME.'[contact][lastName]']->setValue('Di Carlo');

        $this->client->submit($form);
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertContains('Fernando Di Carlo', $crawler->filter('td#contact-name-value')->text());
    }

    public function testAsAdminUserGetErrorMessagesWhenCreatingStudentWithEmptyRequiredFields()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );
        
        $form = $this->getAdminForm();
        //submit form with empty field
        $this->client->submit($form, []);
        $this->client->waitFor('#content');
        
        $crawler = $this->client->refreshCrawler();

        $this->assertTrue($crawler->filter(self::FORM_ERRORS_CLASS)->isDisplayed());
    }

    public function testAsAdminUserICanCreateAStudentFillingAllRequiredFields()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );

        $form = $this->getAdminForm();

        //radio
        $form[self::FORM_ROOT_NAME.'[contact][gender]']->select('F');

        $this->client->submit($form, [
            self::FORM_ROOT_NAME.'[contact][firstName]' => 'Sara',
            self::FORM_ROOT_NAME.'[contact][lastName]' => 'Di Carlo',
        ]);

        $crawler = $this->client->refreshCrawler();

        $this->assertContains('Sara Di Carlo', $crawler->filter('#contact-name-value')->text());
        $this->assertContains('F', $crawler->filter('#gender-value')->text());
    }

    public function testAsAnAdminUserICanCreateAStudentWithPlacesInfo()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );

        $this->scrollToBottom();

        $form = $this->getAdminForm();

        $form[self::FORM_ROOT_NAME.'[contact][gender]']->select('F');
        $form[self::FORM_ROOT_NAME.'[contact][firstName]']->SetValue('Sara');
        $form[self::FORM_ROOT_NAME.'[contact][lastName]']->SetValue('Di Carlo');

        $ownedPlacesData = $this->populateOwnedPlaces($form);

        $this->client->submit($form);
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertOwnedPlaces($ownedPlacesData, $crawler);
    }

    public function testAsAnAdminUserICanCreateACompanyAggregatorWithContactInfos()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );

        $this->scrollToBottom();

        $form = $this->getAdminForm();

        $form[self::FORM_ROOT_NAME.'[contact][firstName]']->SetValue('Sara');
        $form[self::FORM_ROOT_NAME.'[contact][lastName]']->SetValue('Di Carlo');
        $form[self::FORM_ROOT_NAME.'[contact][gender]']->select('F');

        $infos = $this->populateContactInfos($form);

        $this->client->submit($form);
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertContactInfos($infos, $crawler);
    }

    public function testAsAnAdminUserICanCreateAStudentWithContactInfos()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );

        $this->scrollToBottom();

        $form = $this->getAdminForm();

        $form[self::FORM_ROOT_NAME.'[contact][firstName]']->SetValue('Sara');
        $form[self::FORM_ROOT_NAME.'[contact][lastName]']->SetValue('Di Carlo');
        $form[self::FORM_ROOT_NAME.'[contact][gender]']->select('F');

        $infos = $this->populateContactInfos($form);

        $this->client->submit($form);
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertContactInfos($infos, $crawler);
    }

    public function testTaxCodeGenerationInStudent()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );

        $form = $this->getAdminForm();

        $expectedTaxValue = $this->generateTaxCode($form);

        $this->client->submit($form);
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        if(!$expectedTaxValue)
            $this->fail('codice fiscale non generato');

        $this->assertContains($expectedTaxValue, $crawler->filter('#field-contact-summary')->text());
    }
}
