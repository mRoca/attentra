<?php

namespace Attentra\ApiBundle\Handler;

use Attentra\ApiBundle\Exception\InvalidFormException;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;

abstract class GenericRestHandler implements GenericRestHandlerInterface
{
    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $om;

    /** @var string */
    protected $entityClass;

    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    protected $repository;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    protected $formFactory;

    /** @var string */
    protected $formTypeClass;

    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory, $formTypeClass)
    {
        $this->om            = $om;
        $this->entityClass   = $entityClass;
        $this->repository    = $this->om->getRepository($this->entityClass);
        $this->formFactory   = $formFactory;
        $this->formTypeClass = $formTypeClass;
    }

    /**
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), null, $limit, $offset);
    }

    /**
     * @param array $parameters
     * @return object
     */
    public function post(array $parameters)
    {
        $object = new $this->entityClass();
        return $this->processForm($object, $parameters, 'POST');
    }

    /**
     * @param object $object
     * @param array $parameters
     * @return object
     */
    public function put($object, array $parameters)
    {
        return $this->processForm($object, $parameters, 'PUT');
    }

    /**
     * @param object $object
     * @param array $parameters
     * @return object
     */
    public function patch($object, array $parameters)
    {
        return $this->processForm($object, $parameters, 'PATCH');
    }

    /**
     * @return FormTypeInterface
     */
    public function getFormType()
    {
        return new $this->formTypeClass();
    }

    /**
     * @param object $object
     * @param array $parameters
     * @param string $method
     * @return mixed
     * @throws InvalidFormException
     */
    protected function processForm($object, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create($this->getFormType(), $object, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {
            $page = $form->getData();
            $this->om->persist($page);
            $this->om->flush($page);
            return $page;
        }
        throw new InvalidFormException('Invalid submitted data', $form);
    }

} 
