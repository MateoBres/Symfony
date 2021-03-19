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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sinervis\NotificationBundle\Entity\EventKind;
use Symfony\Component\Validator\Constraints as Assert;
use Sinervis\ContactBundle\Validator\Constraints as SinervisAssert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * Company
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ContactRoleMap({
 * })
 */
class Company extends Contact
{
    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    /**
     * @var string
     *
     * @ORM\Column(name="business_name", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="Ragione Sociale non dovrebbe essere vuoto.")
     */
    protected $businessName;


    # * @SinervisAssert\Vatid(message="La partita IVA non e' valida")
    /**
     * @var string
     *
     * @ORM\Column(name="vat_id", type="string", length=255, nullable=true)
     */
    protected $vatId;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_code", type="string", length=255, nullable=true)
     */
    protected $taxCode;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = 'c';
    }

    public function getFullName()
    {
        return $this->getBusinessName();
    }

    /**
     * Get businessName
     *
     * @return string
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set businessName
     *
     * @param string $businessName
     * @return Contact
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = strtoupper($businessName);

        return $this;
    }

//  public function getEmailsForEventKind(EventKind $nk)
//  {
//    $result = parent::getEmailsForEventKind($nk);
//    foreach($this->representatives as $representative)
//    {
//      $result = array_merge($result, $representative->getContact()->getContactable()->getEmailsForEventKind($nk));
//    }
//    return $result;
//  }
    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    public function getFullNameCanonical()
    {
        return $this->getBusinessName();
    }

    public function getType()
    {
        return $this->type;
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
     * Get taxCode
     *
     * @return string
     */
    public function getTaxCode()
    {
        return $this->taxCode;
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
     * Set code
     *
     * @param integer $code
     * @return Company
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

    public function __toString()
    {
        return $this->getFullNameCanonical() ? $this->getFullNameCanonical() : '';
    }

    /**************************************/
    /* END                                */
    /**************************************/
}
