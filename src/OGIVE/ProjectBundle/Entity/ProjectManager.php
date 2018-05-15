<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectManager
 *
 * @ORM\Table(name="project_manager")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\ProjectManagerRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ProjectManager extends Contributor {

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
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Set project
     *
     * @param \OGIVE\ProjectBundle\Entity\Project $project
     *
     * @return ProjectManager
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

    /**
     * @ORM\PrePersist() 
     */
    public function prePersist() {
        parent::prePersist();
        $this->setContributorType("project-manager");
    }
    
}
