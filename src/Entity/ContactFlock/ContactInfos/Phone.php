<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 03/11/14
 * Time: 9.49
 */

namespace App\Entity\ContactFlock\ContactInfos;

use App\Entity\ContactFlock\ContactInfo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Phone
 *
 * @ORM\Entity
 */
class Phone extends ContactInfo
{
    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    public function getIcon()
    {
        switch ($this->getType()) {
            case 'fax':
                return 'fa-fax';
                break;
            case 'cellulare':
                return 'fa-lg fa-mobile';
                break;
            default:
                return 'fa-phone';
                break;
        }

    }

    public function getProtocol()
    {
        return 'tel';
    }

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    /**************************************/
    /* END                                */
    /**************************************/

}
