<?php

namespace App\Entity\ContactFlock;

use App\Entity\AdminFlock\TimestampableInterface;
use App\Entity\AdminFlock\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use ReflectionClass;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Role
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({
 *     "role" = "Role",
 * })
 */
class Role implements TimestampableInterface
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
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="roles", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     *
     * @Assert\Valid()
     */
    protected $contact;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    /**
     * Constructor
     */
    public function __construct()
    {
//    $this->eventLogs = new ArrayCollection();
    }

    public function __toString()
    {
        if ($this->getContact()) {
            return $this->getContact()->getFullNameCanonical();
        }

        return 'Manca il contatto';
    }

    public function getRoleName()
    {
        $class = (new ReflectionClass($this))->getShortName();
        switch ($class) {
            case 'Worker':
                return 'Operatore';
            default:
                return $class;
        }
    }

    public function getFullNameWithRoleName()
    {
        return $this->getContact()->getFullNameCanonical() . ' - ' . $this->getRoleName();
    }

    public function getAnchor($url)
    {
        return '<a href="' . $url . '" target="_blank">' . $this->__toString() . '</a>';
    }

    public function getInfoWindowContent($url)
    {
        $content = '<b>' . $this->getAnchor($url) . '</b><br/>';

        $emails = $this->contact->getEmails();
        $content .= '<b>Emails: </b>' . implode(', ', $emails) . '<br/>';

        $phones = $this->contact->getPhones();
        $content .= '<b>Tel: </b>' . implode(', ', $phones) . '<br/>';

        return $content;
    }

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set contact
     *
     * @param Contact $contact
     * @return Role
     */
    public function setContact(Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }


}
