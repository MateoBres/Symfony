<?php

namespace App\Entity\ContactFlock\Contacts;

use App\Entity\ContactFlock\Contact;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Sinervis\ContactBundle\Validator\Constraints as SinervisAssert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * SimplePerson
 *
 * @ORM\Entity
 */
class SimplePerson extends Contact
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

    public function getFullName()
    {
        return $this->getLastName() . ' ' . $this->getFirstName();
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

    /**************************************/
    /* END                                */
    /**************************************/

    /**
     * Set code
     *
     * @param integer $code
     * @return SimplePerson
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
