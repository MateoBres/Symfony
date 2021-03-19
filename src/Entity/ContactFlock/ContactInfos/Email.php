<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 03/11/14
 * Time: 9.47
 */

namespace App\Entity\ContactFlock\ContactInfos;

use App\Entity\ContactFlock\ContactInfo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Email
 *
 * @ORM\Entity
 */
class Email extends ContactInfo
{
    /**************************************/
    /* PROPERTIES                         */
    /**************************************/


    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notificationTemplateEmails = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notificationTemplateEmailsCCN = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/


    public function getIcon()
    {
        return 'fa-envelope';
    }

    public function getProtocol()
    {
        return 'mailto';
    }

    public function getFullNameWithEmail()
    {
        return $this->getContactable()->getOwner()->getFullNameCanonical() . ' <' . $this->getValue() . '>';
    }
}
