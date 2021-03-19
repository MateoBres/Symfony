<?php

namespace App\Tests\ContactFlock\Roles;

use App\Tests\Functional\BaseTestCase;

class FundingAgencyControllerTest extends BaseTestCase
{
    const SUB_MENU_SELECTOR = 'a.enti-finanziatori-link';
    const FORM_ERRORS_CLASS = '.entity_form-errors';
    const CREATE_LINK_CLASS = '.crea-link';
    const MAIN_MENU_SELECTOR = "li[data-target='#sub-contatti']";
    const ENTITY_MENU_NODE = "ul#sub-enti-finanziatori";
    const FORM_ROOT_NAME = 'contactflock_roles_funding_agency';
    const BASE_ROUTE_PATH = '/admin/contact_flock_roles_funding_agency/';
    const REPOSITORY_NAMESPACE = 'App\Entity\ContactFlock\Roles\FundingAgency';

    public function testAsAdminUserICanDeleteAFundingAgencyFromListPage()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/FundingAgencyFixtures.yaml",
            ]
        );

        $fAgencyDBCount = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

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
        $this->assertSame($fAgencyDBCount-1, $listCount);
    
    }

    public function testAsAdminUserICanSeeTenFundingAgencyInTheList()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/FundingAgencyFixtures.yaml",
            ]
        );

        $fAgencyDBCount = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityListPage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::BASE_ROUTE_PATH);

        $crawler = $this->client->refreshCrawler();

        $listCount = $crawler->filter('tbody.table-striped tr')->count();
        $this->assertSame($fAgencyDBCount, $listCount);
    }

    public function testAsAdminUserICanEditAFundingAgencyData()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/FundingAgencyFixtures.yaml",
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
        $this->client->refreshCrawler();

        $this->assertSame(self::$baseUri.self::BASE_ROUTE_PATH.$entityId.'/edit', $this->client->getCurrentURL());

        $form = $this->getAdminForm();

        $form[self::FORM_ROOT_NAME.'[contact][businessName]']->setValue('Regione Lombardia');

        $this->client->submit($form);

        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertContains('REGIONE LOMBARDIA', $crawler->filter('td#contact-fullName-value')->text());
    }

    public function testAsAdminUserGetErrorMessagesWhenCreatingFundingAgencyWithEmptyRequiredFields()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->client->refreshCrawler();

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

    public function testAsAdminUserICanCreateAFundingAgencyFillingAllRequiredFields()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );

        $this->client->refreshCrawler();

        $form = $this->getAdminForm();

        //submit form with empty field
        $this->client->submit($form, [
            self::FORM_ROOT_NAME.'[contact][businessName]' => 'Regione Lombardia',
        ]);

        $this->client->waitFor('#content');

        $crawler = $this->client->refreshCrawler();

        $this->assertContains('REGIONE LOMBARDIA', $crawler->filter('#contact-fullName-value')->text());
    }

    public function testAsAnAdminUserICanCreateAFundingAgencyWithPlacesInfo()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );

        $this->scrollToBottom();

        $form = $this->getAdminForm();

        $form[self::FORM_ROOT_NAME.'[contact][businessName]']->SetValue('Regione Lombardia');

        $ownedPlacesData = $this->populateOwnedPlaces($form);

        $this->client->submit($form);
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertOwnedPlaces($ownedPlacesData, $crawler);
    }

    public function testAsAnAdminUserICanCreateAFundingAgencyWithContactInfos()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );

        $this->scrollToBottom();

        $form = $this->getAdminForm();

        $form[self::FORM_ROOT_NAME.'[contact][businessName]']->SetValue('Regione Lombardia');

        $infos = $this->populateContactInfos($form);

        $this->client->submit($form);
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertContactInfos($infos, $crawler);
    }
}
