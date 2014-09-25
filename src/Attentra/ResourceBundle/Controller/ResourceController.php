<?php

namespace Attentra\ResourceBundle\Controller;

use Attentra\CoreBundle\Form\Type\LinkButtonType;
use Attentra\ResourceBundle\Entity\Resource;
use Attentra\ResourceBundle\Form\ResourceType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Manage Attentra resource entities
 *
 * @Route("/resource")
 *
 */
class ResourceController extends Controller
{
    /**
     * Lists all entities.
     *
     * @Route("/", name="attentra_resource")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        return array(
            'entities' => $em->getRepository('AttentraResourceBundle:Resource')->findBy([], ['name' => 'asc']),
        );
    }

    /**
     * Displays a form to create a new entity.
     *
     * @Route("/new", name="attentra_resource_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Resource();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new entity.
     *
     * @Route("/", name="attentra_resource_create")
     * @Method("POST")
     * @Template("AttentraResourceBundle:Resource:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Resource();
        $form   = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('attentra_resource_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays an entity.
     *
     * @Route("/{id}", name="attentra_resource_show", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AttentraResourceBundle:Resource')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find the entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to edit an existing entity.
     *
     * @Route("/{id}/edit", name="attentra_resource_edit", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AttentraResourceBundle:Resource')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find the entity.');
        }

        $editForm   = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing entity.
     *
     * @Route("/{id}", name="attentra_resource_update")
     * @Method({"PUT", "DELETE"})
     * @Template("AttentraResourceBundle:Resource:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AttentraResourceBundle:Resource')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find the entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('attentra_resource'));
        }

        $deleteForm = $this->createDeleteForm($id);
        $deleteForm->handleRequest($request);
        if ($deleteForm->isValid()) {

            $em->remove($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('attentra_resource'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Find a resource by the identifier key
     *
     * @Route("/byidentifier/{identifier}", name="attentra_resource_byidentifier")
     * @Method("GET")
     */
    public function showByIdentifier($identifier)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AttentraResourceBundle:Resource')->findOneBy(array('identifier' => $identifier));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find the entity.');
        }

        return new RedirectResponse($this->generateUrl('attentra_resource_show', array('id' => $entity->getId())));
    }

    // ======================================

    /**
     * Creates a form to create an entity.
     *
     * @param \Attentra\ResourceBundle\Entity\Resource|Resource $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Resource $entity)
    {
        $form = $this->createForm(
            new ResourceType(),
            $entity,
            array(
                'action' => $this->generateUrl('attentra_resource_create'),
                'method' => 'POST'
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create', 'attr' => ['icon' => 'ok']));
        $form->add('return', new LinkButtonType(), array('link' => $this->generateUrl('attentra_resource')));

        return $form;
    }

    /**
     * Creates a form to edit an entity.
     *
     * @param \Attentra\ResourceBundle\Entity\Resource|Resource $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(Resource $entity)
    {
        $form = $this->createForm(
            new ResourceType(),
            $entity,
            array(
                'action' => $this->generateUrl('attentra_resource_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => ['icon' => 'ok']));
        $form->add('return', new LinkButtonType(), array('link' => $this->generateUrl('attentra_resource')));

        return $form;
    }

    /**
     * Creates a form to delete an entity by id.
     *
     * @param mixed $id The entity id
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id, Request $request = null)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('attentra_resource_update', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('previous_page', 'hidden', ['data' => $request !== null ? $request->getUri() : ''])
            ->add('confirm', 'checkbox', ['label' => 'I confirm the complete deletion of this item and all relatives items', 'constraints' => new NotBlank()])
            ->add('submit', 'submit', ['label' => 'Delete', 'attr' => ['class' => 'btn-danger', 'icon' => 'remove']])
            ->getForm();
    }

} 
