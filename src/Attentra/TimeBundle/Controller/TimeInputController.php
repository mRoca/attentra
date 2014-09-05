<?php

namespace Attentra\TimeBundle\Controller;

use Attentra\CoreBundle\Form\Type\LinkButtonType;
use Attentra\TimeBundle\Entity\TimeInput;
use Attentra\TimeBundle\Form\TimeInputType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Manage Attentra time inputs entities
 *
 * @Route("/timeinput")
 *
 */
class TimeInputController extends Controller
{
    /**
     * Lists all entities.
     *
     * @Route("/", name="attentra_timeinput")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('AttentraTimeBundle:TimeInput')->createQueryBuilder('t');

        if ($identifier = $this->get('request')->query->get('identifier')) {
            $qb->where($qb->expr()->eq('t.identifier', ':identifier'));
            $qb->setParameter('identifier', $identifier);
        }

        //TODO Remember the last pagination
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $this->get('request')->query->get('page', 1), 50, array('defaultSortFieldName' => 't.datetime', 'defaultSortDirection' => 'desc'));

        return array('pagination' => $pagination, 'identifier' => $identifier);
    }

    /**
     * Displays a form to create a new entity.
     *
     * @Route("/new", name="attentra_timeinput_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TimeInput();
        $entity->setDatetime(new \DateTime());

        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new entity.
     *
     * @Route("/", name="attentra_timeinput_create")
     * @Method("POST")
     * @Template("AttentraTimeBundle:TimeInput:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TimeInput();
        $form   = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('attentra_timeinput_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays an entity.
     *
     * @Route("/{id}", name="attentra_timeinput_show", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AttentraTimeBundle:TimeInput')->find($id);

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
     * @Route("/{id}/edit", name="attentra_timeinput_edit", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AttentraTimeBundle:TimeInput')->find($id);

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
     * @Route("/{id}", name="attentra_timeinput_update")
     * @Method({"PUT", "DELETE"})
     * @Template("AttentraTimeBundle:TimeInput:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AttentraTimeBundle:TimeInput')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find the entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

//            return $this->redirect($this->generateUrl('attentra_timeinput_edit', array('id' => $id)));
            return $this->redirect($this->generateUrl('attentra_timeinput'));
        }

        $deleteForm = $this->createDeleteForm($id);
        $deleteForm->handleRequest($request);
        if ($deleteForm->isValid()) {

            $em->remove($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('attentra_timeinput'));
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
     * @param \Attentra\TimeBundle\Entity\TimeInput $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TimeInput $entity)
    {
        $form = $this->createForm(
            new TimeInputType(),
            $entity,
            array(
                'action' => $this->generateUrl('attentra_timeinput_create'),
                'method' => 'POST'
            )
        );

        $form->add('datetime', 'datetime', ['date_widget' => 'single_text', 'time_widget' => 'single_text', 'date_format' => 'yyyy-MM-dd', 'with_seconds' => true, 'required' => true, 'attr' => ['class' => 'datepicker-container']]);

        $form->add('submit', 'submit', array('label' => 'Create', 'attr' => ['icon' => 'ok']));
        $form->add('return', new LinkButtonType(), array('link' => $this->generateUrl('attentra_timeinput')));

        return $form;
    }

    /**
     * Creates a form to edit an entity.
     *
     * @param \Attentra\TimeBundle\Entity\TimeInput $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(TimeInput $entity)
    {
        $form = $this->createForm(
            new TimeInputType(),
            $entity,
            array(
                'action' => $this->generateUrl('attentra_timeinput_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => ['icon' => 'ok']));
        $form->add('return', new LinkButtonType(), array('link' => $this->generateUrl('attentra_timeinput')));

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
            ->setAction($this->generateUrl('attentra_timeinput_update', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('previous_page', 'hidden', ['data' => $request !== null ? $request->getUri() : ''])
            ->add('confirm', 'checkbox', ['label' => 'I confirm the complete deletion of this item and all relatives items', 'constraints' => new NotBlank()])
            ->add('submit', 'submit', ['label' => 'Delete', 'attr' => ['class' => 'btn-danger', 'icon' => 'remove']])
            ->getForm();
    }

} 
