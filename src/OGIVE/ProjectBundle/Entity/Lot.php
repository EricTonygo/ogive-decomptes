<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lot
 *
 * @ORM\Table(name="lot")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\LotRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Lot extends GeneralClass {
    
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="text", nullable=true)
     */
    private $nom;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var \Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project", referencedColumnName="id")
     * })
     */
    private $project;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\Task", mappedBy="lot", cascade={"remove", "persist"})
     */
    private $tasks;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Lot
     */
    public function setNom($nom) {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom() {
        return $this->nom;
    }
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return Lot
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }
    
    /**
     * Add task
     *
     * @param \OGIVE\ProjectBundle\Entity\Task $task 
     * @return Lot
     */
    public function addTask(\OGIVE\ProjectBundle\Entity\Task $task) {
        $this->tasks[] = $task;
        return $this;
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks() {
        return $this->tasks;
    }

    /**
     * Set tasks
     *
     * @param \Doctrine\Common\Collections\Collection $tasks
     * @return Lot
     */
    public function setTasks(\Doctrine\Common\Collections\Collection $tasks = null) {
        $this->tasks = $tasks;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \OGIVE\ProjectBundle\Entity\Task $task
     * @return Lot
     */
    public function removeTask(\OGIVE\ProjectBundle\Entity\Task $task) {
        $this->tasks->removeElement($task);
        return $this;
    }
    
    
    /**
     * Set project
     *
     * @param \OGIVE\ProjectBundle\Entity\Project $project
     *
     * @return Lot
     */
    public function setProject(\OGIVE\ProjectBundle\Entity\Project $project=null) {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \OGIVE\ProjectBundle\Entity\Project
     */
    public function getProject() {
        return $this->project;
    }
    
    public function setSearchData() {
        $this->searchData = $this->getNom()." ".$this->getDescription();
    }
}
