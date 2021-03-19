<?php

namespace App\Tests\Functional\CourseFlock;

use App\Tests\Functional\BaseTestCase;
use Facebook\WebDriver\WebDriverBy;

class CourseControllerTest extends BaseTestCase
{

    const SUB_MENU_SELECTOR = 'a.format-corsuali-link';
    const FORM_ERRORS_CLASS = '.entity_form-errors';
    const CREATE_LINK_CLASS = '.crea-link';
    const MAIN_MENU_SELECTOR = "li[data-target='#sub-corsi']";
    const ENTITY_MENU_NODE = "ul#sub-corsi";
    const FORM_ROOT_NAME = 'courseflock_course';
    const BASE_ROUTE_PATH = '/admin/course_flock_course/';
    const REPOSITORY_NAMESPACE = 'App\Entity\CourseFlock\Course';

    public function testAsAdminUserICanDeleteACourseFromListPage()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/CourseFlock/CourseFixtures.yaml",
            ]
        );

        $courseDBCount = count($this->dbManager->getRepository(self::REPOSITORY_NAMESPACE)->findAll());

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
        $this->assertSame($courseDBCount-1, $listCount);
    }

    public function testAsAdminUserICanSeeTenCourseInTheList()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/CourseFlock/CourseFixtures.yaml",
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

    public function testAsAdminUserICanEditACourseData()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/CourseFlock/CourseFixtures.yaml",
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

        $form[self::FORM_ROOT_NAME.'[name]']->setValue('Titolo corso test');

        $this->client->submit($form);

        $this->assertSame(self::$baseUri.self::BASE_ROUTE_PATH.$entityId.'/show', $this->client->getCurrentURL());

        $this->client->waitFor('#content');
        $crawler = $this->client->refreshCrawler();

        $this->assertSame('Titolo corso test',$crawler->filter('#row1-col1-course tbody td#name-value')->text());
    }

    public function testAsAdminUserGetErrorMessagesWhenCreatingCourseWithEmptyRequiredFields()
    {
        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR,self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH);
        
        $form = $this->getAdminForm();
        //submit form with empty field
        $this->client->submit($form, []);
        $this->client->waitFor('#content');
        
        $crawler = $this->client->refreshCrawler();

        $this->assertTrue($crawler->filter(self::FORM_ERRORS_CLASS)->isDisplayed());
    }

    public function testAsAdminUserICanCreateACourseFillingAllRequiredFields()
    {
        $this->fixtureLoader->load([
                $this->baseDir."/tests/fixtures/Functional/CourseFlock/CourseFixtures.yaml",
            ]
        );

        $this->adminLogIn();
        $this->client->waitFor('#content');

        $this->goToEntityCreatePage(self::MAIN_MENU_SELECTOR,
            self::SUB_MENU_SELECTOR,self::ENTITY_MENU_NODE,
            self::CREATE_LINK_CLASS,self::BASE_ROUTE_PATH);

        $crawler = $this->client->refreshCrawler();

        // add 1 module to the form
        $link = $crawler->filter('a.add-modulo')->link();
        $this->client->click($link);

        $this->client->waitFor('input#courseflock_course_courseModules_0_name');

        $form = $this->getAdminForm();

        $categoryValue = $crawler->filterXPath(
            "//select[@id='courseflock_course_category']/option[contains(.,'Informatica')]")
            ->attr('value');

        $this->client->submit($form, [
            'courseflock_course[name]' => 'java',
            'courseflock_course[category]' => $categoryValue,
            'courseflock_course[courseModules][0][name]' => 'Modulo 1',
            'courseflock_course[courseModules][0][duration]' => '12:00'
        ]);

        $this->client->waitFor('#content');

        $crawler = $this->client->refreshCrawler();

        $this->assertSame('java', $crawler->filter('#row1-col1-course tbody td#name-value')->text());
        $this->assertSame('Informatica', $crawler->filter('#row1-col1-course tbody td#category-value')->text());
        $this->assertSame('12:00', $crawler->filter('#row1-col1-course tbody td#duration-value')->text());
        $this->assertSame('Modulo 1', $crawler->filter('#module-name')->text());
    }
}
