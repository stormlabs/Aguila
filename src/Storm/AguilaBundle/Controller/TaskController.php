<?php

namespace Storm\AguilaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Storm\AguilaBundle\Entity\Task;
use Storm\AguilaBundle\Form\TaskType;

/**
 * Task controller.
 *
 * @Route("/task")
 */
class TaskController extends Controller
{
    /**
     * Lists all Task tasks.
     *
     * @Route("/", name="task_list")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $tasks = $em->getRepository('AguilaBundle:Task')->findAll();

        return array('tasks' => $tasks);
    }

    /**
     * Finds and displays a Task task.
     *
     * @Route("/{id}/show", name="task_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $task = $em->getRepository('AguilaBundle:Task')->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Unable to find Task task.');
        }

        return array(
            'task'      => $task,
            'task_difficulty_choices' => Task::$difficulty_choices,
            'task_priority_choices' => Task::$priority_choices,
            'task_status_choices' => Task::$status_choices,
        );
    }

    /**
     * Displays a form to create a new Task task.
     *
     * @Route("/new/{feature_id}", name="task_new")
     * @Template()
     */
    public function newAction($feature_id)
    {
        $task = new Task();
        $task->setFeature($feature_id);
        $form   = $this->createForm(new TaskType(), $task);

        return array(
            'task' => $task,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Task task.
     *
     * @Route("/create", name="task_create")
     * @Method("post")
     * @Template("AguilaBundle:Task:new.html.twig")
     */
    public function createAction()
    {
        $task  = new Task();
        $request = $this->getRequest();
        $form    = $this->createForm(new TaskType(), $task);
        $form->bindRequest($request);

        if ($form->isValid()) {
            /** @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getDoctrine()->getEntityManager();
            $feature = $em->getRepository('AguilaBundle:Feature')->find((int) $task->getFeature());
            $task->setFeature($feature);

            $project = $feature->getProject();

            $task->setNumber($number = $project->getTaskCounter());
            $project->setTaskCounter(++$number);

            $user = $this->get('security.context')->getToken()->getUser();
            $task->setReporter($user);
            $em->persist($task);
            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('feature_show', array('id' => $task->getFeature()->getId())));
            
        }

        return array(
            'task' => $task,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Task task.
     *
     * @Route("/{id}/edit", name="task_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $task = $em->getRepository('AguilaBundle:Task')->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Unable to find Task task.');
        }

        $editForm = $this->createForm(new TaskType(), $task);

        return array(
            'task'      => $task,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Task task.
     *
     * @Route("/{id}/update", name="task_update")
     * @Method("post")
     * @Template("AguilaBundle:Task:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $task = $em->getRepository('AguilaBundle:Task')->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Unable to find Task task.');
        }

        $editForm   = $this->createForm(new TaskType(), $task);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($task);
            $em->flush();

            return $this->redirect($this->generateUrl('task_edit', array('id' => $id)));
        }

        return array(
            'task'      => $task,
            'edit_form'   => $editForm->createView(),
        );
    }
}
