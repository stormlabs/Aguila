<?php

namespace Storm\AguilaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Storm\AguilaBundle\Entity\Task;
use Storm\AguilaBundle\Entity\Project;
use Storm\AguilaBundle\Entity\Feature;
use Storm\AguilaBundle\Form\TaskType;
use Storm\AguilaBundle\Entity\Comment;
use Storm\AguilaBundle\Form\CommentType;

/**
 * Task controller.
 *
 * @Route("/{project_slug}/task")
 */
class TaskController extends Controller
{
    /**
     * Lists all Task tasks.
     *
     * @Template()
     */
    public function listAction($feature_slug, $status = Task::OPEN)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $tasks = $em->getRepository('AguilaBundle:Task')->findOpenByFeature($feature_slug, $status);

        return array('tasks' => $tasks);
    }

    /**
     * Finds and displays a Task.
     *
     * @Route("/{number}", name="aguila_task_show", requirements={"number" = "\d+"})
     * @Template()
     * @ParamConverter("task", class="AguilaBundle:Task", options={"method"="findOneByProject", "params" = {"project_slug", "number"}})
     */
    public function showAction(Task $task)
    {
        $project = $task->getFeature()->getProject();
        $this->checkAccess(MaskBuilder::MASK_VIEW, $project);

        $comment = new Comment();
        $commentForm = $this->createForm(new CommentType(), $comment);

        return array(
            'task' => $task,
            'comment_form' => $commentForm->createView(),
        );
    }

    /**
     * Comment
     * @Route("/{number}/comment", name="aguila_task_comment", requirements={"number" = "\d+"})
     * @Template("AguilaBundle:Task:show.html.twig")
     * @ParamConverter("task", class="AguilaBundle:Task", options={"method"="findOneByProject", "params" = {"project_slug", "number"}})
     */
    public function commentAction(Task $task)
    {
        $project = $task->getFeature()->getProject();
        $this->checkAccess(MaskBuilder::MASK_VIEW, $project);

        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);

        $request = $this->getRequest();
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $comment->setUser($this->get('security.context')->getToken()->getUser());
            $comment->setType(Comment::POST);

            $task->addComment($comment);

            $em->persist($task);
            $em->persist($comment);
            $em->flush();

            return $this->redirect($this->generateUrl('aguila_task_show', array(
                'project_slug' => $project->getSlug(),
                'number' => $task->getNumber(),
            )));
        }

        return array(
            'task'                    => $task,
            'task_difficulty_choices' => Task::$difficulty_choices,
            'task_priority_choices'   => Task::$priority_choices,
            'task_status_choices'     => Task::$status_choices,
            'comment_form'            => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Task.
     *
     * @Template()
     * @ParamConverter("feature", class="AguilaBundle:Feature", options={"method"="findFeatureBySlugs", "params"={"project_slug", "feature_slug"}})
     */
    public function newAction(Feature $feature)
    {
        $project = $feature->getProject();
        $this->checkAccess(MaskBuilder::MASK_EDIT, $project);

        $task = new Task();
        $form = $this->createForm(new TaskType(), $task);

        return array(
            'task' => $task,
            'form' => $form->createView(),
            'project_slug' => $project->getSlug(),
            'feature_slug' => $feature->getSlug(),
        );
    }

    /**
     * Creates a new Task task.
     *
     * @Route("/create/{feature_slug}", name="aguila_task_create")
     * @Method("post")
     * @Template("AguilaBundle:Task:new.html.twig")
     * @ParamConverter("feature", class="AguilaBundle:Feature", options={"method"="findFeatureBySlugs", "params"={"project_slug", "feature_slug"}})
     */
    public function createAction(Feature $feature)
    {
        $project = $feature->getProject();
        $this->checkAccess(MaskBuilder::MASK_EDIT, $project);

        $task = new Task();
        $form = $this->createForm(new TaskType(), $task);

        $request = $this->getRequest();
        $form->bindRequest($request);

        if ($form->isValid()) {
            /** @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getDoctrine()->getEntityManager();
            $task->setFeature($feature);

            $task->setNumber($number = $project->getTaskCounter());
            $project->setTaskCounter(++$number);

            $user = $this->get('security.context')->getToken()->getUser();
            $task->setReporter($user);
            $em->persist($task);
            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('aguila_task_show', array(
                'project_slug' => $project->getSlug(),
                'number' => $task->getNumber(),
            )));

        }

        return array(
            'task' => $task,
            'form' => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Task task.
     *
     * @Route("/{number}/edit", name="aguila_task_edit", requirements={"number" = "\d+"})
     * @Template()
     * @ParamConverter("task", class="AguilaBundle:Task", options={"method"="findOneByProject", "params" = {"project_slug", "number"}})
     */
    public function editAction(Task $task)
    {
        $project = $task->getFeature()->getProject();
        $this->checkAccess(MaskBuilder::MASK_EDIT, $project);

        $editForm = $this->createForm(new TaskType(), $task);

        return array(
            'task'      => $task,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Task task.
     *
     * @Route("/{number}/update", name="aguila_task_update", requirements={"number" = "\d+"})
     * @Method("post")
     * @Template("AguilaBundle:Task:edit.html.twig")
     * @ParamConverter("task", class="AguilaBundle:Task", options={"method"="findOneByProject", "params" = {"project_slug", "number"}})
     */
    public function updateAction(Task $task)
    {
        $project = $task->getFeature()->getProject();
        $this->checkAccess(MaskBuilder::MASK_EDIT, $project);

        $editForm = $this->createForm(new TaskType(), $task);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $em->persist($task);
            $em->flush();

            return $this->redirect($this->generateUrl('aguila_task_show', array(
                'project_slug' => $project->getSlug(),
                'number' => $task->getNumber(),
            )));
        }

        return array(
            'task' => $task,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Close an existing Task.
     *
     * @Route("/{number}/close", name="aguila_task_close", requirements={"number" = "\d+"})
     * @ParamConverter("task", class="AguilaBundle:Task", options={"method"="findOneByProject", "params" = {"project_slug", "number"}})
     * @Template()
     */
    public function closeAction(Task $task)
    {
        $project = $task->getFeature()->getProject();
        $this->checkAccess(MaskBuilder::MASK_EDIT, $project);

        if ($task->getStatus() == Task::CLOSE) {
            $task->setStatus(Task::OPEN);
        }
        else {
            $task->setStatus(Task::CLOSE);
        }
        $em = $this->getDoctrine()->getEntityManager();

        $em->persist($task);
        $em->flush();

        return $this->redirect($this->generateUrl('aguila_task_show', array(
            'project_slug' => $project->getSlug(),
            'number' => $task->getNumber(),
        )));
    }
}
