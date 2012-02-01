<?php

namespace Storm\AguilaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Storm\AguilaBundle\Entity\Comment;
use Storm\AguilaBundle\Entity\Task;
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
     * @ParamConverter("comment", class="AguilaBundle:Comment", options={"method"="find", "params" = {"id"}})
     * @Template()
     */
    public function showAction(Comment $comment)
    {
        $deleteForm = $this->createDeleteForm($comment->getId());

        return array(
            'comment'     => $comment,
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
     * @ParamConverter("task", class="AguilaBundle:Task", options={"method"="find", "params" = {"task_id"}})
     * @Template("AguilaBundle:Comment:new.html.twig")
     */
    public function createAction(Task $task)
    {
        $comment  = new Comment();
        $request = $this->getRequest();
        $form    = $this->createForm(new CommentType(), $comment);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $comment->setTask($task);
            $comment->setType(Comment::POST);
            $comment->setUser($this->get('security.context')->getToken()->getUser());

            $em->persist($comment);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('aguila_task_show', array(
            'project_slug' => $task->getFeature()->getProject()->getSlug(),
            'number'       => $task->getNumber(),
        )));
    }

    /**
     * Displays a form to edit an existing comment.
     *
     * @Route("/{id}/edit", name="comment_edit")
     * @ParamConverter("comment", class="AguilaBundle:Comment", options={"method"="find", "params" = {"id"}})
     * @Template()
     */
    public function editAction(Comment $comment)
    {
        $editForm = $this->createForm(new CommentType(), $comment);

        return array(
            'comment'   => $comment,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing comment.
     *
     * @Route("/{id}/update", name="comment_update")
     * @Method("post")
     * @ParamConverter("comment", class="AguilaBundle:Comment", options={"method"="find", "params" = {"id"}})
     * @Template("AguilaBundle:Comment:edit.html.twig")
     */
    public function updateAction(Comment $comment)
    {
        $editForm   = $this->createForm(new CommentType(), $comment);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $em->persist($comment);
            $em->flush();

            return $this->redirect($this->generateUrl('comment_edit', array('id' => $comment->getId())));
        }

        return array(
            'comment'   => $comment,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a comment.
     *
     * @Route("/{id}/delete", name="aguila_comment_delete")
     * @ParamConverter("comment", class="AguilaBundle:Comment", options={"method"="find", "params" = {"id"}})
     * @Method("post")
     */
    public function deleteAction(Comment $comment)
    {
        $form = $this->createDeleteForm($comment->getId());
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $task = $comment->getTask();

            $em->remove($comment);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('aguila_task_show', array(
            'project_slug' => $task->getFeature()->getProject(),
            'number'       => $task->getNumber(),
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
