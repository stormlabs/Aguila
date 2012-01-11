<?php

namespace Storm\AguilaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Storm\AguilaBundle\Entity\Project;
use Storm\AguilaBundle\Form\ProjectType;

/**
 * Project controller.
 *
 * @Route("")
 */
class ProjectController extends Controller
{
    /**
     * Lists all Projects.
     *
     * @Route("/", name="project_list")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $projects = $em->getRepository('AguilaBundle:Project')->findAll();

        return array('projects' => $projects);
    }

    /**
     * Finds and displays a Project.
     *
     * @Route("/{id}/show", name="project_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $project = $em->getRepository('AguilaBundle:Project')->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'project'      => $project,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new Project.
     *
     * @Route("/new", name="project_new")
     * @Template()
     */
    public function newAction()
    {
        $project = new Project();
        $form   = $this->createForm(new ProjectType(), $project);

        return array(
            'project' => $project,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Project.
     *
     * @Route("/create", name="project_create")
     * @Method("post")
     * @Template("AguilaBundle:Project:new.html.twig")
     */
    public function createAction()
    {
        $project  = new Project();
        $request = $this->getRequest();
        $form    = $this->createForm(new ProjectType(), $project);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('project_show', array('id' => $project->getId())));
            
        }

        return array(
            'project' => $project,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Project.
     *
     * @Route("/{id}/edit", name="project_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $project = $em->getRepository('AguilaBundle:Project')->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project.');
        }

        $editForm = $this->createForm(new ProjectType(), $project);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'project'      => $project,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Project.
     *
     * @Route("/{id}/update", name="project_update")
     * @Method("post")
     * @Template("AguilaBundle:Project:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $project = $em->getRepository('AguilaBundle:Project')->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project.');
        }

        $editForm   = $this->createForm(new ProjectType(), $project);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('project_edit', array('id' => $id)));
        }

        return array(
            'project'      => $project,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Project.
     *
     * @Route("/{id}/delete", name="project_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $project = $em->getRepository('AguilaBundle:Project')->find($id);

            if (!$project) {
                throw $this->createNotFoundException('Unable to find Project.');
            }

            $em->remove($project);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('project'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
