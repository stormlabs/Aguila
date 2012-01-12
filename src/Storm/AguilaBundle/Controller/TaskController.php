<?php

namespace Storm\AguilaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Storm\AguilaBundle\Entity\Task;
use Storm\AguilaBundle\Form\TaskType;
use Doctrine\ORM\NoResultException;

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
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $tasks = $em->getRepository('AguilaBundle:Task')->findAll();

        return array('tasks' => $tasks);
    }

    /**
     * Finds and displays a Task task.
     *
     * @Route("/{number}", name="aguila_task_show", requirements={"number" = "\d+"})
     * @Template()
     */
    public function showAction($project_slug, $number)
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getEntityManager();

        try {
            $task = $em->getRepository('AguilaBundle:Task')->findOneByProject($project_slug, $number);
        }
        catch (NoResultException $e) {
            throw $this->createNotFoundException($this->get('translator')->trans('task.not_found', array(), 'AguilaBundle'));
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
     * @Template()
     */
    public function newAction($project_slug, $feature_slug)
    {
        $task = new Task();
        $form   = $this->createForm(new TaskType(), $task);

        return array(
            'task' => $task,
            'form'   => $form->createView(),
            'project_slug' => $project_slug,
            'feature_slug' => $feature_slug,
        );
    }

    /**
     * Creates a new Task task.
     *
     * @Route("/create/{feature_slug}", name="aguila_task_create")
     * @Method("post")
     * @Template("AguilaBundle:Task:new.html.twig")
     */
    public function createAction($project_slug, $feature_slug)
    {
        $task  = new Task();
        $request = $this->getRequest();
        $form    = $this->createForm(new TaskType(), $task);
        $form->bindRequest($request);

        if ($form->isValid()) {
            /** @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getDoctrine()->getEntityManager();
            $feature = $em->getRepository('AguilaBundle:Feature')->findOneBy(array('slug' => $feature_slug));
            $task->setFeature($feature);

            $project = $feature->getProject();

            $task->setNumber($number = $project->getTaskCounter());
            $project->setTaskCounter(++$number);

            $user = $this->get('security.context')->getToken()->getUser();
            $task->setReporter($user);
            $em->persist($task);
            $em->persist($project);
            $em->flush();

            return $this->redirect($this->generateUrl('task_show', array(
                'project_slug' => $project_slug,
                'number' => $task->getNumber(),
            )));
            
        }

        return array(
            'task' => $task,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Task task.
     *
     * @Route("/{number}/edit", name="aguila_task_edit", requirements={"number" = "\d+"})
     * @Template()
     */
    public function editAction($project_slug, $number)
    {
        $em = $this->getDoctrine()->getEntityManager();

        try {
            $task = $em->getRepository('AguilaBundle:Task')->findOneByProject($project_slug, $number);
        }
        catch (NoResultException $e) {
            throw $this->createNotFoundException($this->get('translator')->trans('task.not_found', array(), 'AguilaBundle'));
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
     * @Route("/{number}/update", name="aguila_task_update", requirements={"number" = "\d+"})
     * @Method("post")
     * @Template("AguilaBundle:Task:edit.html.twig")
     */
    public function updateAction($project_slug, $number)
    {
        $em = $this->getDoctrine()->getEntityManager();

        try {
            $task = $em->getRepository('AguilaBundle:Task')->findOneByProject($project_slug, $number);
        }
        catch (NoResultException $e) {
            throw $this->createNotFoundException($this->get('translator')->trans('task.not_found', array(), 'AguilaBundle'));
        }

        $editForm   = $this->createForm(new TaskType(), $task);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($task);
            $em->flush();

            return $this->redirect($this->generateUrl('task_show', array(
                'project_slug' => $project_slug,
                'number' => $number,
            )));
        }

        return array(
            'task'      => $task,
            'edit_form'   => $editForm->createView(),
        );
    }
}
