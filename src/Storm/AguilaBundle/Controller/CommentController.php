<?php

namespace Storm\AguilaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Storm\AguilaBundle\Entity\Comment;
use Storm\AguilaBundle\Form\CommentType;

/**
 * Comment controller.
 *
 * @Route("/comment")
 */
class CommentController extends Controller
{
    /**
     * Lists all comments.
     *
     * @Template()
     */
    public function listAction($task_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $comments = $em->getRepository('AguilaBundle:Comment')->findBy(array('task' => $task_id));

        return array('comments' => $comments);
    }


    /**
     * Finds and displays a comment.
     *
     * @Route("/{id}/show", name="aguila_comment_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $comment = $em->getRepository('AguilaBundle:Comment')->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('Unable to find {{ comment }} comment.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'comment'      => $comment,
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Displays a form to create a new comment.
     *
     * @Template()
     */
    public function newAction($task_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $task = $em->getRepository('AguilaBundle:Task')->find($task_id);

        $comment = new Comment();
        $form   = $this->createForm(new CommentType(), $comment);

        return array(
            'form' => $form->createView(),
            'task' => $task,
        );
    }

    /**
     * Creates a new comment.
     *
     * @Route("/create/{task_id}", name="aguila_comment_create")
     * @Method("post")
     * @Template("AguilaBundle:Comment:new.html.twig")
     */
    public function createAction($task_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $task = $em->getRepository('AguilaBundle:Task')->find($task_id);

        $comment  = new Comment();
        $request = $this->getRequest();
        $form    = $this->createForm(new CommentType(), $comment);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $comment->setTask($task);
            $comment->setType(Comment::POST);
            $comment->setUser($this->get('security.context')->getToken()->getUser());

            $em->persist($comment);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('aguila_task_show', array(
            'project_slug' => $task->getFeature()->getProject(),
            'number' => $task->getNumber(),
        )));
    }

    /**
     * Displays a form to edit an existing comment.
     *
     * @Route("/{id}/edit", name="comment_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $comment = $em->getRepository('AguilaBundle:Comment')->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('Unable to find Comment comment.');
        }

        $editForm = $this->createForm(new CommentType(), $comment);

        return array(
            'comment'      => $comment,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing comment.
     *
     * @Route("/{id}/update", name="comment_update")
     * @Method("post")
     * @Template("AguilaBundle:Comment:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $comment = $em->getRepository('AguilaBundle:Comment')->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('Unable to find Comment comment.');
        }

        $editForm   = $this->createForm(new CommentType(), $comment);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($comment);
            $em->flush();

            return $this->redirect($this->generateUrl('comment_edit', array('id' => $id)));
        }

        return array(
            'comment'      => $comment,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a comment.
     *
     * @Route("/{id}/delete", name="aguila_comment_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $comment = $em->getRepository('AguilaBundle:Comment')->find($id);

            if (!$comment) {
                throw $this->createNotFoundException();
            }

            $task = $comment->getTask();

            $em->remove($comment);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('aguila_task_show', array(
            'project_slug' => $task->getFeature()->getProject(),
            'number' => $task->getNumber(),
        )));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
