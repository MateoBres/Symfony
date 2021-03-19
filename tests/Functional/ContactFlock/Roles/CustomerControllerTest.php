<?php

namespace App\Tests\Functional\ContactFlock\Roles;

use App\Tests\Functional\BaseTestCase;

class CustomerControllerTest extends BaseTestCase
{
    const SUB_MENU_SELECTOR = 'a.clienti-link';
    const FORM_ERRORS_CLASS = '.entity_form-errors';
    const CREATE_LINK_CLASS = '.crea-link';
    const MAIN_MENU_SELECTOR = "li[data-target='#sub-contatti']";
    const ENTITY_MENU_NODE = "ul#sub-clienti";
    const FORM_ROOT_NAME = 'contactflock_roles_customer';
    const BASE_ROUTE_PATH = '/admin/contact_flock_roles_customer/';
    const REPOSITORY_NAMESPACE = 'App\Entity\ContactFlock\Roles\Customer';

    public function testAsAdminUserICanDeleteACustomerFromListPage()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/CustomerCompanyFixtures.yaml",
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/CustomerPersonFixtures.yaml",
            ]
        );

        $customerDBCount = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

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
        $this->assertSame($customerDBCount-1, $listCount);
    }

    public function testAsAdminUserICanSeeTenCustomerInTheList()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/CustomerCompanyFixtures.yaml",
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/CustomerPersonFixtures.yaml",
            ]
        );

        $customerDBCount = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->client->refreshCrawler();

        $this->goToEntityListPage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::BASE_ROUTE_PATH);

        $crawler = $this->client->refreshCrawler();

        $listCount = $crawler->filter('tbody.table-striped tr')->count();
        $this->assertSame($customerDBCount, $listCount);
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

    /**
     * @dataProvider addBasicEditDataProvider
     */
    public function testAsAdminUserICanEditACustomerData($contactType, $fields, $expectations)
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/Customer".$contactType."Fixtures.yaml",
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

    public function testAsAdminUserGetErrorMessagesWhenCreatingCustomerWithEmptyRequiredFields()
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

    /**
     * @dataProvider addBasicCreationDataProvider
     */
    public function testAsAdminUserICanCreateACustomerFillingAllRequiredFields($fields, $contactType, $expectations)
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

    public function createCustomerWithStudentProvider()
    {
        return [
            'Whit customer that IS NOT also a student' => [
                'isCustomerStudent' => false
            ],
            'Whit customer that IS also a student' => [
                'isCustomerStudent' => true
            ]
        ];
    }

    /**
     * @dataProvider createCustomerWithStudentProvider
     */
    public function testAsAdminUserICanCreateACustomerWithStudents($isCustomerStudent)
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR, self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH
        );

        $form = $this->getAdminForm();

        $customerFullName = 'Sara Di Carlo';

        $form[self::FORM_ROOT_NAME.'[contact][firstName]']->SetValue('Sara');
        $form[self::FORM_ROOT_NAME.'[contact][lastName]']->SetValue('Di Carlo');
        $form[self::FORM_ROOT_NAME.'[contact][gender]']->select('F');

        sleep(1);
        $this->mouse->clickTo('#admin-form-titles li[data-stepTabId="students"]');
        sleep(2);

        $studentsData = $this->populateStudents($form, [], $isCustomerStudent);

        $this->client->submit($form);
        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        foreach($studentsData as $student){
            $fullName = $student['firstName']." ".$student['lastName'];
            $this->assertContains($fullName, $crawler->filter('td#students-value')->text());
        }

        if($isCustomerStudent)
            $this->assertNotContains($customerFullName,
                $crawler->filter('td#students-value')->text(),
                'Customer name should not be included in the students list'
            );
    }

    private function populateStudents(&$form, array $data = [], $isCustomerAStudent = false)
    {
        $defaultData = [
            ['firstName' => 'Cristina', 'lastName'=> 'Di Carlo', 'gender' => 'F'],
            ['firstName' => 'Mara', 'lastName'=> 'Di Carlo', 'gender' => 'F']
        ];

        $students = $data ?: $defaultData;

        $formRootName = $form->getElement()->getAttribute('name');

        $crawler = $this->client->refreshCrawler();

        $studentTableWrapper = '#contactflock_roles_customer_students';

        if($isCustomerAStudent){
            $this->scrollToTop();
            sleep(1);
            //click to checkbox when input field is not visible
            $this->mouse->clickTo("div.isStudent-field > label");
            sleep(1);
        }

        foreach ($students as $key => $student) {
            $cssKey = $key + 1;

            $addStudentLink = $crawler->filter("{$studentTableWrapper} a.add-allievo")->link();
            $this->client->click($addStudentLink);
            $this->scrollToBottom();
            $this->client->waitFor("tr.new-entry:nth-of-type({$cssKey})");

            $form[$formRootName . "[students][{$key}][contact][firstName]"]->setValue($student['firstName']);
            $form[$formRootName . "[students][{$key}][contact][lastName]"]->setValue($student['lastName']);
            $form[$formRootName . "[students][{$key}][contact][gender]"]->select($student['gender']);

            sleep(1);
            $blockSaveLinkSelector = "{$studentTableWrapper} tr:nth-of-type({$cssKey}) .action-button-wrapper a.collection-widget-save-modification";
            $saveBlockData = $crawler->filter($blockSaveLinkSelector)->link();
            $this->client->click($saveBlockData);
            sleep(1);
        }

        return $students;
    }

    public function testAsAnAdminUserICanCreateACustomerWithPlacesInfo()
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

    public function testAsAnAdminUserICanCreateACustomerWithContactInfos()
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

    public function testTaxCodeGenerationInCustomer()
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
}
