<?php

namespace Attentra\TimeBundle\Entity;

interface TimePeriodInterface
{

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier);

    /**
     * @return boolean
     */
    public function getHasError();

    /**
     * @param boolean $hasError
     */
    public function setHasError($hasError);

    /**
     * @return \DateTime
     */
    public function getStart();

    /**
     * @param \DateTime $start
     */
    public function setStart(\DateTime $start);

    /**
     * @return \DateTime
     */
    public function getEnd();

    /**
     * @param \DateTime $end
     */
    public function setEnd(\DateTime $end);

    /**
     * @return \DateTime
     */
    public function getConcernedDay();

    /**
     * @param \DateTime $concernedDay
     */
    public function setConcernedDay(\DateTime $concernedDay);

}
