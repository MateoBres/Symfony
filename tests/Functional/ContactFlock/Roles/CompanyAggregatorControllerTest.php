<?php

namespace App\Tests\Functional\ContactFlock\Roles;

use App\Tests\Functional\BaseTestCase;

class CompanyAggregatorControllerTest extends BaseTestCase
{
    const SUB_MENU_SELECTOR = 'a.concentratori-link';
    const FORM_ERRORS_CLASS = '.entity_form-errors';
    const CREATE_LINK_CLASS = '.crea-link';
    const MAIN_MENU_SELECTOR = "li[data-target='#sub-contatti']";
    const ENTITY_MENU_NODE = "ul#sub-concentratori";
    const FORM_ROOT_NAME = 'contactflock_roles_company_aggregator';
    const BASE_ROUTE_PATH = '/admin/contact_flock_roles_company_aggregator/';
    const REPOSITORY_NAMESPACE = 'App\Entity\ContactFlock\Roles\CompanyAggregator';

    public function testAsAdminUserICanDeleteACompanyAggregatorFromListPage()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/CompanyAggregatorPersonFixtures.yaml",
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/CompanyAggregatorCompanyFixtures.yaml",
            ]
        );

        $companyAggregatorDBCount = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

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
        $this->assertSame($companyAggregatorDBCount-1, $listCount);

    }

    public function testAsAdminUserICanSeeTenCompanyAggregatorInTheList()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/CompanyAggregatorPersonFixtures.yaml",
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/CompanyAggregatorCompanyFixtures.yaml",
            ]
        );

        $companyAggregatorDBCount = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->client->refreshCrawler();

        $this->goToEntityListPage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::BASE_ROUTE_PATH);

        $crawler = $this->client->refreshCrawler();

        $listCount = $crawler->filter('tbody.table-striped tr')->count();
        $this->assertSame($companyAggregatorDBCount, $listCount);
    }

    /**
     * @dataProvider addBasicEditDataProvider
     */
    public function testAsAdminUserICanEditACompanyAggregatorData($contactType, $fields, $expectations)
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/CompanyAggregator".$contactType."Fixtures.yaml",
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

        foreach ($fields as $field) {
            $form[$field['name']]->setValue($field['value']);
        }

        $this->client->submit($form);
        $this->client->waitFor('#content');

        $crawler = $this->client->refreshCrawler();

        foreach($expectations as $expectation){
            $this->assertContains($expectation['expectedValue'], $crawler->filter($expectation['filterNode'])->text());
        }
    }

    public function testAsAdminUserGetErrorMessagesWhenCreatingCompanyAggregatorWithEmptyRequiredFields()
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

    /**
     * @dataProvider addBasicCreationDataProvider
     */
    public function testAsAdminUserICanCreateACompanyAggregatorFillingAllRequiredFields($fields, $contactType, $expectations)
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );

        $form = $this->getAdminForm();

        // select contact type
        $form[self::FORM_ROOT_NAME.'[contact][type]']->select($contactType);
        sleep(5);

        $this->client->submit($form, $fields);
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        foreach($expectations as $expectation){
            $this->assertContains($expectation['expectedValue'], $crawler->filter($expectation['filterNode'])->text());
        }
    }

    public function testAsAnAdminUserICanCreateACompanyAggregatorWithPlacesInfo()
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

    public function testTaxCodeGenerationInCompanyAggregator()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );

        $form = $this->getAdminForm();
        $form[self::FORM_ROOT_NAME.'[contact][type]']->select('p');

        $expectedTaxValue = $this->generateTaxCode($form);

        $this->client->submit($form);
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        if(!$expectedTaxValue)
            $this->fail('codice fiscale non generato');

        $this->assertContains($expectedTaxValue, $crawler->filter('#field-contact-summary')->text());
    }

    public function addBasicCreationDataProvider()
    {
        return array(
            'With Contact Person type data' => array('fields' => [
                self::FORM_ROOT_NAME . '[contact][firstName]' => 'Sara',
                self::FORM_ROOT_NAME . '[contact][lastName]' => 'Di Carlo',
                self::FORM_ROOT_NAME . '[contact][gender]' => 'F'
            ],
                'contactType' => 'p',
                'expectations' => [
                    [
                        'filterNode' => '#contact-fullName-value',
                        'expectedValue' => 'Sara Di Carlo'
                    ],
                    [
                        'filterNode' => '#gender-value',
                        'expectedValue' => 'F'
                    ],
                ]
            ),
            'With Contact Company type data' => array('fields' => [
                self::FORM_ROOT_NAME . '[contact][businessName]' => 'Regione Lombardia',
            ],
                'contactType' => 'c',
                'expectations' => [
                    [
                        'filterNode' => '#contact-fullName-value',
                        'expectedValue' => 'REGIONE LOMBARDIA'
                    ],
                ]
            ),
        );
    }

    public function addBasicEditDataProvider()
    {
        return array(
            'With Contact Person type data' => array('Person', 'fields' => [
                [
                    'name' => self::FORM_ROOT_NAME . '[contact][firstName]',
                    'value' => 'Fernando'
                ],
                [
                    'name' => self::FORM_ROOT_NAME . '[contact][lastName]',
                    'value' => 'Di Carlo'
                ]
            ],
                'expectations' => [
                    [
                        'filterNode' => '#contact-fullName-value',
                        'expectedValue' => 'Fernando Di Carlo'
                    ],
                ]
            ),
            'With Contact Company type data' => array('Company', 'fields' => [
                [
                    'name' => self::FORM_ROOT_NAME . '[contact][businessName]',
                    'value' => 'Regione Lombardia'
                ]
            ],
                'expectations' => [
                    [
                        'filterNode' => '#contact-fullName-value',
                        'expectedValue' => 'REGIONE LOMBARDIA'
                    ],
                ]
            ),
        );
    }
}
