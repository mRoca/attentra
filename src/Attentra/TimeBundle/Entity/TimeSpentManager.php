<?php

namespace Attentra\TimeBundle\Entity;

use Attentra\CoreBundle\Collections\ArrayCollection;

class TimeSpentManager
{

    /** @var \DateTime */
    protected $start;

    /** @var \DateTime */
    protected $end;

    /** @var ArrayCollection|TimePeriod[] */
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
     * @return \DatePeriod
     */
    public function getDatePeriod($ajustPeriod = 'day')
    {
        $intervals = array(
            'day'   => 'P1D',
            'week'  => 'P1W',
            'month' => 'P1M',
            'year'  => 'P1Y',
        );

        return new \DatePeriod($this->start, new \DateInterval(isset($intervals[$ajustPeriod]) ? $intervals[$ajustPeriod] : 'P1D'), $this->end);
    }

    public function hasFullPeriod($ajustPeriod)
    {
        $period = $this->getDatePeriod();
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
        //TODO Manage less than days
        if ($ajustPeriod === 'year') {
            return $this->start <= new \DateTime('first day of ' . $concernedDate->format('Y')) && $this->end >= new \DateTime('last day of ' . $concernedDate->format('Y'));
        } else if ($ajustPeriod === 'month') {
            return $this->start <= new \DateTime('first day of ' . $concernedDate->format('F Y')) && $this->end >= new \DateTime('last day of ' . $concernedDate->format('F Y'));
        } else if ($ajustPeriod === 'week') {
            $firstDay = clone $concernedDate;
            $firstDay->modify('first day of this week');
            $lastDay = clone $concernedDate;
            $lastDay->modify('last day of this week');
            return $this->start <= $firstDay && $this->end >= $lastDay;
        } else if ($ajustPeriod === 'day') {
            return $this->start <= new \DateTime($concernedDate->format('Y-m-d')) && $this->end >= new \DateTime($concernedDate->format('Y-m-d'));
        }

        return false;
    }

    /**
     * @return ArrayCollection|TimePeriod[]
     */
    public function getTimePeriods()
    {
        return $this->timePeriods;
    }


    /**
     * @param TimePeriod[]|ArrayCollection $timePeriods
     */
    public function addTimePeriods($timePeriods)
    {
        $this->timePeriods->merge($timePeriods);
    }

    /**
     * @param TimePeriod $timePeriod
     */
    public function addTimePeriod(TimePeriod $timePeriod)
    {
        $this->timePeriods->add($timePeriod);
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
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
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }
}
