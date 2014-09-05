<?php

namespace Attentra\TimeBundle\Parser;

use Attentra\TimeBundle\Entity\TimeInput;

class TimePeriodsParser
{
    /**
     * Permit to group times inputs by days.
     * @var string
     */
    protected $workDayStartHour = '03:00:00';

    /**
     * @param TimeInput[] $timeInputs
     */
    public function parse($timeInputs)
    {
        usort($timeInputs, array(__CLASS__, 'sortTimeInputs'));

        $periods = array();

        foreach ($timeInputs as $timeInput) {

        }
    }

    public static function sortTimeInputs(TimeInput $a, TimeInput $b)
    {
        if ($a->getDatetime()->getTimestamp() == $b->getDatetime()->getTimestamp()) {
            return 0;
        }
        return ($a->getDatetime()->getTimestamp() < $b->getDatetime()->getTimestamp()) ? -1 : 1;
    }
} 
