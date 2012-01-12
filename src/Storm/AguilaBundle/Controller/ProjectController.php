<?php

namespace Storm\AguilaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Storm\AguilaBundle\Entity\Project;
use Storm\AguilaBundle\Form\ProjectType;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
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
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getEntityManager();


        $project = $em->getRepository('AguilaBundle:Project')->findOneBy(array('slug' => $slug));

        if (!$project) {
            throw $this->createNotFoundException($this->get('translator')->trans('project.not_found', array(), 'AguilaBundle'));
        }

        $securityContext = $this->get('security.context');
        // if the user has permission to edit the project
        if (false === $securityContext->isGranted('VIEW', $project))
        {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($slug);

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

            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($project);
            $acl = $aclProvider->createAcl($objectIdentity);

            $securityContext = $this->get('security.context');
            $user = $securityContext->getToken()->getUser();
            $securityIdentity = UserSecurityIdentity::fromAccount($user);

            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            $aclProvider->updateAcl($acl);

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
     */
    public function editAction($slug)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $project = $em->getRepository('AguilaBundle:Project')->findOneBy(array('slug' => $slug));

        if (!$project) {
            throw $this->createNotFoundException($this->get('translator')->trans('project.not_found', array(), 'AguilaBundle'));
        }

        $securityContext = $this->get('security.context');
        // check for edit access
        if (false === $securityContext->isGranted('EDIT', $project))
        {
            throw new AccessDeniedException();
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

        $securityContext = $this->get('security.context');
        // check for edit access
        if (false === $securityContext->isGranted('EDIT', $project))
        {
            throw new AccessDeniedException();
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

            $securityContext = $this->get('security.context');
            // check for delete access
            if (false === $securityContext->isGranted('DELETE', $project))
            {
                throw new AccessDeniedException();
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
