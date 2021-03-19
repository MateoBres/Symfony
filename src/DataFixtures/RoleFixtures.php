<?php

namespace App\DataFixtures;

use App\DBAL\Types\ContactKindType;
use App\DBAL\Types\PersonGenderType;
use App\DBAL\Types\RepresentativeTypeType;
use App\DBAL\Types\ProfessionalPositionType;
use App\Entity\ContactFlock\ContactInfos\Email;
use App\Entity\ContactFlock\ContactInfos\Phone;
use App\Entity\ContactFlock\ContactInfos\Website;
use App\Entity\ContactFlock\Contacts\Company;
use App\Entity\ContactFlock\Contacts\Person;
use App\Entity\ContactFlock\Contacts\SimplePerson;
use App\Entity\ContactFlock\Number\Number;
use App\Entity\ContactFlock\Number\NumberType;
use App\Entity\ContactFlock\Places\House;
use App\Entity\ContactFlock\Places\Office;
use App\Entity\ContactFlock\Roles\CompanyAggregator;
use App\Entity\ContactFlock\Roles\Customer;
use App\Entity\ContactFlock\Roles\EducationProvider;
use App\Entity\ContactFlock\Roles\FundingAgency;
use App\Entity\ContactFlock\Roles\RegionalPartner;
use App\Entity\ContactFlock\Roles\Representative;
use App\Entity\ContactFlock\Roles\Student;
use App\Entity\ContactFlock\Roles\Teacher;
use App\Entity\ContactFlock\Roles\Tutor;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Vich\UploaderBundle\Entity\File;

class RoleFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            TaxonomyFixtures::class,
        );
    }

    protected function execute()
    {

//        $this->createMany(32, function()  {
//
//            $company = $this->createCompany();
//
//            $role = $this->faker->randomElement([
//                new CompanyAggregator()
//            ]);
//
//            if (method_exists($role, 'setCode')) {
//                $role->setCode(strtoupper($this->faker->randomLetter() . $this->faker->randomLetter()) . $this->faker->randomNumber(4));
//            }
//
//            $role->setContact($company);
//
//            return $role;
//        });

        /*
         * Persons
         */
//        $this->createMany(32, function() {
//
//            $person = $this->createPerson();
//
//            $role = $this->faker->randomElement([
//                new Teacher(),
//                new Student(),
//                new Tutor(),
//            ]);
//
//            if ($role instanceof Student) {
//                $role->setCustomer($this->getRandomReference('Customer'));
//            }
//
//            $role->setContact($person);
//
//            return $role;
//        });

        $this->createMainContact();
    }

    /**
     * @return Company
     */
    protected function createCompany()
    {
        $company = new Company();

        $company->setBusinessName($this->faker->company());
        $company->setVatId($this->faker->vatId());

        $info = new Email();
        $info->setValue($this->faker->companyEmail());
        $info->setType('email');
        $company->addInfo($info);
        $this->registerAndPersist($info, false);

        $info = new Phone();
        $info->setValue($this->faker->phoneNumber());
        $info->setType('cellulare');
        $company->addInfo($info);
        $this->registerAndPersist($info, false);

        $info = new Phone();
        $info->setValue($this->faker->phoneNumber());
        $info->setType('fisso');
        $company->addInfo($info);
        $this->registerAndPersist($info, false);

        $info = new Phone();
        $info->setValue($this->faker->phoneNumber());
        $info->setType('fax');
        $company->addInfo($info);
        $this->registerAndPersist($info, false);

        $office = new Office();
        $office->setFullAddress($this->faker->address());
        $office->setIsFiscalSendingAddress(true);
        $company->addOwnedPlace($office);
        $this->registerAndPersist($office);

        $owner = new SimplePerson();
        $owner->setFirstName($this->faker->firstName());
        $owner->setLastName($this->faker->lastName());
        $owner->setType(ContactKindType::PERSON);
        $this->registerAndPersist($owner);

//        if ($this->faker->randomNumber(2) > 80) {
//            $image = $this->faker->image('public/media/images', 640, 300, 'cats', false);
//            $file = new File();
//            $file->setOriginalName($image);
//            $file->setName($image);
//            $file->setMimeType('image/jpg');
//            $company->setImage($file);
//        }

        $this->addCreationInfo($company);
        $this->registerAndPersist($company);

        return $company;
    }

    /**
     * @return Person
     */
    protected function createPerson()
    {
        $person = new Person();

        /** @var Person $person */
        $gender = $this->faker->boolean() ? PersonGenderType::MALE : PersonGenderType::FEMALE;
        $professionalPosition = $this->faker->boolean() ? ProfessionalPositionType::EMPLOYEE : ProfessionalPositionType::FREELANCE;

        $person->setGender($gender);
        $person->setFirstName($this->faker->firstName($gender === PersonGenderType::MALE ? 'male' : 'female'));
        $person->setLastName($this->faker->lastName($gender === PersonGenderType::MALE ? 'male' : 'female'));
        $person->setBirthDate($this->faker->dateTimeThisCentury());
        $person->setBirthCity($this->faker->city());
        $person->setBirthProvince('MI');
        $person->setCode($this->faker->taxId());
        $person->setProfessionalPosition($professionalPosition);
        
        $this->addCreationInfo($person);

        $info = new Email();
        $info->setValue($this->faker->email());
        $info->setType('email');
        $person->addInfo($info);
        $this->registerAndPersist($info, false);

        $info = new Phone();
        $info->setValue($this->faker->phoneNumber());
        $info->setType('cellulare');
        $person->addInfo($info);
        $this->registerAndPersist($info, false);

        $info = new Phone();
        $info->setValue($this->faker->phoneNumber());
        $info->setType('fisso');
        $person->addInfo($info);
        $this->registerAndPersist($info, false);

        $house = new House();
        $house->setFullAddress($this->faker->address());
        $house->setIsFiscalSendingAddress(true);

        $person->addOwnedPlace($house);
        $this->registerAndPersist($house);


        $this->registerAndPersist($person);

        return $person;
    }

    protected function createMainContact()
    {
        $company = new Company();

        $company->setBusinessName('IOT System');
        $company->setVatId('12345678912');

        $info = new Email();
        $info->setValue('info@360lifeformazione.it');
        $info->setType('email');
        $company->addInfo($info);
        $this->registerAndPersist($info, false);

        $info = new Website();
        $info->setValue('https://www.360lifeformazione.it');
        $info->setType('website');
        $company->addInfo($info);
        $this->registerAndPersist($info, false);

        $info = new Phone();
        $info->setValue('+39 0543 1712704');
        $info->setType('fisso');
        $company->addInfo($info);
        $this->registerAndPersist($info, false);

        $info = new Phone();
        $info->setValue('+39 0543 1712800');
        $info->setType('fax');
        $company->addInfo($info);
        $this->registerAndPersist($info, false);

        $office = new Office();
        $office->setFullAddress('Via Brugnoli 13 – 40139 Bologna BO');
        $office->setIsFiscalSendingAddress(true);
        $company->addOwnedPlace($office);
        $this->registerAndPersist($office);

        $office = new Office();
        $office->setFullAddress('Via Balzella 41/G – 47122 Forlì FC');
        $office->setIsFiscalSendingAddress(false);
        $company->addOwnedPlace($office);
        $this->registerAndPersist($office);

        $office = new Office();
        $office->setFullAddress('Via Collamarini 14 – 40138 Bologna BO');
        $office->setIsFiscalSendingAddress(false);
        $company->addOwnedPlace($office);
        $this->registerAndPersist($office);

        $this->addCreationInfo($company);
        $this->registerAndPersist($company);


        copy('assets/images/sinervis/360_life_logo_black.png', 'public/media/images/360_life_logo_black.png');

        $file = new File();
        $file->setOriginalName('360_life_logo_black.png');
        $file->setName('360_life_logo_black.png');
        $file->setMimeType('image/png');
        $company->setImage($file);

        $owner = new SimplePerson();
        $owner->setFirstName('Claudio');
        $owner->setLastName('Panzacchi');
        $owner->setType(ContactKindType::PERSON);
        $this->registerAndPersist($owner);

        $this->addReference('MAIN_CONTACT', $company);
        return $company;
    }
}
