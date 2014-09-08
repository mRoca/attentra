<?php

namespace Attentra\TimeBundle\Parser;

use Attentra\TimeBundle\Entity\TimeInputInterface;
use Attentra\TimeBundle\Entity\TimePeriodInterface;

class TimePeriodsParser implements TimePeriodsParserInterface
{
    /**
     * Permit to group times inputs by days.
     * @var string
     */
    protected $workDayStartHour = '00:00:00';

    /**
     * @var string
     */
    protected $timePeriodClass;

    public function __construct($timePeriodClass, $workDayStartHour)
    {
        //Time format validation
        if (!is_object(\DateTime::createFromFormat('h:i:s', $workDayStartHour))) {
            throw new \ErrorException('The hour format is invalid');
        }

        $this->timePeriodClass  = $timePeriodClass;
        $this->workDayStartHour = $workDayStartHour;
    }

    /**
     * @param \DateTime $start
     * @return \DateTime
     */
    public function ajustStartDate(\DateTime $start)
    {
        $workDaySep = $this->getWorkDayStartHour(true);
        $start->setTime($workDaySep['hour'], $workDaySep['minute'], $workDaySep['second']);
        return $start;
    }

    /**
     * @param \DateTime $end
     * @return \DateTime
     */
    public function ajustEndDate(\DateTime $end)
    {
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
        $timeInputs = self::groupTimeInputs($timeInputs);

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
        } else {
            $period->setHasError(true);
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

        $workDaySep = $this->getWorkDayStartHour(true);

        $timeInputsByIdentifier = [];
        foreach ($timeInputs as $timeInput) {
            $day = $timeInput->getDatetime()->sub(new \DateInterval(sprintf('PT%sH%sM%sS', $workDaySep['hour'], $workDaySep['minute'], $workDaySep['second'])))->format('Y-m-d');

            $timeInputsByIdentifier[$timeInput->getIdentifier()][$day][] = $timeInput;
        }

        return $timeInputsByIdentifier;
    }

    /**
     * @param TimeInputInterface $a
     * @param TimeInputInterface $b
     * @return int
     */
    public static function sortTimeInputs(TimeInputInterface $a, TimeInputInterface $b)
    {
        if ($a->getDatetime()->getTimestamp() == $b->getDatetime()->getTimestamp()) {
            return 0;
        }
        return ($a->getDatetime()->getTimestamp() < $b->getDatetime()->getTimestamp()) ? -1 : 1;
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
