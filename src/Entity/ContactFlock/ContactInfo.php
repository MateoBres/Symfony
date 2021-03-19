<?php

namespace App\Entity\ContactFlock;

use App\Entity\AdminFlock\TimestampableInterface;
use App\Entity\AdminFlock\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Entity\ContactFlock\ContactInfos\Email;
use App\Entity\ContactFlock\ContactInfos\Phone;

/**
 * ContactInfo
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"info" = "ContactInfo",
 *   "Email" = "App\Entity\ContactFlock\ContactInfos\Email",
 *   "Phone" = "App\Entity\ContactFlock\ContactInfos\Phone",
 *   "Website" = "App\Entity\ContactFlock\ContactInfos\Website",
 * })
 */
abstract class ContactInfo implements TimestampableInterface
{
    use TimestampableTrait;

    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    protected $value;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    public function __toString()
    {
        return $this->getValue();
    }

    abstract public function getIcon();

    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function contactInfoValidator(ExecutionContextInterface $context)
    {
        $value = $this->getValue();
        $type = $this->getType();
        $errorMsg = null;

        if (empty($value) && $this instanceof Email) {
            $errorMsg = 'Email non dovrebbe essere vuoto.';
            $errorPath = 'value';
        } elseif (!empty($value) && $this instanceof Email && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errorMsg = 'Email non è valida.';
            $errorPath = 'value';
        } elseif (empty($value) && $this instanceof Phone) {
            $errorMsg = 'Telefono non dovrebbe essere vuoto.';
            $errorPath = 'value';
        }

        if (!empty($value) && $this instanceof Phone && is_null($type)) {
            $errorMsg = 'Scegli tipo telefono';
            $errorPath = 'type';
        }

        if (!is_null($errorMsg)) {
            $context->buildViolation($errorMsg)
                ->atPath($errorPath)
                ->addViolation();
        }
    }

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    /**
     * Constructor
     */
    public function __construct()
    {
//    $this->eventKinds = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return ContactInfo
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return ContactInfo
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**************************************/
    /* END                                */
    /**************************************/
}
