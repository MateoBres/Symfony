<?php

namespace App\Tests\Functional\ContactFlock\Roles;

use App\Tests\Functional\BaseTestCase;

class RegionalPartnerControllerTest extends BaseTestCase
{
    const SUB_MENU_SELECTOR = 'a.partner-territoriali-link';
    const FORM_ERRORS_CLASS = '.entity_form-errors';
    const CREATE_LINK_CLASS = '.crea-link';
    const MAIN_MENU_SELECTOR = "li[data-target='#sub-contatti']";
    const ENTITY_MENU_NODE = "ul#sub-partner-territoriali";
    const FORM_ROOT_NAME = 'contactflock_roles_regional_partner';
    const BASE_ROUTE_PATH = '/admin/contact_flock_roles_regional_partner/';
    const REPOSITORY_NAMESPACE = 'App\Entity\ContactFlock\Roles\RegionalPartner';

    public function testAsAdminUserICanDeleteARegionalPartnerFromListPage()
    {   
        // $this->refreshDatabase();
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/RegionalPartnerFixtures.yaml",
            ]
        );

        $regionalPartner = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

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
        $this->assertSame($regionalPartner-1, $listCount);
    
    }

    public function testAsAdminUserICanSeeTenRegionalPartnerInTheList()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/RegionalPartnerFixtures.yaml",
            ]
        );

        $regionalPartner = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

        $this->adminLogIn();
        $this->client->waitFor('#content');
        $this->client->refreshCrawler();

        $this->goToEntityListPage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::BASE_ROUTE_PATH);

        $crawler = $this->client->refreshCrawler();

        $listCount = $crawler->filter('tbody.table-striped tr')->count();
        $this->assertSame($regionalPartner, $listCount);
    }

    public function testAsAdminUserICanEditARegionalPartnerData()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/RegionalPartnerFixtures.yaml",
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

        $form[self::FORM_ROOT_NAME.'[contact][businessName]']->setValue('Sinervis Consulting');

        $this->client->submit($form);
        
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertContains('SINERVIS CONSULTING', $crawler->filter('td#contact-fullName-value')->text());
    }

    public function testAsAdminUserGetErrorMessagesWhenCreatingRegionalPartnerWithEmptyRequiredFields()
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
        $crawler = $this->client->refreshCrawler();

        $this->assertTrue($crawler->filter(self::FORM_ERRORS_CLASS)->isDisplayed());
    }

    public function testAsAdminUserICanCreateARegionalPartnerFillingAllRequiredFields()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );

        $form = $this->getAdminForm();

        //submit form with empty field
        $this->client->submit($form, [
            self::FORM_ROOT_NAME.'[contact][businessName]' => '360 Life',
            self::FORM_ROOT_NAME.'[code]' => 'LF',
        ]);

        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertContains('360 LIFE', $crawler->filter('#contact-fullName-value')->text());
        $this->assertContains('LF', $crawler->filter('#code-value')->text());
    }

    public function testAsAnAdminUserICanCreateARegionalPartnerWithPlacesInfo()
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
        $form[self::FORM_ROOT_NAME.'[code]']->SetValue('LF');

        $ownedPlacesData = $this->populateOwnedPlaces($form);

        $this->client->submit($form);
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertOwnedPlaces($ownedPlacesData, $crawler);
    }

    public function testAsAnAdminUserICanCreateARegionalPartnerWithContactInfos()
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
        $form[self::FORM_ROOT_NAME.'[code]']->SetValue('LF');

        $infos = $this->populateContactInfos($form);

        $this->client->submit($form);
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertContactInfos($infos, $crawler);
    }
}
