<?php

namespace App\DataFixtures;

use App\DBAL\Types\PersonGenderType;
use App\Entity\ContactFlock\ContactInfos\Email;
use App\Entity\ContactFlock\Contacts\Company;
use App\Entity\ContactFlock\Contacts\Person;
use App\Entity\ContactFlock\Number\Number;
use App\Entity\ContactFlock\Number\NumberType;
use App\Entity\ContactFlock\Places\House;
use App\Entity\ContactFlock\Places\Office;
use App\Entity\ContactFlock\Roles\CompanyAggregator;
use App\Entity\ContactFlock\Roles\Customer;
use App\Entity\ContactFlock\Roles\EducationProvider;
use App\Entity\ContactFlock\Roles\FundingAgency;
use App\Entity\ContactFlock\Roles\RegionalPartner;
use App\Entity\ContactFlock\Roles\Student;
use App\Entity\ContactFlock\Roles\Teacher;
use App\Entity\ContactFlock\Roles\Tutor;
use App\Entity\CourseFlock\CourseEdition;
use App\Entity\TaxonomyFlock\Vocabulary\CourseCategory;
use App\Entity\TaxonomyFlock\Vocabulary\CourseEcmNationalTarget;
use App\Entity\UserFlock\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\common\Persistence\ObjectManager;

class TaxonomyFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }

    protected function execute()
    {
//        $this->createTaxonomies(CourseCategory::class,[
//            '01' => 'Informatica',
//            '02' => 'Medicina',
//            '03' => 'Lingue',
//        ]);
//
//        $this->createTaxonomies(CourseEcmNationalTarget::class,[
//            '01' => 'Applicazione nella pratica quotidiana dei principi e delle procedure dell\'evidence based practice (EBM - EBN - EBP) (1)',
//            '02' => 'Linee guida - Protocolli - Procedure (2)',
//            '03' => 'Documentazione clinica. Percorsi clinico-assistenziali diagnostici e riabilitativi, profili di assistenza - profili di cura (3)',
//            '04' => 'Appropriatezza prestazioni sanitarie nei LEA. Sistemi di valutazione, verifica e miglioramento dell\'efficienza ed efficacia (4)',
//        ]);
    }

    private function createTaxonomy(string $taxonomyClass, string $term, string $code)
    {
        $taxonomy = new $taxonomyClass();
        $taxonomy->setTerm($term);
        $taxonomy->setCode($code);
        $this->registerAndPersist($taxonomy);
    }

    private function createTaxonomies(string $taxonomyClass, array $list)
    {
        foreach($list as $code => $term) {
            $taxonomy = new $taxonomyClass();
            $taxonomy->setTerm($term);
            $taxonomy->setCode($code);
            $this->registerAndPersist($taxonomy);
        }
    }
}
