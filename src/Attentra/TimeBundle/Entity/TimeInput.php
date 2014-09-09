<?php

namespace Attentra\TimeBundle\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TimeInput
 *
 * @Entity(repositoryClass="Attentra\TimeBundle\Repository\TimeInputRepository")
 *
 * @ORM\Table(name="timeinputs", indexes={
 *      @ORM\Index(name="datetime_idx", columns={"datetime"}),
 *      @ORM\Index(name="identifier_idx", columns={"identifier"}),
 *      @ORM\Index(name="type_idx", columns={"type"})
 * })
 *
 */
class TimeInput implements TimeInputInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    protected $datetime;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $identifier;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;


    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '[' . $this->getIdentifier() . '] ' . $this->getDatetime()->format('Y-m-d H:i:s');
    }

    /*
     * ##########################################
     * ####### Generated getters & setters ######
     * ##########################################
     */


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     * @return TimeInput
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return TimeInput
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return TimeInput
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return TimeInput
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
