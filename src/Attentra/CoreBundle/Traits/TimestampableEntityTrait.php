<?php

namespace Attentra\CoreBundle\Traits;

/**
 * Timestampable Trait, usable with PHP >= 5.4
 *
 */
trait TimestampableEntityTrait
{
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="update_at", type="datetime")
     */
    protected $updated_at;

    /**
     * Sets created_at.
     *
     * @param  \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Returns created_at.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Sets updated_at.
     *
     * @param  \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Returns updated_at.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }


}
