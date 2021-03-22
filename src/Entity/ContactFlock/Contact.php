<?php

namespace App\Entity\ContactFlock;

use App\Entity\AdminFlock\TimestampableInterface;
use App\Entity\AdminFlock\TimestampableTrait;
use App\Entity\UserFlock\User;
use App\Entity\ContactFlock\Roles\Tutor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contact
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\ContactFlock\ContactRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"contact" = "Contact",
 *   "Company" = "App\Entity\ContactFlock\Contacts\Company",
 *   "Person" = "App\Entity\ContactFlock\Contacts\Person",
 *   "SimplePerson" = "App\Entity\ContactFlock\Contacts\SimplePerson",
 * })
 * @ORM\EntityListeners({"App\EventListener\ContactFlock\ContactListener"})
 */
abstract class Contact implements TimestampableInterface
{
    use TimestampableTrait;

    // call initWithImageTrait in constructor

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
     * @ORM\Column(name="full_name_canonical", type="string", length=255, nullable=true)
     */
    protected $fullNameCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="clean_full_name", type="string", length=255, nullable=true)
     */
    protected $cleanFullName;

    /**
     * @ORM\ManyToMany(targetEntity="ContactInfo", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\JoinTable(name="contacts_contactinfo",
     *      joinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contactinfo_id", referencedColumnName="id", unique=true, onDelete="CASCADE")}
     *      )
     * @Assert\Valid()
     */
    protected $infos;

    /**
     * @ORM\OneToMany(targetEntity="Role", mappedBy="contact", orphanRemoval=true, cascade={"persist", "remove"})
     */
    protected $roles;

    /**
     * @ORM\OneToMany(targetEntity="Place", mappedBy="contact", orphanRemoval=true, cascade={"persist"})
     *
     * @Assert\Valid()
     */
    protected $ownedPlaces;

    /**
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\ContactKindType")
     * @ORM\Column(name="type", type="ContactKindType", length=1)
     *
     * @Assert\NotBlank(message="Tipo non dovrebbe essere vuoto.")
     */
    protected $type;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserFlock\User", inversedBy="contact", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\Valid()
     */
    protected $user;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $notes;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $contactRoles = [];

    /**************************************/
    /* ABSTRACT METHODS                   */
    /**************************************/
    abstract public function getFullName();

    abstract public function getType();


    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->roleNames = [];
        $this->infos = new ArrayCollection();
        $this->ownedPlaces = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function getMainAddress()
    {
        return (count($this->ownedPlaces)) ? $this->ownedPlaces[0] : null;
    }

    public function getMainContactInfo($infoType, $subType = null)
    {
        foreach ($this->getInfos() as $info) {
            if ($infoType == 'Email' && $info->getClassName() == 'Email') {
                return $info;
            } elseif ($infoType == 'Website' && $info->getClassName() == 'Website') {
                return $info;
            } elseif ($infoType == 'Phone' && $info->getClassName() == 'Phone') {
                if (is_null($subType))
                    return $info;

                if ($info->getType() == $subType) {
                    return $info;
                }
            }
        }

        return '';
    }

    public function getFullNameWithRoleNames() // used by UserType form (field contact)
    {
        $ret = $this->getFullNameCanonical();
        foreach ($this->getRoles() as $role) {
            $ret .= ' - ' . $role->getRoleName();
        }
        return $ret;
    }

    public function hasRole($roleClass)
    {
        foreach ($this->getRoles() as $role) {
            if ($role instanceof $roleClass)
                return $role;
        }
        return false;
    }

    public function getEmails()
    {
        if ($this->getContactable()) {
            return $this->getContactable()->getEmails();
        }

        return array();
    }

    public function getPhones()
    {
        $phones = [];
        foreach ($this->getInfos() as $info) {
            if ($info->getClassName() == 'Phone') {
                $phones[] = $info;
            }
        }

        return new ArrayCollection($phones);
    }

    public function getContactInvoicePlace()
    {
        if (count($this->getOwnedPlaces())) {
            foreach ($this->getOwnedPlaces() as $ownedPlace) {
                if ($ownedPlace->getIsFiscalSendingAddress()) {
                    return $ownedPlace;
                }
            }

            return $this->getOwnedPlaces()->first();
        }

        return null;
    }

    public function getOperativePlaces()
    {
        $places = [];
        if (count($this->getOwnedPlaces())) {
            foreach ($this->getOwnedPlaces() as $ownedPlace) {
                if (!$ownedPlace->getIsFiscalSendingAddress()) {
                    $places[] = $ownedPlace;
                }
            }
        }

        return new ArrayCollection($places);
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

    /**
     * Set fullNameCanonical
     *
     * @param string $fullNameCanonical
     * @return Contact
     */
    public function setFullNameCanonical($fullNameCanonical)
    {
        $this->fullNameCanonical = $fullNameCanonical;

        return $this;
    }

    /**
     * Get fullNameCanonical
     *
     * @return string
     */
    public function getFullNameCanonical()
    {
        return $this->fullNameCanonical;
    }

    /**
     * Set cleanFullName
     *
     * @param string $cleanFullName
     *
     * @return Contact
     */
    public function setCleanFullName($cleanFullName)
    {
        $this->cleanFullName = $cleanFullName;

        return $this;
    }

    /**
     * Add info
     *
     * @param ContactInfo $contactInfo
     * @return Contact
     */
    public function addInfo(ContactInfo $contactInfo)
    {
        $this->infos[] = $contactInfo;
        return $this;
    }

    /**
     * Remove infos
     *
     * @param ContactInfo $contactInfo
     */
    public function removeInfo(ContactInfo $contactInfo)
    {
        $this->infos->removeElement($contactInfo);
    }

    /**
     * Get infos
     *
     * @return Collection
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * Get cleanFullName
     *
     * @return string
     */
    public function getCleanFullName()
    {
        return $this->cleanFullName;
    }

    /**
     * Add roles
     *
     * @param Role $role
     * @return Contact
     */
    public function addRole(Role $role)
    {
        if (!$this->roles->contains($role)) {
            $role->setContact($this);
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Remove roles
     *
     * @param Role $role
     */
    public function removeRole(Role $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Get roles
     *
     * @return Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add ownedPlaces
     *
     * @param Place $ownedPlace
     * @return Contact
     */
    public function addOwnedPlace(Place $ownedPlace)
    {
        $ownedPlace->setContact($this);
        $this->ownedPlaces[] = $ownedPlace;

        return $this;
    }

    /**
     * Remove places
     *
     * @param Place $ownedPlace
     */
    public function removeOwnedPlace(Place $ownedPlace)
    {
        $this->ownedPlaces->removeElement($ownedPlace);
    }

    /**
     * Get places
     *
     * @return Collection
     */
    public function getOwnedPlaces()
    {
        return $this->ownedPlaces;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Contact
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Add roleName
     *
     * @param  $roleName
     * @return Contact
     */
    public function addRoleName($roleName)
    {
        if (is_null($this->roleNames)) {
            $this->roleNames = [];
        }

        if (!in_array($roleName, $this->roleNames)) {
            $this->roleNames[] = $roleName;
        }

        return $this;
    }

    /**
     * Remove roleName
     *
     * @param $roleName
     */
    public function removeRoleName($roleName)
    {
        if (($key = array_search($roleName, $this->roleNames)) !== false) {
//            foreach ($this->roles as $role) {
//                $reflector = new \ReflectionClass($role);
//                if ($reflector->getShortName() == $roleName) {
//                    $this->removeRole($role);
//                }
//            }
            unset($this->roleNames[$key]);
        }
    }

    /**
     * Get representatives
     *
     * @return array
     */
    public function getRoleNames()
    {
        return $this->roleNames;
    }

    /**
     * Set createUser
     *
     * @param integer $createUser
     * @return Contact
     */
    public function setCreateUser($createUser)
    {
        $this->createUser = $createUser;
        return $this;
    }

    /**
     * Get createUser
     *
     * @return integer
     */
    public function getCreateUser()
    {
        return $this->createUser;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Contact
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     */
    public function getUser()
    {
        return $this->user;
    }


    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getContactRoles(): ?array
    {
        return $this->contactRoles;
    }

    public function setContactRoles(?array $contactRoles): self
    {
        $this->contactRoles = $contactRoles;

        return $this;
    }



}
