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
     * Lists all Feature features.
     *
     * @Route("/", name="feature_list")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $features = $em->getRepository('AguilaBundle:Feature')->findAll();

        return array('features' => $features);
    }

    /**
     * Finds and displays a Feature feature.
     *
     * @Route("/{id}/show", name="feature_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $feature = $em->getRepository('AguilaBundle:Feature')->find($id);

        if (!$feature) {
            throw $this->createNotFoundException('Unable to find Feature feature.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'feature'      => $feature,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Feature feature.
     *
     * @Route("/new/{project_id}", name="feature_new")
     * @Template()
     */
    public function newAction($project_id)
    {
        $feature = new Feature();
        $feature->setProject($project_id);
        $form   = $this->createForm(new FeatureType(), $feature);

        return array(
            'feature' => $feature,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Feature feature.
     *
     * @Route("/create", name="feature_create")
     * @Method("post")
     * @Template("AguilaBundle:Feature:new.html.twig")
     */
    public function createAction()
    {
        $feature  = new Feature();
        $request = $this->getRequest();
        $form    = $this->createForm(new FeatureType(), $feature);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $project = $this->getDoctrine()->getEntityManager()->getReference('AguilaBundle:Project', $feature->getProject());
            $feature->setProject($project);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($feature);
            $em->flush();

            return $this->redirect($this->generateUrl('feature_show', array('id' => $feature->getId())));
            
        }

        return array(
            'feature' => $feature,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Feature feature.
     *
     * @Route("/{id}/edit", name="feature_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $feature = $em->getRepository('AguilaBundle:Feature')->find($id);

        if (!$feature) {
            throw $this->createNotFoundException('Unable to find Feature feature.');
        }

        $editForm = $this->createForm(new FeatureType(), $feature);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'feature'      => $feature,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Feature feature.
     *
     * @Route("/{id}/update", name="feature_update")
     * @Method("post")
     * @Template("AguilaBundle:Feature:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $feature = $em->getRepository('AguilaBundle:Feature')->find($id);

        if (!$feature) {
            throw $this->createNotFoundException('Unable to find Feature feature.');
        }

        $editForm   = $this->createForm(new FeatureType(), $feature);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($feature);
            $em->flush();

            return $this->redirect($this->generateUrl('feature_edit', array('id' => $id)));
        }

        return array(
            'feature'      => $feature,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Feature feature.
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
            $feature = $em->getRepository('AguilaBundle:Feature')->find($id);

            if (!$feature) {
                throw $this->createNotFoundException('Unable to find Feature feature.');
            }

            $em->remove($feature);
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
