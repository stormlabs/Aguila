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
     * @Route("/", name="homepage")
     * @Route("/projects", name="aguila_project_list")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $projects = $em->getRepository('AguilaBundle:Project')->findBy(array('public' => true));

        $project = new Project();
        $form   = $this->createForm(new ProjectType(), $project);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($project);
                $em->flush();

                $this->grantAccess(MaskBuilder::MASK_OWNER, $project, true);

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
     * @ParamConverter("project", class="AguilaBundle:Project", options={"method"="findOneBySlug", "params"={"slug"}})
     */
    public function showAction(Project $project)
    {
        //$this->checkAccess(MaskBuilder::MASK_VIEW, $project);
        $deleteForm = $this->createDeleteForm($project->getSlug());

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
     * @ParamConverter("project", class="AguilaBundle:Project", options={"method"="findOneBySlug", "params"={"slug"}})
     */
    public function editAction(Project $project)
    {
        $this->checkAccess(MaskBuilder::MASK_OWNER, $project);

        $editForm = $this->createForm(new ProjectType(), $project);
        $deleteForm = $this->createDeleteForm($project->getSlug());

        return array(
            'project'     => $project,
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
     * @ParamConverter("project", class="AguilaBundle:Project", options={"method"="findBySlug", "params"={"slug"}})
     */
    public function updateAction(Project $project)
    {
        $this->checkAccess(MaskBuilder::MASK_OWNER, $project);

        $editForm   = $this->createForm(new ProjectType(), $project);
        $deleteForm = $this->createDeleteForm($project->getSlug());

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('aguila_project_edit', array('slug' => $project->getSlug())));
        }

        return array(
            'project'     => $project,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Project
     *
     * @Route("/{slug}/admin/delete", name="aguila_project_delete")
     * @Method("post")
     * @ParamConverter("project", class="AguilaBundle:Project", options={"method"="findBySlug", "params"={"slug"}})
     */
    public function deleteAction(Project $project)
    {
        $this->checkAccess(MaskBuilder::MASK_OWNER, $project);

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

    /**
     * Creates a form for deleting Projects
     *
     * @param $slug
     * @return mixed
     */
    private function createDeleteForm($slug)
    {
        return $this->createFormBuilder(array('slug' => $slug))
            ->add('slug', 'hidden')
            ->getForm()
        ;
    }
}
