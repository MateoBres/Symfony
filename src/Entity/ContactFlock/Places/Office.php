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
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * Office
 *
 * @ORM\Entity
 */
class Office extends Place {
  /**************************************/
  /* PROPERTIES                         */
  /**************************************/
  /**
   * @var string
   *
   * @DoctrineAssert\Enum(entity="App\DBAL\Types\OfficeTypeType")
   * @ORM\Column(name="office_type", type="OfficeTypeType", length=255, nullable=true)
   */
  private $type;

  /**************************************/
  /* CUSTOM CODE                        */
  /**************************************/


  public function getIcon()
  {
    return 'build/images/sinervis/ContactFlock/office.png';
  }

  /**************************************/
  /* GETTERS & SETTERS                  */
  /**************************************/
  /**
   * Set type
   *
   * @param string $type
   * @return Office
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

  /**************************************/
  /* END                                */
  /**************************************/
    /**
     * @var \Sinervis\ContactBundle\Entity\Contactable
     */
    private $contactable;


}
