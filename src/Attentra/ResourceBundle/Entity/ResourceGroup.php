<?php

namespace Attentra\ResourceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Class ResourceGroup
 *
 * @ORM\Entity
 * @ORM\Table(name="resource_groups", indexes={
 *      @ORM\Index(name="name_idx", columns={"name"})
 * })
 */
class ResourceGroup
{
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var ArrayCollection|Resource[]
     * @ORM\OneToMany(targetEntity="Resource", mappedBy="group", fetch="EXTRA_LAZY")
     * @JMS\Exclude
     */
    protected $resources;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->resources = new ArrayCollection();
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
     * @return ResourceGroup
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
     * Set description
     *
     * @param string $description
     * @return ResourceGroup
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
     * Add resources
     *
     * @param \Attentra\ResourceBundle\Entity\Resource $resources
     * @return ResourceGroup
     */
    public function addResource(\Attentra\ResourceBundle\Entity\Resource $resources)
    {
        $this->resources[] = $resources;

        return $this;
    }

    /**
     * Remove resources
     *
     * @param \Attentra\ResourceBundle\Entity\Resource $resources
     */
    public function removeResource(\Attentra\ResourceBundle\Entity\Resource $resources)
    {
        $this->resources->removeElement($resources);
    }

    /**
     * Get resources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResources()
    {
        return $this->resources;
    }
}
