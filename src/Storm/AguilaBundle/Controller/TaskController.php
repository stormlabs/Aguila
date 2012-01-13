<?php

namespace Storm\AguilaBundle\Controller;

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\Collection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Storm\AguilaBundle\Entity\Task;
use Storm\AguilaBundle\Entity\Project;
use Storm\AguilaBundle\Entity\Feature;
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
     * @ParamConverter("project", class="AguilaBundle:Project", options={"match" = {"project_slug"="slug"}})
     * @SecureParam(name="project", permissions="VIEW")
     */
    public function showAction(Project $project, $number)
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getEntityManager();

        try {
            $task = $em->getRepository('AguilaBundle:Task')->findOneByProject($project->getSlug(), $number);
        }
        catch (NoResultException $e) {
            throw $this->createNotFoundException($this->get('translator')->trans('task.not_found', array(), 'AguilaBundle'));
        }

        $commentForm = $this->createCommentForm($task);

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $commentForm->bindRequest($request);

            if ($commentForm->isValid()) {
                $user = $this->get('security.context')->getToken()->getUser();
                $data = $commentForm->getData();
                $commentList = $task->getComments();
                $commentList[] = array(
                    'user' => $user->getUserName(),
                    'body' => $data['body'],
                    'date' => new \Datetime('now'),
                );
                $task->setComments($commentList);
                $em->persist($task);
                $em->flush();

                $this->get('session')->setFlash('notice', 'comment.added');

                return $this->redirect($this->generateUrl('aguila_task_show', array(
                    'project_slug' => $project->getSlug(),
                    'number' => $number,
                )));
            }
        }

        return array(
            'task'                    => $task,
            'task_difficulty_choices' => Task::$difficulty_choices,
            'task_priority_choices'   => Task::$priority_choices,
            'task_status_choices'     => Task::$status_choices,
            'comment_form'            => $commentForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Task task.
     *
     * @Template()
     * @ParamConverter("project", class="AguilaBundle:Project", options={"match" = {"project_slug"="slug"}})
     * @ParamConverter("feature", class="AguilaBundle:Feature", options={"match" = {"feature_slug"="slug"}})
     * @SecureParam(name="project", permissions="EDIT")
     */
    public function newAction(Project $project, Feature $feature)
    {
        if ($feature->getProject() !== $project)  {
            throw new AccessDeniedException($this->getRequest()->getUri());
        }
        $task = new Task();
        $form   = $this->createForm(new TaskType(), $task);

        return array(
            'task' => $task,
            'form'   => $form->createView(),
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
     * @ParamConverter("project", class="AguilaBundle:Project", options={"match" = {"project_slug"="slug"}})
     * @ParamConverter("feature", class="AguilaBundle:Feature", options={"match" = {"feature_slug"="slug"}})
     * @SecureParam(name="project", permissions="EDIT")
     */
    public function createAction(Project $project, Feature $feature)
    {
        if ($feature->getProject() !== $project)  {
            throw new AccessDeniedException($this->getRequest()->getUri());
        }

        $task  = new Task();
        $request = $this->getRequest();
        $form    = $this->createForm(new TaskType(), $task);
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
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Task task.
     *
     * @Route("/{number}/edit", name="aguila_task_edit", requirements={"number" = "\d+"})
     * @Template()
     * @ParamConverter("project", class="AguilaBundle:Project", options={"match" = {"project_slug"="slug"}})
     * @SecureParam(name="project", permissions="EDIT")
     */
    public function editAction(Project $project, $number)
    {
        $em = $this->getDoctrine()->getEntityManager();

        try {
            $task = $em->getRepository('AguilaBundle:Task')->findOneByProject($project->getSlug(), $number);
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
     * @ParamConverter("project", class="AguilaBundle:Project", options={"match" = {"project_slug"="slug"}})
     * @SecureParam(name="project", permissions="EDIT")
     */
    public function updateAction(Project $project, $number)
    {
        $em = $this->getDoctrine()->getEntityManager();

        try {
            $task = $em->getRepository('AguilaBundle:Task')->findOneByProject($project->getSlug(), $number);
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

            return $this->redirect($this->generateUrl('aguila_task_show', array(
                'project_slug' => $project->getSlug(),
                'number' => $number,
            )));
        }

        return array(
            'task'      => $task,
            'edit_form'   => $editForm->createView(),
        );
    }

    /*
     * Creates a form for commentaries
     */
    protected function createCommentForm(Task $task)
    {
        $constraints = new Collection(array(
            'body' => new Constraints\NotBlank(),
        ));
        $commentForm = $this->container->get('form.factory')->createNamedBuilder(
            'form',
            'task_comment_form',
            null,
            array('validation_constraint' => $constraints,)
        )
        ->add('body', 'textarea')
        ->getForm();

        return $commentForm;
    }
}
