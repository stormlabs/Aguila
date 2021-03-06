<?php

namespace Storm\AguilaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Storm\AguilaBundle\Entity\Task
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Storm\AguilaBundle\Entity\TaskRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Task
{
    const CLOSE = 0;
    const OPEN = 1;
    const IN_PROGRESS = 2;
    const REOPENED = 3;

    static function getStatusChoices()
    {
        return array(
            self::CLOSE =>'task.status.0',
            self::OPEN=>'task.status.1',
            self::IN_PROGRESS =>'task.status.2',
            self::REOPENED =>'task.status.3',
        );
    }

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
     * @var ArrayCollection $comments
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="task")
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
        $this->comments = new ArrayCollection();
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
     * @param ArrayCollection $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * Get comments
     *
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function addComment(Comment $comment)
    {
        $comment->setTask($this);
        $this->comments->add($comment);
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
