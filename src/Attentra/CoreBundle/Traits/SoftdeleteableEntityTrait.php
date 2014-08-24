<?php

namespace Attentra\CoreBundle\Traits;

/**
 * SoftdeleteableEntityTrait Trait, usable with PHP >= 5.4
 *
 */
trait SoftdeleteableEntityTrait
{
    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deleted_at;

    /**
     * Sets deleted_at.
     *
     * @param  \DateTime $deletedAt
     * @return $this
     */
    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deleted_at = $deletedAt;

        return $this;
    }

    /**
     * Returns deleted_at.
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

}
