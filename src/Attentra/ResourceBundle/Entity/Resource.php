<?php

namespace Attentra\ResourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Attentra\CoreBundle\Traits;

/**
 * Class Resource
 *
 * @ORM\Entity
 * @ORM\Table(name="resources", indexes={
 *      @ORM\Index(name="name_idx", columns={"name"}),
 *      @ORM\Index(name="identifier_idx", columns={"identifier"})
 * })
 *
 * @Gedmo\SoftDeleteable(fieldName="deleted_at", timeAware=false)
 *
 */
class Resource
{
    use Traits\TimestampableEntityTrait;
    use Traits\SoftdeleteableEntityTrait;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $identifier;

    /**
     * @var ResourceGroup
     * @ORM\ManyToOne(targetEntity="ResourceGroup", inversedBy="resources")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $group;

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
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     * @return Resource
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return Resource
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
     * Set description
     *
     * @param string $description
     * @return Resource
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

    /**
     * Set group
     *
     * @param \Attentra\ResourceBundle\Entity\ResourceGroup $group
     * @return Resource
     */
    public function setGroup(\Attentra\ResourceBundle\Entity\ResourceGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Attentra\ResourceBundle\Entity\ResourceGroup 
     */
    public function getGroup()
    {
        return $this->group;
    }
}
