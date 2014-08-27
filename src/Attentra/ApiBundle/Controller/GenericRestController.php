<?php

namespace Attentra\ApiBundle\Controller;

use Attentra\ApiBundle\Exception\InvalidFormException;
use Attentra\ApiBundle\Handler\GenericRestHandler;
use Attentra\ApiBundle\Handler\GenericRestHandlerInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

abstract class GenericRestController extends FOSRestController
{
    /** @var GenericRestHandlerInterface | GenericRestHandler */
    protected $handler;

    public function __construct(Container $container, GenericRestHandlerInterface $handler)
    {
        $this->setContainer($container);
        $this->handler = $handler;
    }

    /**
     * List all entities.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages.")
     * @QueryParam(name="limit", requirements="\d+", default="5", description="How many pages to return.")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return array
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        return $this->handler->all($paramFetcher->get('limit'), $paramFetcher->get('offset') ? : 0);
    }

    /**
     * Get single entity.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets an entity for a given id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the entity is not found"
     *   }
     * )
     *
     * @param int $id The entity id
     * @return array
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function getAction($id)
    {
        $page = $this->getOr404($id);

        return $page;
    }

    /**
     * Presents the form to use to create a new entity.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @return FormTypeInterface
     */
    public function newAction()
    {
        if ($this->handler instanceof GenericRestHandler) {
            return $this->createForm($this->handler->getFormType());
        }

        return true;
    }

    /**
     * Create an entity from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Attentra\ResourceBundle\Form\ResourceType",
     *   description = "Creates a new entity from the submitted data.",
     *   statusCodes = {
     *     201 = "Returned when the entity is created",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request
     *
     * @return FormTypeInterface
     */
    public function postAction(Request $request)
    {
        try {
            $newEntity = $this->handler->post($request->request->all());

            $routeOptions = array(
                'id'      => $newEntity->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('attentra_api_get_resource', $routeOptions, Codes::HTTP_CREATED);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing entity from the submitted data or create a new entity at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     201 = "Returned when the entity is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request
     * @param int $id The entity id
     *
     * @return FormTypeInterface
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function putAction(Request $request, $id)
    {
        try {
            if (!($entity = $this->handler->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $entity     = $this->handler->post($request->request->all());
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $entity     = $this->handler->put($entity, $request->request->all());
            }

            $routeOptions = array(
                'id'      => $entity->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('attentra_api_get_resource', $routeOptions, $statusCode);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing entity from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *     404 = "Returned when the entity is not found"
     *   }
     * )
     *
     * @param Request $request
     * @param int $id The entity id
     *
     * @return FormTypeInterface
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function patchAction(Request $request, $id)
    {
        try {
            $page = $this->handler->patch($this->getOr404($id), $request->request->all());

            $routeOptions = array(
                'id'      => $page->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('attentra_api_get_resource', $routeOptions, Codes::HTTP_NO_CONTENT);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }


    /**
     * Fetch an entity or throw an 404 Exception.
     *
     * @param mixed $id
     * @return object
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->handler->get($id))) {
            throw new NotFoundHttpException(sprintf('The entity \'%s\' was not found.', $id));
        }

        return $entity;
    }
}
