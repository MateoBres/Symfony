<?php

namespace App\Entity\DateTimeSet;

use App\DBAL\Types\DaysOfWeekType;
use App\Entity\DateTimeSet\DateRepeater;
use Date;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Time;

/**
 * DatePeriod
 *
 */
class DatePeriod
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
    protected $id;

    /**
     * @var Date
     *
     * ORM\Column(name="start_date", type="date")
     * Assert\NotBlank()
     */
    protected $startDate;

    /**
     * @var Date
     *
     * ORM\Column(name="end_date", type="date")
     * Assert\NotBlank()
     */
    protected $endDate;

    /**
     * @var DateRepeater
     *
     * ORM\OneToOne(targetEntity="App\Entity\DateTimeSet\DateRepeater", cascade={"persist"}, orphanRemoval=true)
     * ORM\JoinColumn(name="periodRepeater_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $periodRepeater;

    /**
     * @var boolean
     *
     * ORM\Column(name="period_repeat", type="boolean", options={"default"=0})
     */
    protected $isPeriodRepeat;

    /**
     * @var Time
     *
     * @ORM\Column(name="start_time", type="time")
     *
     * @Assert\NotBlank()
     */
    protected $startTime;

    /**
     * @var Time
     *
     * @ORM\Column(name="end_time", type="time")
     *
     * @Assert\NotBlank()
     */
    protected $endTime;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    public function __toString()
    {
        return 'date period to string (DatePeriod.php)';
    }

    public function __construct()
    {
        $this->isPeriodRepeat = false;
    }

    public function getEffectiveWorkingDays($ordered = false)
    {
        $days = array();

        if (count($this->timePeriods)) {
            foreach ($this->timePeriods as $key => $timePeriod) {
                $repeatOn = $timePeriod->getRepeatOn();
                if (empty($repeatOn)) {
                    return array_keys(DaysOfWeekType::getChoices());
                } else {
                    foreach ($timePeriod->getRepeatOn() as $day) {
                        $days[$day] = $day;
                    }
                }
            }
        } else {
            return array_keys(DaysOfWeekType::getChoices());
        }

        if ($ordered) {
            $workingDays = array_keys(DaysOfWeekType::getChoices());
            foreach ($workingDays as $workingDay) {
                if (array_search($workingDay, $days) === false) {
                    unset($workingDays[array_search($workingDay, $workingDays)]);
                }
            }
            return $workingDays;
        }

        return $days;
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
     * Set startDate
     *
     * @param DateTime $startDate
     *
     * @return DatePeriod
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return Date
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param DateTime $endDate
     *
     * @return DatePeriod
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return Date
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set isPeriodRepeat
     *
     * @param boolean $isPeriodRepeat
     *
     * @return DatePeriod
     */
    public function setIsPeriodRepeat($isPeriodRepeat)
    {
        $this->isPeriodRepeat = $isPeriodRepeat;

        return $this;
    }

    /**
     * Get isPeriodRepeat
     *
     * @return boolean
     */
    public function getIsPeriodRepeat()
    {
        return $this->isPeriodRepeat;
    }

    /**
     * Set periodRepeater
     *
     * @param DateRepeater $periodRepeater
     *
     * @return DatePeriod
     */
    public function setPeriodRepeater(DateRepeater $periodRepeater = null)
    {
        $this->periodRepeater = $periodRepeater;

        return $this;
    }

    /**
     * Get periodRepeater
     *
     * @return DateRepeater
     */
    public function getPeriodRepeater()
    {
        return $this->periodRepeater;
    }

    /**
     * Set startTime
     *
     * @param DateTime $startTime
     * @return DatePeriod
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return Time
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param DateTime $endTime
     * @return DatePeriod
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return Time
     */
    public function getEndTime()
    {
        return $this->endTime;
    }
}
