<?php 

namespace App\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Bundle\FrameworkBundle\Test\WebTestAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCaseTrait;

abstract class BaseTestCase extends WebTestCase
{
    use PantherTestCaseTrait;

    protected $client;
    protected $mouse;
    protected $DIC;
    protected $dbManager;
    protected $fixtureLoader;
    protected $baseDir;
    protected $key;

    public function setUp()
    {
        self::bootKernel();

        //method createPantherClient overridden from PantherTestCaseTrait
        $args = $this->getDefaultPantherArguments();
        $this->client = static::createPantherClient([], $args, []);
        $this->client->start();
        $this->mouse = $this->client->getMouse();
        $this->DIC = static::$container;
        $this->baseDir = $this->DIC->getParameter('kernel.project_dir');
        $this->dbManager = $this->DIC->get('doctrine.orm.default_entity_manager');
        $this->fixtureLoader = $this->DIC->get('fidry_alice_data_fixtures.loader.doctrine');
    }

    protected function getDefaultPantherArguments(): array
    {
        // Enable the headless mode unless PANTHER_NO_HEADLESS is defined
        $args = ($_SERVER['PANTHER_NO_HEADLESS'] ?? false) ? ['--start-fullscreen'] : ['--headless', '--window-size=1200,1100', '--disable-gpu'];

        // Disable Chrome's sandbox if PANTHER_NO_SANDBOX is defined or if running in Travis
        if ($_SERVER['PANTHER_NO_SANDBOX'] ?? $_SERVER['HAS_JOSH_K_SEAL_OF_APPROVAL'] ?? false) {
            // Running in Travis, disabling the sandbox mode
            $args[] = '--no-sandbox';
        }

        // Add custom arguments with PANTHER_CHROME_ARGUMENTS
        if ($_SERVER['PANTHER_CHROME_ARGUMENTS'] ?? false) {
            $arguments = explode(' ', $_SERVER['PANTHER_CHROME_ARGUMENTS']);
            $args = array_merge($args, $arguments);
        }

        return $args;
    }

    protected static function createPantherClient(array $options = [], array $arguments = [], array $kernelOptions = []): Client
    {
        $callGetClient = \is_callable([self::class, 'getClient']) && (new \ReflectionMethod(self::class, 'getClient'))->isStatic();
        if (null !== self::$pantherClient) {
            return $callGetClient ? WebTestAssertionsTrait::getClient(self::$pantherClient) : self::$pantherClient;
        }

        self::startWebServer($options);

        self::$pantherClients[0] = self::$pantherClient = Client::createChromeClient(null, $arguments, [], self::$baseUri);

        return $callGetClient ? WebTestAssertionsTrait::getClient(self::$pantherClient) : self::$pantherClient;
    }

    /**
     * custom function:
     * for sqlLite that does not reset autoincrement sequence on delete/truncate tables
     *
     * alternatively, RecreateDatabaseTrait can also be used
     * N.B. both are very slow
     */
    protected function refreshDatabase(){
    
        static $metadatas = null;

        if(is_null($metadatas)){
            $metadatas = $this->dbManager->getMetadataFactory()->getAllMetadata();
        }

        $schemaTool = new SchemaTool($this->dbManager);
        $schemaTool->dropDatabase();

        if(!empty($metadatas)){
            $schemaTool->createSchema($metadatas);
        }
    }

    /* AUTHENTICATION */
    protected function adminLogIn()
    {
        if($this->loggedIn()){
            return;
        }

        $crawler = $this->client->request('GET', '/');
        $this->client->waitFor('#login-form');
        $buttonCrawlerNode = $crawler->selectButton('Login');

        $form = $buttonCrawlerNode->form([
            '_username' => 'admin_test',
            '_password' => 'fer',
        ]);

        $this->client->submit($form);
    }

    protected function loggedIn(){
        $this->client->request('GET', '/');
        $this->client->wait(1);
        return $this->client->getCurrentURL() != self::$baseUri.'/login';
    }
    /* END AUTHENTICATION */

    /* START FORM */
    protected function getAdminForm()
    {   
        $crawler = $this->client->refreshCrawler();
        return $crawler->filter('#admin-form')->form();
    }

    protected function populateOwnedPlaces(&$form, array $data = [])
    {
        $formRootName = $form->getElement()->getAttribute('name');

        $defaultData = [
            ["inputValue" => "via crescenzago, 55, Milano", "expectedValue" => "Via Crescenzago, 55, 20134 Milano Mi, Italia"],
            ["inputValue" => "via farini,78, Milano", "expectedValue" => "Via Carlo Farini, 78, 20159 Milano Mi, Italia"]
        ];

        $addresses = $data ?: $defaultData;

        $crawler = $this->client->refreshCrawler();
        $dataTableId = "#{$formRootName}_contact_ownedPlaces";
        foreach($addresses as $key => $address) {

            $addItemLink = $crawler->filter("{$dataTableId} tr.add-new-item a.add")->link();
            $this->client->click($addItemLink);
            $this->client->waitFor("{$dataTableId} tr.new-entry");

            $form[$formRootName . "[contact][ownedPlaces][{$key}][fullAddress]"]
                ->setValue($address['inputValue']);

            $this->scrollToBottom();

            $cssKey = $key+1;
            sleep(3);
            $this->mouse->clickTo("{$dataTableId} tr:nth-of-type({$cssKey}) span.algolia-autocomplete > pre");

            $blockSaveLinkSelector = "{$dataTableId} tr:nth-of-type({$cssKey}) .action-button-wrapper a.collection-widget-save-modification";
            $saveBlockData = $crawler->filter($blockSaveLinkSelector)->link();
            $this->client->click($saveBlockData);
            sleep(1);
        }

        return $addresses;
    }

    protected function assertOwnedPlaces(array $addresses, $crawler): void
    {
        foreach ($addresses as $key => $address) {
            $expectedValue = $address['expectedValue'];
            $cssKey = $key + 1;
            $this->assertSame($expectedValue, $crawler->filter("table.contact-places tr:nth-of-type({$cssKey}) td")->text());
        }
    }

    protected function populateContactInfos(&$form, array $data = [])
    {
        $formRootName = $form->getElement()->getAttribute('name');

        $defaultData = [
            ['type' => 'email', 'inputValue' => "car@car.it"],
            ['type' => 'telefono', 'inputValue' => "35353453453", 'phoneType' => 'fisso'],
            ['type' => 'telefono', 'inputValue' => "8997765556", 'phoneType' => 'cellulare'],
            ['type' => 'email', 'inputValue' => "ger@ger.it"],
        ];

        $infos = $data ?: $defaultData;

        $tableWrapper = ".contact-sub-entity-infos";

        $crawler = $this->client->refreshCrawler();

        foreach ($infos as $key => $info) {
            $infoType = $info['type'];
            $cssKey = $key + 1;
            $addLink = $crawler->filter("{$tableWrapper} tr.prototype a.add-{$infoType}")->link();
            $this->client->click($addLink);
            $this->client->waitFor("{$tableWrapper} tfoot tr.new-entry:nth-of-type({$cssKey})");

            if ($info['type'] == 'email'){
                $form[$formRootName . "[contact][infos][{$key}][value]"]
                    ->setValue($info['inputValue']);
            }
            if ($info['type'] == 'telefono') {
                $form[$formRootName . "[contact][infos][{$key}][value]"]
                    ->setValue($info['inputValue']);
                $form[$formRootName . "[contact][infos][{$key}][type]"]
                    ->setValue($info['phoneType']);
            }
            sleep(1);
        }

        return $infos;
    }

    protected function assertContactInfos(array $infos, $crawler): void
    {
        foreach ($infos as $key => $info) {
            $this->assertContains($info['inputValue'], $crawler->filter("table#contact-contactable-infos")->text());
        }
    }

    protected function generateTaxCode(&$form)
    {
        $rootFormName = $form->getElement()->getAttribute('name');

        sleep(3);
        $form[$rootFormName.'[contact][firstName]']->SetValue('Rossi');
        $form[$rootFormName.'[contact][lastName]']->SetValue('Andrea');
        $form[$rootFormName.'[contact][gender]']->select('M');

        $this->datePickerSelect($rootFormName.'[contact][birthDate]', "13/03/1984");

        $form[$rootFormName.'[contact][birthCity]']->SetValue('Francavilla, CH');
        $this->client->waitFor('span .aa-suggestions');

        $this->mouse->clickTo('div .aa-suggestion > p');
        sleep(1);

        $taxCode = $form[$rootFormName.'[contact][taxCode]']->getValue();

        return $taxCode;
    }

    protected function datePickerSelect($formFieldNameSelector, $date = null){
        $crawler = $this->client->refreshCrawler();

        $this->mouse->clickTo("input[name='{$formFieldNameSelector}']");
        $this->client->waitFor('div.flatpickr-calendar');

        if(!$date){
            $this->mouse->clickTo('span.flatpickr-day.today');
            sleep(1);
            return;
        } else {
            list ($day, $month, $year) = explode ('/', $date);
            $month = (int)$month;

            $select = $crawler->filter("select.flatpickr-monthDropdown-months");
            $select->click();
            sleep(1);

            $option = $crawler->filter("option.flatpickr-monthDropdown-month")->eq($month-1);
            $option->click();
            sleep(1);

            sleep(1);
            $crawler->findElement(WebDriverBy::cssSelector('.numInput.cur-year'))
                ->clear()->sendKeys($year);
            sleep(1);

            $day = $crawler->filter('span.flatpickr-day:not(.prevMonthDay):not(.nextMonthDay)')->eq($day-1);
            $day->click();
            sleep(1);
        }
    }
    /* END FORM */

    /* MENU NAVIGATION */
    protected function goToEntityListPage($rootMenuSelector, $subMenuSelector, $baseRoutePath)
    {
        $crawler = $this->client->refreshCrawler();

        $this->openRootMenu($rootMenuSelector);
        $this->client->waitFor($subMenuSelector);

        $menuLink = $crawler->filter($subMenuSelector)->link();
        $this->client->click($menuLink);

        $this->client->waitFor('#content');

        $this->assertSame(self::$baseUri.$baseRoutePath, $this->client->getCurrentURL());
    }

    protected function goToEntityCreatePage($rootMenuSelector, $subMenuSelector,
        $entityMenuNode, $createLinkClass, $baseRoutePath)
    {
        $crawler = $this->client->refreshCrawler();

        $this->openRootMenu($rootMenuSelector);
        $this->client->waitFor($subMenuSelector);

        $entityCreateLinkSelector = $entityMenuNode." a".$createLinkClass;

        $this->openEntitySubMenu($subMenuSelector);
        $this->client->waitFor($entityCreateLinkSelector);

        $menuLink = $crawler->filter($entityCreateLinkSelector)->link();
        $this->client->click($menuLink);

        $this->client->waitFor('#content');
    }

    protected function openRootMenu($rootMenuSelector){
        $this->mouse->mouseMoveTo($rootMenuSelector);
        sleep(1);
    }

    protected function openEntitySubMenu($subMenuSelector)
    {
        $this->mouse->mouseMoveTo($subMenuSelector);
        sleep(1);
    }
    /* END MENU NAVIGATION */

    /* PAGE NAVIGATION */
    protected function scrollToBottom(): void
    {
        $this->client->executeScript("window.scrollTo(0, document.body.scrollHeight);");
    }

    protected function scrollToTop(): void
    {
        $this->client->executeScript("window.scrollTo(0, 0);");
    }
    /* END PAGE NAVIGATION */

    protected function removePageLoader()
    {
        $this->client->executeScript("var el = document.getElementsByClassName('se-pre-con'); el[0].remove();");
    }
}