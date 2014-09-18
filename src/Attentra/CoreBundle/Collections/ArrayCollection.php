<?php

namespace Attentra\CoreBundle\Collections;

use Doctrine\Common\Collections\ArrayCollection as Base;
use Doctrine\Common\Collections\Collection;

class ArrayCollection extends Base
{

    /**
     * @return ArrayCollection
     */
    public function uniqueById()
    {
        $ids      = [];
        $entities = new ArrayCollection();

        foreach ($this->toArray() as $item) {
            if (method_exists($item, 'getId') && !isset($ids[$item->getId()])) {
                $ids[$item->getId()] = true;
                $entities->add($item);
            }
        }

        return $entities;
    }

    /**
     * @param Array|ArrayCollection|Collection $newItems
     * @return bool
     */
    public function merge($newItems)
    {
        foreach ($newItems as $n) {
            $this->add($n);
        }

        return true;
    }

    /**
     * @param string $method
     * @param mixed $searchValue
     * @return mixed
     */
    public function findFirstByMethod($method, $searchValue)
    {
        foreach ($this->toArray() as $item) {
            if (method_exists($item, $method) && call_user_func([&$item, $method]) === $searchValue) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param string $method
     * @param mixed $searchValue
     * @return mixed
     */
    public function findByMethod($method, $searchValue)
    {
        return $this->filter(
            function ($item) use ($method, $searchValue) {
                return (method_exists($item, $method) && call_user_func([&$item, $method]) === $searchValue);
            }
        );
    }

} 
