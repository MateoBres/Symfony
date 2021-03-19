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
class Website extends ContactInfo
{
    /**************************************/
    /* PROPERTIES                         */
    /**************************************/


    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/


    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    public function getIcon()
    {
        return 'fas fa-link';
    }

    public function getProtocol()
    {
        return 'http';
    }

}
