<?php

namespace App\Entity\DateTimeSet;

use App\DBAL\Types\DateRepeatModeType;

/**
 * DateRepeater
 *
 * ORM\Table()
 * ORM\Entity
 */
class DateRepeater
{
    /**************************************/
    /* PROPERTIES                         */
    /**************************************/
    /**
     * @var integer
     *
     * ORM\Column(name="id", type="integer")
     * ORM\Id
     * ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var DateRepeatModeType
     *
     * DoctrineAssert\Enum(entity="App\DBAL\Types\DateRepeatModeType")
     * ORM\Column(name="repeat_mode", type="DateRepeatModeType", nullable=false)
     */
    private $repeatMode;

    /**
     * @var integer
     *
     * ORM\Column(name="repeat_interval", type="integer", nullable=false)
     * Assert\NotBlank()
     */
    private $repeatInterval;

    /**
     * @var smallint
     *
     * ORM\Column(name="repeat_by", type="smallint", nullable=true)
     */
    private $repeatBy;

    /**
     * @var array
     *
     * ORM\Column(name="repeat_on", type="json_array", nullable=true)
     */
    private $repeatOn;

    /**
     * @var integer
     *
     * ORM\Column(name="num_occurrences", type="integer", nullable=true)
     */
    private $numOccurrences;

    /**
     * Non mapped field.
     */
    private $onlyFinalDate;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

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
     * Set repeatMode
     *
     * @param DateRepeatModeType $repeatMode
     *
     * @return DateRepeater
     */
    public function setRepeatMode($repeatMode)
    {
        $this->repeatMode = $repeatMode;

        return $this;
    }

    /**
     * Get repeatMode
     *
     * @return DateRepeatModeType
     */
    public function getRepeatMode()
    {
        return $this->repeatMode;
    }

    /**
     * Set repeatInterval
     *
     * @param integer $repeatInterval
     *
     * @return DateRepeater
     */
    public function setRepeatInterval($repeatInterval)
    {
        $this->repeatInterval = $repeatInterval;

        return $this;
    }

    /**
     * Get repeatInterval
     *
     * @return integer
     */
    public function getRepeatInterval()
    {
        return $this->repeatInterval;
    }

    /**
     * Set repeatBy
     *
     * @param boolean $repeatBy
     *
     * @return DateRepeater
     */
    public function setRepeatBy($repeatBy)
    {
        $this->repeatBy = $repeatBy;

        return $this;
    }

    /**
     * Get repeatBy
     *
     * @return boolean
     */
    public function getRepeatBy(): bool
    {
        return $this->repeatBy;
    }

    /**
     * Set repeatOn
     *
     * @param array $repeatOn
     *
     * @return DateRepeater
     */
    public function setRepeatOn($repeatOn)
    {
        $this->repeatOn = $repeatOn;

        return $this;
    }

    /**
     * Get repeatOn
     *
     * @return array
     */
    public function getRepeatOn()
    {
        return $this->repeatOn;
    }

    /**
     * Set numOccurrences
     *
     * @param integer $numOccurrences
     *
     * @return DateRepeater
     */
    public function setNumOccurrences($numOccurrences)
    {
        $this->numOccurrences = $numOccurrences;

        return $this;
    }

    /**
     * Get numOccurrences
     *
     * @return integer
     */
    public function getNumOccurrences()
    {
        return $this->numOccurrences;
    }

    /**
     * Set onlyFinalDate
     *
     * @param boolean $onlyFinalDate
     *
     * @return DateRepeater
     */
    public function setOnlyFinalDate($onlyFinalDate)
    {
        $this->onlyFinalDate = $onlyFinalDate;

        return $this;
    }

    /**
     * Get onlyFinalDate
     *
     * @return boolean
     */
    public function getOnlyFinalDate()
    {
        return $this->onlyFinalDate;
    }

    /**************************************/
    /* END                                */
    /**************************************/

}
