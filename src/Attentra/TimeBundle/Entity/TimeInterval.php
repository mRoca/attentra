<?php

namespace Attentra\TimeBundle\Entity;

class TimeInterval extends \DateInterval
{
    /**
     * @param \DateInterval $from
     * @return TimeInterval
     */
    public static function fromDateInterval(\DateInterval $from)
    {
        return new TimeInterval($from->format('P%yY%dDT%hH%iM%sS'));
    }

    /**
     * @param \DateInterval $interval
     */
    public function add(\DateInterval $interval)
    {
        foreach (str_split('ymdhis') as $prop) {
            $this->$prop += $interval->$prop;
        }
        $this->i += (int)($this->s / 60);
        $this->s = $this->s % 60;
        $this->h += (int)($this->i / 60);
        $this->i = $this->i % 60;
    }

    /**
     * @return int
     */
    public function toSeconds()
    {
        return (new \DateTime('@0'))->add($this)->getTimestamp();
    }
} 
