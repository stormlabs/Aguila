<?php

namespace Storm\AguilaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use Storm\AguilaBundle\Entity\Project;
use Storm\AguilaBundle\Form\ProjectType;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;


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
     * @Route("/", name="aguila_project_list")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $projects = $em->getRepository('AguilaBundle:Project')->findBy(array('public' => true));

        return array('projects' => $projects);
    }

    /**
     * Finds and displays a Project.
     *
     * @Route("/{slug}", name="aguila_project_show")
     * @Template()
     * @ParamConverter("slug", class="AguilaBundle:Project")
     * @SecureParam(name="project", permissions="VIEW")
     */
    public function showAction(Project $project)
    {
        $deleteForm = $this->createDeleteForm($project->getSlug());

        return array(
            'project'     => $project,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Project.
     *
     * @Route("/project/new", name="aguila_project_new")
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
     * @Route("/project/create", name="aguila_project_create")
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

            $this->grantAccess(MaskBuilder::MASK_OWNER, $project);

            return $this->redirect($this->generateUrl('aguila_project_show', array('slug' => $project->getSlug())));
        }

        return array(
            'project' => $project,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Project.
     *
     * @Route("/{slug}/admin/edit", name="aguila_project_edit")
     * @Template()
     * @ParamConverter("project", class="AguilaBundle:Project")
     * @SecureParam(name="project", permissions="EDIT")
     */
    public function editAction(Project $project)
    {
        $editForm = $this->createForm(new ProjectType(), $project);
        $deleteForm = $this->createDeleteForm($project->getSlug());

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
     * @ParamConverter("project", class="AguilaBundle:Project")
     * @SecureParam(name="project", permissions="EDIT")
     */
    public function updateAction(Project $project)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $editForm   = $this->createForm(new ProjectType(), $project);
        $deleteForm = $this->createDeleteForm($project->getSlug());

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('aguila_project_edit', array('slug' => $project->getSlug())));
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
     * @ParamConverter("project", class="AguilaBundle:Project")
     * @SecureParam(name="project", permissions="DELETE")
     */
    public function deleteAction(Project $project)
    {
        $form = $this->createDeleteForm($project->getSlug());
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

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
