<?php

namespace Attentra\TimeBundle\Entity;

class TimePeriod implements TimePeriodInterface
{
    protected $id;

    /** @var \DateTime */
    protected $start;

    /** @var \DateTime */
    protected $end;

    /** @var \DateTime */
    protected $concernedDay;

    /** @var string */
    protected $identifier;

    /** @var bool */
    protected $hasError = false;

    /**
     * @return \DateTime
     */
    public function getConcernedDay()
    {
        if (!$this->concernedDay && $this->start) {
            return new \DateTime($this->start->format('Y-m-d'));
        }
        return $this->concernedDay;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd(\DateTime $end)
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
     * @param boolean $hasError
     */
    public function setHasError($hasError)
    {
        $this->hasError = $hasError;
    }

    /**
     * @return boolean
     */
    public function getHasError()
    {
        return $this->hasError;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart(\DateTime $start)
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

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param \DateTime $concernedDay
     */
    public function setConcernedDay(\DateTime $concernedDay)
    {
        $this->concernedDay = $concernedDay;
    }

}
