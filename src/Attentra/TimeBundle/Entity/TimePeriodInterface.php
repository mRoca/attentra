<?php

namespace Attentra\TimeBundle\Entity;

interface TimePeriodInterface
{
    /**
     * @param boolean $hasError
     */
    public function setHasError($hasError);

    /**
     * @return boolean
     */
    public function getHasError();

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier);

    /**
     * @param \DateTime $start
     */
    public function setStart(\DateTime $start);

    /**
     * @param \DateTime $end
     */
    public function setEnd(\DateTime $end);

    /**
     * @return \DateTime
     */
    public function getEnd();

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return \DateTime
     */
    public function getStart();
}
