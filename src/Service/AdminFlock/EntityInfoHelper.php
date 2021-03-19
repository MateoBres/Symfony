<?php

namespace App\Service\AdminFlock;

use App\Entity\CommercialFlock\Certificates\AutomatedCertificate;
use App\Entity\CommercialFlock\Certificates\ManualCertificate;
use App\Entity\CommercialFlock\Contracts\Assignment;
use App\Entity\CommercialFlock\Contracts\Enrollment;
use App\Entity\CommercialFlock\Contracts\ProxyAssignment;
use App\Entity\ContactFlock\Contacts\Company;
use App\Entity\ContactFlock\Contacts\Person;
use App\Entity\ContactFlock\Place;
use App\Entity\ContactFlock\Roles\CompanyAggregator;
use App\Entity\ContactFlock\Roles\Customer;
use App\Entity\ContactFlock\Roles\EducationProvider;
use App\Entity\ContactFlock\Roles\FundingAgency;
use App\Entity\ContactFlock\Roles\RegionalPartner;
use App\Entity\ContactFlock\Roles\Student;
use App\Entity\ContactFlock\Roles\Teacher;
use App\Entity\ContactFlock\Roles\Tutor;
use App\Entity\CourseFlock\Course;
use App\Entity\CourseFlock\CourseEdition;
use App\Entity\CourseFlock\CourseVenue;
use App\Entity\CourseFlock\Ecm\EcmEvent;
use App\Entity\CourseFlock\Lesson;
use App\Entity\CourseFlock\TrainingPlans\FundingPlan;
use App\Entity\CourseFlock\TrainingPlans\MarketPlan;
use App\Entity\CourseFlock\TrainingPlans\ResolutionPlan;
use App\Entity\CourseFlock\TrainingPlans\TrainingPlan;
use App\Entity\QuestionnaireFlock\Answer;
use App\Entity\QuestionnaireFlock\CompletedQuestionnaire\LearningTestCompletedQuestionnaire;
use App\Entity\QuestionnaireFlock\CompletedQuestionnaire\TeacherAssessmentCompletedQuestionnaire;
use App\Entity\QuestionnaireFlock\Question;
use App\Entity\QuestionnaireFlock\Questionnaire;
use App\Entity\UserFlock\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EntityInfoHelper
{
    /**
     * @var Container
     */
    protected $container;
    protected $em;
    public $icons;

    public function __construct(ContainerInterface $container, EntityManagerInterface $em)
    {
        $this->container = $container;
        $this->em = $em;
        $this->icons = (object)$this->container->getParameter('icons');
    }

    public function getEntityIcon($object)
    {
        switch (true) {
            case $object instanceof CourseEdition:
                return '<i class="' . $this->icons->edition . '"></i>';
            case $object instanceof Course:
                return '<i class="' . $this->icons->format . '"></i>';
            case $object instanceof FundingPlan:
                return '<i class="' . $this->icons->funding_plan . '"></i>';
            case $object instanceof MarketPlan:
                return '<i class="' . $this->icons->market_plan . '"></i>';
            case $object instanceof ResolutionPlan:
                return '<i class="' . $this->icons->resolution_plan . '"></i>';
            case $object instanceof TrainingPlan:
                return '<i class="' . $this->icons->training_plan . '"></i>';
            case $object instanceof Customer:
                return '<i class="' . $this->icons->customer . '"></i>';
            case $object instanceof Teacher:
                return '<i class="' . $this->icons->teacher . '"></i>';
            case $object instanceof Assignment:
                return '<i class="' . $this->icons->assignment . '"></i>';
            case $object instanceof Student:
                return '<i class="' . $this->icons->student . '"></i>';
            case $object instanceof Enrollment:
                return '<i class="' . $this->icons->enrollment . '"></i>';
            case $object instanceof Tutor:
                return '<i class="' . $this->icons->tutor . '"></i>';
            case $object instanceof FundingAgency:
                return '<i class="' . $this->icons->fundingagency . '"></i>';
            case $object instanceof EducationProvider:
                return '<i class="' . $this->icons->educationprovider . '"></i>';
            case $object instanceof RegionalPartner:
                return '<i class="' . $this->icons->regionalpartner . '"></i>';
            case $object instanceof CompanyAggregator:
                return '<i class="' . $this->icons->companyaggregator . '"></i>';
            case $object instanceof Questionnaire:
                return '<i class="' . $this->icons->questionnaire . '"></i>';
            case $object instanceof Question:
                return '<i class="' . $this->icons->question . '"></i>';
            case $object instanceof Answer:
                return '<i class="' . $this->icons->answer . '"></i>';
            case $object instanceof TeacherAssessmentCompletedQuestionnaire:
                return '<i class="' . $this->icons->form . '"></i>';
            case $object instanceof CourseAssessmentCompletedQuestionnaire:
            case $object instanceof LearningTestCompletedQuestionnaire:
                return '<i class="' . $this->icons->compiled . '"></i>';
            case $object instanceof ProxyAssignment:
                return '<i class="' . $this->icons->proxyassignment . '"></i>';
            case $object instanceof Certificate:
            case $object instanceof ManualCertificate:
            case $object instanceof AutomatedCertificate:
                return '<i class="' . $this->icons->certificate . '"></i>';
            case $object instanceof Lesson:
                return '<i class="' . $this->icons->lesson . '"></i>';
            case $object instanceof Place:
            case $object instanceof CourseVenue:
                return '<i class="' . $this->icons->location . '"></i>';
            case $object instanceof EcmEvent:
                return '<i class="' . $this->icons->ecm . '"></i>';
            case $object instanceof Company:
                return '<i class="' . $this->icons->company . '"></i>';
            case $object instanceof Person:
                return '<i class="' . $this->icons->person . '"></i>';
            case $object instanceof User:
                return '<i class="' . $this->icons->user . '"></i>';
            default:
                return '';
        }
    }

    public function getEntityDescription($object)
    {
        switch (true) {
            case $object instanceof CourseEdition:
                return 'Edizione';
            case $object instanceof Course:
                return 'Format corsuale';
            case $object instanceof FundingPlan:
                return 'Piano finanziato';
            case $object instanceof MarketPlan:
                return 'Piano a mercato';
            case $object instanceof ResolutionPlan:
                return 'Piano di delibera';
            case $object instanceof TrainingPlan:
                return 'Piano';
            case $object instanceof Customer:
                return 'Cliente';
            case $object instanceof Teacher:
                return 'Docente';
            case $object instanceof Assignment:
                return 'Incarico';
            case $object instanceof Student:
                return 'Allievo';
            case $object instanceof Enrollment:
                return 'Iscrizione';
            case $object instanceof Tutor:
                return 'Tutor';
            case $object instanceof FundingAgency:
                return 'Ente finanziatore';
            case $object instanceof EducationProvider:
                return 'Fornitore didattico';
            case $object instanceof RegionalPartner:
                return 'Partner territoriale';
            case $object instanceof CompanyAggregator:
                return 'Aggregatore di imprese';
            case $object instanceof Questionnaire:
                return 'Modello di questionario';
            case $object instanceof Question:
                return 'Domanda';
            case $object instanceof Answer:
                return 'Risposta';
            case $object instanceof TeacherAssessmentCompletedQuestionnaire:
                return 'Scheda di valutazione docenteeeee';
            case $object instanceof CourseAssessmentCompletedQuestionnaire:
                return 'Scheda di valutazione corso';
            case $object instanceof LearningTestCompletedQuestionnaire:
                return 'Verifica di apprendimento';
            case $object instanceof ProxyAssignment:
                return 'Incarico delega';
            case $object instanceof Certificate:
                return 'Attestato';
            case $object instanceof ManualCertificate:
                return 'Attestato manuale';
            case $object instanceof AutomatedCertificate:
                return 'Attestato automatico';
            case $object instanceof Lesson:
                return 'Lezione';
            case $object instanceof Place:
                return 'Luogo';
            case $object instanceof CourseVenue:
                return 'Location corso';
            case $object instanceof Company:
                return 'Azienda';
            case $object instanceof Person:
                return 'Persona';
            case $object instanceof User:
                return 'Utente';
            default:
                return '';
        }
    }

    public function __get($iconName)
    {
        if (isset($this->icons->$iconName)) {
            return $this->icons->$iconName;
        }
        return '';
    }
}