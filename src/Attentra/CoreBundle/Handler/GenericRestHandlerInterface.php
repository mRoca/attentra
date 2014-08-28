<?php

namespace Attentra\CoreBundle\Handler;

interface GenericRestHandlerInterface
{
    /**
     * @param $id
     * @return object
     */
    public function get($id);

    /**
     * @param int $limit the limit of the result
     * @param int $offset starting from the offset
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * @param array $parameters
     * @return object
     */
    public function post(array $parameters);

    /**
     * @param object $entity
     * @param array $parameters
     * @return object
     */
    public function put($entity, array $parameters);

    /**
     * @param object $entity
     * @param array $parameters
     * @return object
     */
    public function patch($entity, array $parameters);

    /**
     * @param object $entity
     * @return object
     */
    public function delete($entity);
}
