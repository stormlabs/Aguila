<?php

namespace Storm\AguilaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Storm\AguilaBundle\Entity\Feature
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Storm\AguilaBundle\Entity\FeatureRepository")
 */
class Feature
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var array $dependencies
     *
     * @ORM\Column(name="dependencies", type="array")
     */
    private $dependencies;

    /**
     * @var Project $project
     * 
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="features")
     */
    private $project;
    
    /**
     * @var ArrayCollection $tasks
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="feature")
     */
    private $tasks;
    
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
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
     * Set dependencies
     *
     * @param array $dependencies
     */
    public function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Get dependencies
     *
     * @return array 
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Set project
     *
     * @param Project $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * Get project
     *
     * @return Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set tasks
     *
     * @param ArrayCollection $tasks
     */
    public function setTasks($tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * Get tasks
     *
     * @return ArrayCollection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }
}