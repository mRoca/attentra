<?php

namespace Attentra\CoreBundle\Handler;

use Attentra\CoreBundle\Exception\InvalidFormException;
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
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), null, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function post(array $parameters)
    {
        $object = new $this->entityClass();
        return $this->processForm($object, $parameters, 'POST');
    }

    /**
     * {@inheritdoc}
     */
    public function put($object, array $parameters)
    {
        return $this->processForm($object, $parameters, 'PUT');
    }

    /**
     * {@inheritdoc}
     */
    public function patch($object, array $parameters)
    {
        return $this->processForm($object, $parameters, 'PATCH');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object)
    {
        $this->om->remove($object);
        $this->om->flush();
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
