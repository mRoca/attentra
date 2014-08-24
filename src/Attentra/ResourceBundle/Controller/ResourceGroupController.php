<?php

namespace Attentra\ResourceBundle\Controller;

use Attentra\CoreBundle\Form\Type\LinkButtonType;
use Attentra\ResourceBundle\Entity\Resource;
use Attentra\ResourceBundle\Entity\ResourceGroup;
use Attentra\ResourceBundle\Form\ResourceGroupType;
use Attentra\ResourceBundle\Form\ResourceType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Manage Attentra resource group entities
 *
 * @Route("/resourcegroup")
 *
 */
class ResourceGroupController extends Controller
{
    /**
     * Lists all entities.
     *
     * @Route("/", name="attentra_resourcegroup")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        return array(
            'entities' => $em->getRepository('AttentraResourceBundle:ResourceGroup')->findBy([], ['name' => 'asc']),
        );
    }

    /**
     * Displays a form to create a new entity.
     *
     * @Route("/new", name="attentra_resourcegroup_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ResourceGroup();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new entity.
     *
     * @Route("/", name="attentra_resourcegroup_create")
     * @Method("POST")
     * @Template("AttentraResourceBundle:ResourceGroup:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ResourceGroup();
        $form   = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('attentra_resourcegroup_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays an entity.
     *
     * @Route("/{id}", name="attentra_resourcegroup_show", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AttentraResourceBundle:ResourceGroup')->find($id);

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
     * @Route("/{id}/edit", name="attentra_resourcegroup_edit", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AttentraResourceBundle:ResourceGroup')->find($id);

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
     * @Route("/{id}", name="attentra_resourcegroup_update")
     * @Method({"PUT", "DELETE"})
     * @Template("AttentraResourceBundle:ResourceGroup:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AttentraResourceBundle:ResourceGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find the entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

//            return $this->redirect($this->generateUrl('attentra_resourcegroup_edit', array('id' => $id)));
            return $this->redirect($this->generateUrl('attentra_resourcegroup'));
        }

        $deleteForm = $this->createDeleteForm($id);
        $deleteForm->handleRequest($request);
        if ($deleteForm->isValid()) {

            $em->remove($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('attentra_resourcegroup'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    // ======================================

    /**
     * Creates a form to create an entity.
     *
     * @param \Attentra\ResourceBundle\Entity\ResourceGroup $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ResourceGroup $entity)
    {
        $form = $this->createForm(
            new ResourceGroupType(),
            $entity,
            array(
                'action' => $this->generateUrl('attentra_resourcegroup_create'),
                'method' => 'POST'
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create', 'attr' => ['icon' => 'ok']));
        $form->add('return', new LinkButtonType(), array('link' => $this->generateUrl('attentra_resourcegroup')));

        return $form;
    }

    /**
     * Creates a form to edit an entity.
     *
     * @param \Attentra\ResourceBundle\Entity\ResourceGroup $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(ResourceGroup $entity)
    {
        $form = $this->createForm(
            new ResourceGroupType(),
            $entity,
            array(
                'action' => $this->generateUrl('attentra_resourcegroup_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => ['icon' => 'ok']));
        $form->add('return', new LinkButtonType(), array('link' => $this->generateUrl('attentra_resourcegroup')));

        return $form;
    }

    /**
     * Creates a form to delete an entity by id.
     *
     * @param mixed $id The entity id
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id, Request $request = null)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('attentra_resourcegroup_update', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('previous_page', 'hidden', ['data' => $request !== null ? $request->getUri() : ''])
            ->add('confirm', 'checkbox', ['label' => 'I confirm the complete deletion of this item and all relatives items', 'constraints' => new NotBlank()])
            ->add('submit', 'submit', ['label' => 'Delete', 'attr' => ['class' => 'btn-danger', 'icon' => 'remove']])
            ->getForm();
    }

} 