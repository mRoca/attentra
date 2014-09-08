<?php

namespace Attentra\TimeBundle\Entity;

interface TimeInputInterface
{

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     * @return TimeInput
     */
    public function setDatetime(\DateTime $datetime);

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime();

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return TimeInput
     */
    public function setIdentifier($identifier);

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier();

}
