<?php

namespace Attentra\TimeBundle\Parser;

use Attentra\TimeBundle\Entity\TimeInputInterface;
use Attentra\TimeBundle\Entity\TimeInterval;
use Attentra\TimeBundle\Entity\TimePeriodInterface;

class TimePeriodsParser implements TimePeriodsParserInterface
{
    /**
     * Permit to group times inputs by days.
     * @var string
     */
    protected $workDayStartHour = '00:00:00';

    /**
     * Permit to group times inputs by weeks. Numeric representation of the day of the week, 0 (for Sunday) through 6 (for Saturday)
     * @var string
     */
    protected $workWeekStartDay = '1';

    /**
     * @var string
     */
    protected $timePeriodClass;

    public function __construct($timePeriodClass, $workDayStartHour = null, $workWeekStartDay = null)
    {
        //Time format validation
        if ($workDayStartHour !== null) {
            if (!is_object(\DateTime::createFromFormat('h:i:s', $workDayStartHour))) {
                throw new \ErrorException('The hour format is invalid');
            }
            $this->workDayStartHour = $workDayStartHour;
        }

        //Day format validation
        if ($workWeekStartDay !== null) {
            if (!($workWeekStartDay >= 0 && $workWeekStartDay <= 6)) {
                throw new \ErrorException('The day format is invalid');
            }
            $this->workWeekStartDay = $workWeekStartDay;
        }

        $this->timePeriodClass = $timePeriodClass;
    }

    /**
     * @param \DateTime $start
     * @param string $adjustPeriod day|week|month|year
     * @return \DateTime
     */
    public function ajustStartDate(\DateTime $start, $adjustPeriod = 'day')
    {
        $start      = clone $start;
        $workDaySep = $this->getWorkDayStartHour(true);
        $start->setTime($workDaySep['hour'], $workDaySep['minute'], $workDaySep['second']);
        return $start;
    }

    /**
     * @param \DateTime $end
     * @param string $ajustPeriod day|week|month|year
     * @return \DateTime
     */
    public function ajustEndDate(\DateTime $end, $ajustPeriod = 'day')
    {
        $end        = clone $end;
        $workDaySep = $this->getWorkDayStartHour(true);
        $end->setTime($workDaySep['hour'], $workDaySep['minute'], $workDaySep['second']);
        return $end;
    }


    /**
     * @param TimeInputInterface[] $timeInputs
     * @return TimePeriodInterface[]
     */
    public function timeInputsToEvents(array $timeInputs)
    {
        $timeInputs = $this->groupTimeInputs($timeInputs);

        $periods = array();

        foreach ($timeInputs as $identifier => $timeInputsByDay) {
            foreach ($timeInputsByDay as $day => $inputs) {
                /** @var TimeInputInterface $startInput */
                $startInput = null;
                foreach ($inputs as $i => $input) {
                    /** @var TimeInputInterface $input */
                    if ($startInput === null) {
                        if ($i < count($inputs) - 1) {
                            $startInput = $input;
                        } else {
                            $periods[] = $this->newTimePeriod($input, null);
                        }
                    } else {
                        $periods[]  = $this->newTimePeriod($startInput, $input);
                        $startInput = null;
                    }
                }
            }
        }

        return $periods;
    }

    /**
     * @param TimeInputInterface[] $timeInputs
     * @return TimeInterval[]
     */
    public function timeInputsToSpentTimeByDay(array $timeInputs)
    {
        $timePeriods = $this->timeInputsToEvents($timeInputs);

        /** @var TimeInterval[] $spentTime */
        $spentTime = array();

        foreach ($timePeriods as $timePeriod) {
            if (!$timePeriod->getHasError()) {
                $day      = $this->getDateDay($timePeriod->getStart());
                $interval = TimeInterval::fromDateInterval($timePeriod->getStart()->diff($timePeriod->getEnd()));

                if (!isset($spentTime[$day])) {
                    $spentTime[$day] = $interval;
                } else {
                    $spentTime[$day]->add($interval);
                }
            }
        }

        return $spentTime;
    }


    /**
     * @param TimeInputInterface $start
     * @param TimeInputInterface $end
     * @return TimePeriodInterface
     */
    protected function newTimePeriod(TimeInputInterface $start, $end = null)
    {
        /** @var TimePeriodInterface $period */
        $period = new $this->timePeriodClass();

        $period->setIdentifier($start->getIdentifier());
        $period->setStart($start->getDatetime());

        if ($end instanceof TimeInputInterface) {
            $period->setEnd($end->getDatetime());
            $period->setHasError(false);

            if (method_exists($period, 'setId')) {
                $period->setId($start->getId() . '-' . $end->getId());
            }

        } else {
            $period->setHasError(true);

            if (method_exists($period, 'setId')) {
                $period->setId($start->getId());
            }
        }

        return $period;
    }

    /**
     * Group time inputs by identifier and by day.
     *
     * @param TimeInputInterface[] $timeInputs
     * @return array
     */
    public function groupTimeInputs($timeInputs)
    {
        usort($timeInputs, array(__CLASS__, 'sortTimeInputs'));

        $timeInputsByIdentifier = [];
        foreach ($timeInputs as $timeInput) {
            $timeInputsByIdentifier[$timeInput->getIdentifier()][$this->getDateDay($timeInput->getDatetime())][] = $timeInput;
        }

        return $timeInputsByIdentifier;
    }

    /**
     * @param \DateTime $datetime
     * @param bool $returnString
     * @return \DateTime|string
     */
    public function getDateDay(\DateTime $datetime, $returnString = true)
    {
        $datetime = clone $datetime;

        $workDaySep = $this->getWorkDayStartHour(true);
        $datetime->sub(new \DateInterval(sprintf('PT%sH%sM%sS', $workDaySep['hour'], $workDaySep['minute'], $workDaySep['second'])));

        return $returnString ? $datetime->format('Y-m-d') : $datetime;
    }

    /**
     * @param TimeInputInterface $a
     * @param TimeInputInterface $b
     * @return int
     */
    public static function sortTimeInputs(TimeInputInterface $a, TimeInputInterface $b)
    {
        if ($a->getDatetime() == $b->getDatetime()) {
            return 0;
        }
        return ($a->getDatetime() < $b->getDatetime()) ? -1 : 1;
    }

    /**
     * @param string $workDayStartHour
     */
    public function setWorkDayStartHour($workDayStartHour)
    {
        $this->workDayStartHour = $workDayStartHour;
    }

    /**
     * @param bool $returnArray
     * @return string
     */
    public function getWorkDayStartHour($returnArray = false)
    {
        return $returnArray ? date_parse($this->workDayStartHour) : $this->workDayStartHour;
    }


} 
