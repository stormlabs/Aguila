<?php

namespace Storm\AguilaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Storm\AguilaBundle\Entity\Feature;
use Storm\AguilaBundle\Form\FeatureType;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Feature controller.
 *
 * @Route("/{project_slug}")
 */
class FeatureController extends Controller
{
    /**
     * Lists all Feature features.
     *
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
     * @Route("/{slug}", name="aguila_feature_show")
     * @Method("get")
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $feature = $em->getRepository('AguilaBundle:Feature')->findOneBy(array('slug' => $slug));

        if (!$feature) {
            throw $this->createNotFoundException($this->get('translator')->trans('feature.not_found', array(), 'AguilaBundle'));
        }

        $securityContext = $this->get('security.context');
        // if the user has permission to edit the project
        if (false === $securityContext->isGranted('VIEW', $feature->getProject()))
        {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($slug);

        return array(
            'feature'      => $feature,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Feature feature.
     *
     * @Template()
     */
    public function newAction($project_slug)
    {
        $feature = new Feature();
        $form   = $this->createForm(new FeatureType(), $feature);

        return array(
            'feature' => $feature,
            'form'   => $form->createView(),
            'project_slug' => $project_slug,
        );
    }

    /**
     * Creates a new Feature feature.
     *
     * @Route("/feature/create", name="aguila_feature_create")
     * @Method("post")
     * @Template("AguilaBundle:Feature:new.html.twig")
     */
    public function createAction($project_slug)
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getEntityManager();
        $project = $em->getRepository('AguilaBundle:Project')->findOneBy(array('slug' => $project_slug));

        $securityContext = $this->get('security.context');
        // if the user has permission to edit the project
        if (false === $securityContext->isGranted('EDIT', $project))
        {
            throw new AccessDeniedException();
        }

        $feature  = new Feature();
        $request = $this->getRequest();
        $form    = $this->createForm(new FeatureType(), $feature);
        $form->bindRequest($request);

        if ($form->isValid()) {

            $feature->setProject($project);

            $em->persist($feature);
            $em->flush();

            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($feature);
            $acl = $aclProvider->createAcl($objectIdentity);

            $securityContext = $this->get('security.context');
            $user = $securityContext->getToken()->getUser();
            $securityIdentity = UserSecurityIdentity::fromAccount($user);

            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            $aclProvider->updateAcl($acl);

            return $this->redirect($this->generateUrl('aguila_feature_show', array(
                'project_slug' => $project_slug,
                'slug' => $feature->getSlug()
            )));
        }

        return array(
            'feature' => $feature,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Feature feature.
     *
     * @Route("/feature/{slug}/edit", name="aguila_feature_edit")
     * @Template()
     */
    public function editAction($slug)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $feature = $em->getRepository('AguilaBundle:Feature')->findOneBy(array('slug' => $slug));

        if (!$feature) {
            throw $this->createNotFoundException($this->get('translator')->trans('feature.not_found', array(), 'AguilaBundle'));
        }

        $securityContext = $this->get('security.context');
        // if the user has permission to edit the project
        if (false === $securityContext->isGranted('EDIT', $feature->getProject()))
        {
            throw new AccessDeniedException();
        }

        $editForm = $this->createForm(new FeatureType(), $feature);
        $deleteForm = $this->createDeleteForm($slug);

        return array(
            'feature'      => $feature,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Feature feature.
     *
     * @Route("/feature/{slug}/update", name="aguila_feature_update")
     * @Method("post")
     * @Template("AguilaBundle:Feature:edit.html.twig")
     */
    public function updateAction($project_slug, $slug)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $feature = $em->getRepository('AguilaBundle:Feature')->findOneBy(array('slug' => $slug));

        if (!$feature) {
            throw $this->createNotFoundException($this->get('translator')->trans('feature.not_found', array(), 'AguilaBundle'));
        }

        $securityContext = $this->get('security.context');
        // if the user has permission to edit the project
        if (false === $securityContext->isGranted('EDIT', $feature->getProject()))
        {
            throw new AccessDeniedException();
        }

        $editForm   = $this->createForm(new FeatureType(), $feature);
        $deleteForm = $this->createDeleteForm($slug);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($feature);
            $em->flush();

            return $this->redirect($this->generateUrl('aguila_feature_show', array(
                'project_slug' => $project_slug,
                'slug' => $slug,
            )));
        }

        return array(
            'feature'     => $feature,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Feature feature.
     *
     * @Route("/feature/{slug}/delete", name="aguila_feature_delete")
     * @Method("post")
     */
    public function deleteAction($project_slug, $slug)
    {
        $form = $this->createDeleteForm($slug);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $feature = $em->getRepository('AguilaBundle:Feature')->findOneBy(array('slug' => $slug));

            if (!$feature) {
                throw $this->createNotFoundException($this->get('translator')->trans('feature.not_found', array(), 'AguilaBundle'));
            }

            $securityContext = $this->get('security.context');
            // check for edit access
            if (false === $securityContext->isGranted('DELETE', $feature))
            {
                throw new AccessDeniedException();
            }

            $em->remove($feature);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('aguila_feature_show', array(
            'project_slug' => $project_slug,
            'slug' => $slug,
        )));
    }

    private function createDeleteForm($slug)
    {
        return $this->createFormBuilder(array('slug' => $slug))
            ->add('slug', 'hidden')
            ->getForm()
        ;
    }
}
