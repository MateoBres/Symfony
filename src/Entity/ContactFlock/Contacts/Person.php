<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 09/12/14
 * Time: 9.46
 */

namespace App\Entity\ContactFlock\Contacts;

use App\Annotations\ContactRoleMap;
use App\Entity\ContactFlock\Contact;
use App\Entity\ContactFlock\GenericProfession;
use App\Entity\ContactFlock\PersonEcmProfession;
use App\Entity\CourseFlock\CourseEdition;
use App\Entity\CourseFlock\EcmDiscipline;
use App\Entity\CourseFlock\EcmProfession;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Person
 *
 * @ORM\Entity
 * @ContactRoleMap({
 *   "Worker" = "App\Entity\ContactFlock\Roles\Worker",
 * })
 */
class Person extends Contact
{

    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="Nome non dovrebbe essere vuoto.")
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="Cognome non dovrebbe essere vuoto.")
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_code", type="string", length=255, nullable=true)
     *
     */
    protected $taxCode;

    /**
     * @var string
     *
     * @ORM\Column(name="vat_id", type="string", length=255, nullable=true)
     */
    protected $vatId;

    /**
     * @var string
     *
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\PersonGenderType")
     * @ORM\Column(name="gender", type="PersonGenderType", length=1, nullable=true)
     */
    protected $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="birth_date", type="date", nullable=true)
     */
    protected $birthDate;

    /**
     * @var string
     *
     * @ORM\Column(name="birth_city", type="string", length=255, nullable=true)
     */
    protected $birthCity;

    /**
     * @var string
     *
     * @ORM\Column(name="birth_province", type="string", length=255, nullable=true)
     */
    protected $birthProvince;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = 'p';
    }

    /**
     * @Assert\Callback
     */
    public function afterSubmitValidation(ExecutionContextInterface $context)
    {
        $customer = $this->hasRole('Sinervis\ContactBundle\Entity\Roles\Customer');

        if (is_object($customer) && $customer->getStatus() == '1' && $this->getGender() === null) {
            // For customers with status == 'Acuisito' gender field is required.
            $context->buildViolation('Scegli sesso.')
                ->atPath('')
                ->addViolation();
        }

    }

    public function getFullName()
    {
        return $this->getLastName() . ' ' . $this->getFirstName();
    }

    public function getFullNameCanonical()
    {
        return $this->getFullName();
    }

    public function getType()
    {
        return $this->type;
    }


    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return Contact
     */
    public function setFirstName($firstName)
    {
        $this->firstName = ucwords(strtolower($firstName));

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Contact
     */
    public function setLastName($lastName)
    {
        $this->lastName = ucwords(strtolower($lastName));

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set taxCode
     *
     * @param string $taxCode
     * @return Contact
     */
    public function setTaxCode($taxCode)
    {
        $this->taxCode = $taxCode;

        return $this;
    }

    /**
     * Get taxCode
     *
     * @return string
     */
    public function getTaxCode()
    {
        return $this->taxCode;
    }

    /**
     * Set vatId
     *
     * @param string $vatId
     * @return Contact
     */
    public function setVatId($vatId)
    {
        $this->vatId = $vatId;

        return $this;
    }

    /**
     * Get vatId
     *
     * @return string
     */
    public function getVatId()
    {
        return $this->vatId;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return Contact
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return Contact
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set birthCity
     *
     * @param string $birthCity
     * @return Contact
     */
    public function setBirthCity($birthCity)
    {
        $this->birthCity = $birthCity;

        return $this;
    }

    /**
     * Get birthCity
     *
     * @return string
     */
    public function getBirthCity()
    {
        return $this->birthCity;
    }

    /**
     * Set birthProvince
     *
     * @param string $birthProvince
     * @return Contact
     */
    public function setBirthProvince($birthProvince)
    {
        $this->birthProvince = $birthProvince;

        return $this;
    }

    /**
     * Get birthProvince
     *
     * @return string
     */
    public function getBirthProvince()
    {
        return $this->birthProvince;
    }
    /**************************************/
    /* END                                */
    /**************************************/
    /**
     * Set code
     *
     * @param integer $code
     * @return Person
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

}
