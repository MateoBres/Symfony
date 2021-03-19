<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 28/12/14
 * Time: 12.53
 */

namespace App\Entity\ContactFlock\Places;

use App\Entity\ContactFlock\Place;
use Doctrine\ORM\Mapping as ORM;

/**
 * House
 *
 * @ORM\Entity
 */
class House extends Place {
  /**************************************/
  /* PROPERTIES                         */
  /**************************************/

  /**************************************/
  /* CUSTOM CODE                        */
  /**************************************/

  /**
   * Proxy method for listing different kind of Places together
   *
   * @return string
   */
  public function getType()
  {
    return 'abitazione';
  }

  public function getIcon()
  {
    return 'build/images/sinervis/ContactFlock/house.png';
  }
  /**************************************/
  /* GETTERS & SETTERS                  */
  /**************************************/


  /**************************************/
  /* END                                */
  /**************************************/
    /**
     * @var \Sinervis\ContactBundle\Entity\Contactable
     */
    private $contactable;


}
