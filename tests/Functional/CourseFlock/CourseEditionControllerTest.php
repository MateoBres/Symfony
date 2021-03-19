<?php

namespace App\Tests\Functional\CourseFlock;

use App\Tests\Functional\BaseTestCase;

class CourseEditionControllerTest extends BaseTestCase
{
    const SUB_MENU_SELECTOR = 'a.edizioni-link';
    const FORM_ERRORS_CLASS = '.entity_form-errors';
    const CREATE_LINK_CLASS = '.crea-link';
    const MAIN_MENU_SELECTOR = "li[data-target='#sub-corsi']";
    const ENTITY_MENU_NODE = "li[data-target='#sub-edizioni']";
    const FORM_ROOT_NAME = 'courseflock_course_edition';
    const BASE_ROUTE_PATH = '/admin/course_flock_course_edition/';
    const REPOSITORY_NAMESPACE = 'App\Entity\CourseFlock\CourseEdition';

    public function testAsAdminUserICanDeleteACourseEditionFromListPage()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/CourseFlock/CourseFixtures.yaml",
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/RegionalPartnerFixtures.yaml",
                $this->baseDir."/tests/fixtures/Functional/CourseFlock/CourseEditionFixtures.yaml",
            ]
        );

        $courseEditionDBCount = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

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
        $this->assertSame($courseEditionDBCount-1, $listCount);
    }

    public function testAsAdminUserICanSeeTenCourseEditionInTheList()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/CourseFlock/CourseFixtures.yaml",
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/RegionalPartnerFixtures.yaml",
                $this->baseDir."/tests/fixtures/Functional/CourseFlock/CourseEditionFixtures.yaml",
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

    public function testAsAdminUserICanEditACourseEditionData()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/CourseFlock/CourseFixtures.yaml",
                $this->baseDir."/tests/fixtures/Functional/ContactFlock/Roles/RegionalPartnerFixtures.yaml",
                $this->baseDir."/tests/fixtures/Functional/CourseFlock/CourseEditionFixtures.yaml",
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

        $form['courseflock_course_edition[name]']->setValue('Java Edizione 2019');

        $course = $this->dbManager->getRepository('App\Entity\CourseFlock\Course')->findOneBy(['name' => 'Java base']);

        $courseTitle = $course->getName();
        $courseIdCode = $course->getCourseIdCode();

        //autocomplete field
        $form['autocompleter_courseflock_course_edition[course]']->setValue(strtolower($courseTitle));
        //select from autocomplete filter
        $this->client->waitFor('.aa-suggestions');
        $this->mouse->clickTo('.aa-suggestion > p');

        //wait form course modules section
        $this->client->waitFor('#courseflock_course_edition_courseModuleEditions');
        $this->client->submit($form);

        $this->assertSame(self::$baseUri.self::BASE_ROUTE_PATH.$entityId.'/show', $this->client->getCurrentURL());

        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertSame('Java Edizione 2019',$crawler->filter('#row1-col1-edition tbody td#name-value')->text());
        $this->assertSame($courseTitle." ({$courseIdCode})", $crawler->filter('#row1-col1-edition tbody td#course-value')->text());
    }

    public function testAsAdminUserGetErrorMessagesWhenCreatingCourseEditionWithEmptyRequiredFields()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');
                 
        $this->goToCreateCourseEditionPage();
        
        $form = $this->getAdminForm();
        //submit form with empty field
        $this->client->submit($form, []);
        $this->client->waitFor('#content');
        
        $crawler = $this->client->refreshCrawler();

        $this->assertTrue($crawler->filter(self::FORM_ERRORS_CLASS)->isDisplayed());
    }

    public function testAsAdminUserICanCreateACourseEditionFillingAllRequiredFields()
    {             
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToCreateCourseEditionPage();

        $form = $this->getAdminForm();

        $form['courseflock_course_edition[name]']->setValue('Java Edizione 2019');
        $form['courseflock_course_edition[state]']->setValue('3');
        //autocomplete field
        $form['autocompleter_courseflock_course_edition[course]']->setValue('java base');

        //select from autocomplete filter
        $this->client->waitFor('.aa-suggestions');
        $this->mouse->clickTo('.aa-suggestion p');

        //wait form course modules section to appair
        $this->client->waitFor('#courseflock_course_edition_courseModuleEditions');

        $this->client->submit($form);

        $this->client->waitFor('#content');

        $crawler = $this->client->refreshCrawler();

        $this->assertContains('Dettagli Edizione', $this->client->getTitle());
        $this->assertContains('Java Edizione 2019', $crawler->filter('#name-value')->text());
        $this->assertContains('Java base', $crawler->filter('#course-value')->text());
    }

    protected function goToCourseEditionListPage()
    {
        $crawler = $this->client->refreshCrawler();
        
        $this->openCourseMenu();
        $this->client->waitFor(self::SUB_MENU_SELECTOR);

        $menuLink = $crawler->filter(self::SUB_MENU_SELECTOR)->link();
        $crawler = $this->client->click($menuLink);

        $this->client->waitFor('#content');

        $this->assertSame(self::$baseUri.'/admin/course_flock_course_edition/', $this->client->getCurrentURL());
    }

    private function goToCreateCourseEditionPage()
    {
        $crawler = $this->client->refreshCrawler();

        $this->openCourseMenu();
        $this->client->waitFor(self::SUB_MENU_SELECTOR);
        
        $courseEditionCreateLinkSelector = self::ENTITY_MENU_NODE." a".self::CREATE_LINK_CLASS;

        $this->openCourseEditionSubMenu();
        $this->client->waitFor($courseEditionCreateLinkSelector);
        
        $menuLink = $crawler->filter($courseEditionCreateLinkSelector)->link();
        $crawler = $this->client->click($menuLink);

        $this->client->waitFor('#content');
        $this->assertSame(self::$baseUri.'/admin/course_flock_course_edition/new', $this->client->getCurrentURL());
    }

    private function openCourseEditionSubMenu()
    {
        $this->mouse->mouseMoveTo(self::SUB_MENU_SELECTOR);
    }

    private function openCourseMenu(){
        $this->mouse->mouseMoveTo(self::MAIN_MENU_SELECTOR);
    }
}
