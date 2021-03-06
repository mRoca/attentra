<?php

namespace Attentra\TimeBundle\Parser;

use Attentra\CoreBundle\Collections\ArrayCollection;
use Attentra\TimeBundle\Entity\TimeInterval;
use Attentra\TimeBundle\Entity\TimePeriod;
use Attentra\TimeBundle\Entity\TimePeriodInterface;

class TimeSpentParser
{

    /** @var \DateTime */
    protected $start;

    /** @var \DateTime */
    protected $end;

    /** @var ArrayCollection|TimePeriodInterface[] */
    protected $timePeriods;

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     */
    public function __construct(\DateTime $start, \DateTime $end)
    {
        $this->timePeriods = new ArrayCollection();
        $this->start       = $start;
        $this->end         = $end;
    }

    /**
     * @param string $ajustPeriod day|week|month|year
     * @throws \ErrorException
     * @return \DatePeriod
     */
    public function getDatePeriod($ajustPeriod)
    {
        $intervals = array(
            'day'   => 'P1D',
            'week'  => 'P1W',
            'month' => 'P1M',
            'year'  => 'P1Y',
        );

        if (!isset($intervals[$ajustPeriod])) {
            throw new \ErrorException('Invalid period');
        }

        $periodStart = $this->getPeriodStartDate($ajustPeriod, $this->start);
        $periodEnd   = $this->getPeriodEndDate($ajustPeriod, $this->end);

        //Include the end date
        $periodEnd->modify('+1 day');

        return new \DatePeriod($periodStart, new \DateInterval($intervals[$ajustPeriod]), $periodEnd);
    }

    /**
     * @param $ajustPeriod
     * @return bool
     */
    public function hasFullPeriod($ajustPeriod)
    {
        $period = $this->getDatePeriod($ajustPeriod);
        foreach ($period as $date) {
            /** @var \DateTime $date */
            if ($this->isPeriodFull($ajustPeriod, $date)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $ajustPeriod
     * @param \DateTime $concernedDate
     * @return bool
     */
    public function isPeriodFull($ajustPeriod, \DateTime $concernedDate)
    {
        return $this->getPeriodStartDate('day', $this->start) <= $this->getPeriodStartDate($ajustPeriod, $concernedDate)
        && $this->getPeriodStartDate('day', $this->end) >= $this->getPeriodEndDate($ajustPeriod, $concernedDate);
    }

    /**
     * @param string $ajustPeriod
     * @param \DateTime $concernedDate
     * @return bool
     */
    public function isLastPeriod($ajustPeriod, \DateTime $concernedDate)
    {
        return $concernedDate >= $this->getPeriodStartDate($ajustPeriod, $this->end)
        && $concernedDate <= $this->getPeriodEndDate($ajustPeriod, $this->end);
    }

    /**
     * @param string $ajustPeriod
     * @param \DateTime $concernedDate
     * @return ArrayCollection|TimePeriod[]
     */
    public function getTimePeriodsByPeriod($ajustPeriod, \DateTime $concernedDate)
    {
        $start = $this->getPeriodStartDate($ajustPeriod, $concernedDate);
        $end   = $this->getPeriodEndDate($ajustPeriod, $concernedDate);

        $timePeriods = new ArrayCollection();
        foreach ($this->timePeriods as $timePeriod) {
            if ($timePeriod->getConcernedDay() >= $start && $timePeriod->getConcernedDay() <= $end) {
                $timePeriods->add($timePeriod);
            }
        }

        return $timePeriods;
    }

    /**
     * @param string $ajustPeriod
     * @param \DateTime $concernedDate
     * @return TimeInterval
     */
    public function getSpentTimeIntervalByPeriod($ajustPeriod, \DateTime $concernedDate)
    {
        $spentTime   = new TimeInterval('PT0S');
        $timePeriods = $this->getTimePeriodsByPeriod($ajustPeriod, $concernedDate);
        foreach ($timePeriods as $timePeriod) {
            if ($timePeriod->getEnd()) {
                $spentTime->add($timePeriod->getStart()->diff($timePeriod->getEnd()));
            }
        }

        return $spentTime;
    }

    /**
     * @param $ajustPeriod
     * @param \DateTime $concernedDate
     * @return \DateTime|null
     */
    public function getPeriodStartDate($ajustPeriod, \DateTime $concernedDate)
    {
        $date = clone $concernedDate;
        $date->setTime(0, 0, 0);

        if ($ajustPeriod === 'year') {
            return $date->modify('first day of January ' . $date->format('Y'));
        } else if ($ajustPeriod === 'month') {
            return $date->modify('first day of this month');
        } else if ($ajustPeriod === 'week') {
            return $date->modify(($date->format('w') === '0') ? 'monday last week' : 'monday this week');
        } else if ($ajustPeriod === 'day') {
            return $date;
        }

        return null;
    }

    /**
     * @param $ajustPeriod
     * @param \DateTime $concernedDate
     * @return \DateTime|null
     */
    public function getPeriodEndDate($ajustPeriod, \DateTime $concernedDate)
    {
        $date = clone $concernedDate;
        $date->setTime(0, 0, 0);

        if ($ajustPeriod === 'year') {
            return $date->modify('last day of December ' . $date->format('Y'));
        } else if ($ajustPeriod === 'month') {
            return $date->modify('last day of this month');
        } else if ($ajustPeriod === 'week') {
            return $date->modify(($date->format('w') === '0') ? 'today' : 'sunday this week');
        } else if ($ajustPeriod === 'day') {
            return $date;
        }

        return null;
    }

    /**
     * @param TimePeriodInterface[]|ArrayCollection $timePeriods
     */
    public function addTimePeriods($timePeriods)
    {
        $this->timePeriods->merge($timePeriods);
    }

    /**
     * @param TimePeriodInterface $timePeriod
     */
    public function addTimePeriod(TimePeriodInterface $timePeriod)
    {
        $this->timePeriods->add($timePeriod);
    }

    /*
     * ##########################################
     * ####### Generated getters & setters ######
     * ##########################################
     */

    /**
     * @return ArrayCollection|TimePeriodInterface[]
     */
    public function getTimePeriods()
    {
        return $this->timePeriods;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }
}
