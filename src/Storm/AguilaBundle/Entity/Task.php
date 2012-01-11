<?php

namespace Storm\AguilaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Storm\AguilaBundle\Entity\Task
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Storm\AguilaBundle\Entity\TaskRepository")
 */
class Task
{
    static $difficulty_choices = array('task.difficulty.0', 'task.difficulty.1', 'task.difficulty.2', 'task.difficulty.3');
    static $priority_choices = array('task.priority.0', 'task.priority.1', 'task.priority.2', 'task.priority.3');
    static $status_choices = array('task.status.0', 'task.priority.1', 'task.priority.2');

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var integer $difficulty
     *
     * @ORM\Column(name="difficulty", type="integer")
     */
    private $difficulty;

    /**
     * @var integer $priority
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    /**
     * @var integer $status
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var User $assignee
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $assignee;

    /**
     * @var User $reporter
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $reporter;

    /**
     * @var array $comments
     *
     * @ORM\Column(name="comments", type="array")
     */
    private $comments;

    /**
     * @var string $issues
     *
     * @ORM\Column(name="issues", type="string", length=255, nullable=true)
     */
    private $issues;

    /**
     * @var Feature $feature
     *
     * @ORM\ManyToOne(targetEntity="Feature", inversedBy="tasks")
     */
    private $feature;

    public function __construct()
    {
        $this->status = 0;
        $this->comments = array();
    }

    public function __toString()
    {
        return $this->description;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set difficulty
     *
     * @param integer $difficulty
     */
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;
    }

    /**
     * Get difficulty
     *
     * @return integer
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set assignee
     *
     * @param User $assignee
     */
    public function setAssignee(User $assignee)
    {
        $this->assignee = $assignee;
    }

    /**
     * Get assignee
     *
     * @return User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Set reporter
     *
     * @param User $reporter
     */
    public function setReporter(User $reporter)
    {
        $this->reporter = $reporter;
    }

    /**
     * Get reporter
     *
     * @return User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set comments
     *
     * @param array $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * Get comments
     *
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set issues
     *
     * @param string $issues
     */
    public function setIssues($issues)
    {
        $this->issues = $issues;
    }

    /**
     * Get issues
     *
     * @return string
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * Set feature
     *
     * @param Feature $feature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    }

    /**
     * Get feature
     *
     * @return Feature
     */
    public function getFeature()
    {
        return $this->feature;
    }
}
