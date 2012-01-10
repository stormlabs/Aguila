<?php

namespace Storm\AguilaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Storm\AguilaBundle\Entity\Feature;
use Storm\AguilaBundle\Form\FeatureType;

/**
 * Feature controller.
 *
 * @Route("/feature")
 */
class FeatureController extends Controller
{
    /**
     * Lists all Feature entities.
     *
     * @Route("/", name="feature")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('AguilaBundle:Feature')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a Feature entity.
     *
     * @Route("/{id}/show", name="feature_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('AguilaBundle:Feature')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feature entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new Feature entity.
     *
     * @Route("/new", name="feature_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Feature();
        $form   = $this->createForm(new FeatureType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Feature entity.
     *
     * @Route("/create", name="feature_create")
     * @Method("post")
     * @Template("AguilaBundle:Feature:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Feature();
        $request = $this->getRequest();
        $form    = $this->createForm(new FeatureType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('feature_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Feature entity.
     *
     * @Route("/{id}/edit", name="feature_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('AguilaBundle:Feature')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feature entity.');
        }

        $editForm = $this->createForm(new FeatureType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Feature entity.
     *
     * @Route("/{id}/update", name="feature_update")
     * @Method("post")
     * @Template("AguilaBundle:Feature:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('AguilaBundle:Feature')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feature entity.');
        }

        $editForm   = $this->createForm(new FeatureType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('feature_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Feature entity.
     *
     * @Route("/{id}/delete", name="feature_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('AguilaBundle:Feature')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Feature entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('feature'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
