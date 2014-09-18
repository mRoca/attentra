<?php

namespace Attentra\TimeBundle\Parser;

use Attentra\TimeBundle\Entity\TimeInputInterface;
use Attentra\TimeBundle\Entity\TimePeriodInterface;

interface TimePeriodsParserInterface
{

    /**
     * @param TimeInputInterface[] $timeInputs
     * @return TimePeriodInterface[]
     */
    public function timeInputsToEvents(array $timeInputs);

    /**
     * @param TimeInputInterface[] $timeInputs
     * @return \DateInterval[]
     */
    public function timeInputsToSpentTimeByDay(array $timeInputs);

    /**
     * @param \DateTime $start
     * @param string $adjustPeriod day|week|month|year
     * @return \DateTime
     */
    public function ajustStartDate(\DateTime $start, $adjustPeriod = 'day');

    /**
     * @param \DateTime $end
     * @param string $ajustPeriod day|week|month|year
     * @return \DateTime
     */
    public function ajustEndDate(\DateTime $end, $ajustPeriod = 'day');

    /**
     * @param \DateTime $datetime
     * @param bool $returnString
     * @return \DateTime|string
     */
    public function getDateDay(\DateTime $datetime, $returnString = true);
}
