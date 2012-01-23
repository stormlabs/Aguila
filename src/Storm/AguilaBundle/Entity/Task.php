<?php

namespace Storm\AguilaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Storm\AguilaBundle\Entity\Task
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Storm\AguilaBundle\Entity\TaskRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Task
{
    static $difficulty_choices = array('task.difficulty.0', 'task.difficulty.1', 'task.difficulty.2', 'task.difficulty.3');
    static $priority_choices = array('task.priority.0', 'task.priority.1', 'task.priority.2', 'task.priority.3');
    static $status_choices = array('task.status.0', 'task.status.1', 'task.status.2');

    const CLOSE = 0;
    const OPEN = 1;
    const WORK = 2;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $number
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var text $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

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
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

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
        $this->status = self::OPEN;
        $this->created_at = new \DateTime();
        $this->comments = array();
    }

    public function __toString()
    {
        return $this->title;
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
     * Get number
     *
     * @param integer $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * Set number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * @param \DateTime $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
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
