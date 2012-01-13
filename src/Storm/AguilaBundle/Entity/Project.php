<?php

namespace Storm\AguilaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Storm\AguilaBundle\Entity\Project
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Project
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
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $slug
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var ArrayCollection $features
     *
     * @ORM\OneToMany(targetEntity="Feature", mappedBy="project")
     */
    private $features;

    /**
     * @var integer $taskCounter
     *
     * @ORM\Column(name="task_counter", type="integer")
     */
    private $taskCounter;

    public function __construct()
    {
        $this->features = new ArrayCollection();
        $this->taskCounter = 1;
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
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set features
     *
     * @param ArrayCollection $features
     */
    public function setFeatures($features)
    {
        $this->features = $features;
    }

    /**
     * Get features
     *
     * @return ArrayCollection
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * Set task counter
     *
     * @param integer $taskCounter
     */
    public function setTaskCounter($taskCounter)
    {
        $this->taskCounter = $taskCounter;
    }

    /**
     * Get task counter
     *
     * @return integer
     */
    public function getTaskCounter()
    {
        return $this->taskCounter;
    }
}