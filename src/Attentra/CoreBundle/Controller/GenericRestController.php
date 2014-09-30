<?php

namespace Attentra\CoreBundle\Controller;

use Attentra\CoreBundle\Exception\InvalidFormException;
use Attentra\CoreBundle\Handler\GenericRestHandler;
use Attentra\CoreBundle\Handler\GenericRestHandlerInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Class GenericRestController
 */
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
     *   },
     *   output = {
     *      "attentra_type" = "entity_collection"
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
        return $this->handler->all($paramFetcher->get('limit'), $paramFetcher->get('offset') ?: 0);
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
     *   },
     *   output = {
     *      "attentra_type" = "entity"
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
        return $this->getOr404($id);
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
     * @return Form
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
     *   description = "Creates a new entity from the submitted data.",
     *   statusCodes = {
     *     201 = "Returned when the entity is created",
     *     400 = "Returned when the form has errors"
     *   },
     *   input = {
     *      "attentra_type" = "form_type"
     *   }
     * )
     *
     * @param Request $request
     *
     * @return View
     */
    public function postAction(Request $request)
    {
        try {
            $newEntity = $this->handler->post($request->request->all());

            $routeOptions = array(
                'id'      => $newEntity->getId(),
                '_format' => $request->get('_format')
            );

            $getRouteName = str_replace('_post_', '_get_', $request->get('_route'));

            if ($this->container->get('router')->getRouteCollection()->get($getRouteName)) {
                return $this->routeRedirectView($getRouteName, $routeOptions, Codes::HTTP_CREATED);
            } else {
                return $this->view(null, Codes::HTTP_CREATED);
            }

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
     *   },
     *   input = {
     *      "attentra_type" = "form_type"
     *   }
     * )
     *
     * @param Request $request
     * @param int $id The entity id
     *
     * @return View
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

            $getRouteName = str_replace('_put_', '_get_', $request->get('_route'));

            if ($this->container->get('router')->getRouteCollection()->get($getRouteName)) {
                return $this->routeRedirectView($getRouteName, $routeOptions, $statusCode);
            } else {
                return $this->view(null, $statusCode);
            }

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
     *   },
     *   input = {
     *      "attentra_type" = "form_type"
     *   }
     * )
     *
     * @param Request $request
     * @param int $id The entity id
     *
     * @return View
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

            $getRouteName = str_replace('_patch_', '_get_', $request->get('_route'));

            if ($this->container->get('router')->getRouteCollection()->get($getRouteName)) {
                return $this->routeRedirectView($getRouteName, $routeOptions, Codes::HTTP_NO_CONTENT);
            } else {
                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Delete an existing entity.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     404 = "Returned when the entity is not found"
     *   }
     * )
     *
     * @param int $id The entity id
     *
     * @return View
     *
     * @throws NotFoundHttpException when page not exist
     */
    public function deleteAction($id)
    {
        $this->handler->delete($this->getOr404($id));

        return $this->view(null, Codes::HTTP_NO_CONTENT);
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

    /**
     * @return \Attentra\CoreBundle\Handler\GenericRestHandler|\Attentra\CoreBundle\Handler\GenericRestHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

}
