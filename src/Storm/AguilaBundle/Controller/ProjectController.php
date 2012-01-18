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
     * @Route("/", name="homepage")
     * @Route("/projects", name="aguila_project_list")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $projects = $em->getRepository('AguilaBundle:Project')->findAll();

        $project = new Project();
        $form   = $this->createForm(new ProjectType(), $project);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($project);
                $em->flush();

                return $this->redirect($this->generateUrl('aguila_project_show', array('slug' => $project->getSlug())));

            }
        }

        return array(
            'projects' => $projects,
            'form'   => $form->createView()
        );
    }

    /**
     * Finds and displays a Project.
     *
     * @Route("/{slug}", name="aguila_project_show")
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $project = $em->getRepository('AguilaBundle:Project')->findOneBy(array('slug' => $slug));

        if (!$project) {
            throw $this->createNotFoundException($this->get('translator')->trans('project.not_found', array(), 'AguilaBundle'));
        }

        $deleteForm = $this->createDeleteForm($slug);

        return array(
            'project'     => $project,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Project.
     *
     * @Route("/{slug}/admin/edit", name="aguila_project_edit")
     * @Template()
     */
    public function editAction($slug)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $project = $em->getRepository('AguilaBundle:Project')->findOneBy(array('slug' => $slug));

        if (!$project) {
            throw $this->createNotFoundException($this->get('translator')->trans('project.not_found', array(), 'AguilaBundle'));
        }

        $editForm = $this->createForm(new ProjectType(), $project);
        $deleteForm = $this->createDeleteForm($slug);

        return array(
            'project'      => $project,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Project.
     *
     * @Route("/{slug}/admin/update", name="aguila_project_update")
     * @Method("post")
     * @Template("AguilaBundle:Project:edit.html.twig")
     */
    public function updateAction($slug)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $project = $em->getRepository('AguilaBundle:Project')->findOneBy(array('slug' => $slug));

        if (!$project) {
            throw $this->createNotFoundException($this->get('translator')->trans('project.not_found', array(), 'AguilaBundle'));
        }

        $editForm   = $this->createForm(new ProjectType(), $project);
        $deleteForm = $this->createDeleteForm($slug);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('aguila_project_edit', array('slug' => $slug)));
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
     * @Route("/{slug}/admin/delete", name="aguila_project_delete")
     * @Method("post")
     */
    public function deleteAction($slug)
    {
        $form = $this->createDeleteForm($slug);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $project = $em->getRepository('AguilaBundle:Project')->findOneBy(array('slug' => $slug));

            if (!$project) {
                throw $this->createNotFoundException($this->get('translator')->trans('project.not_found', array(), 'AguilaBundle'));
            }

            $em->remove($project);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('aguila_project_list'));
    }

    private function createDeleteForm($slug)
    {
        return $this->createFormBuilder(array('slug' => $slug))
            ->add('slug', 'hidden')
            ->getForm()
        ;
    }
}
