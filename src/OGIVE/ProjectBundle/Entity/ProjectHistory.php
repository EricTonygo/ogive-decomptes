<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectHistory
 *
 * @ORM\Table(name="project_history")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\ProjectHistory")
 * @ORM\HasLifecycleCallbacks
 */
class ProjectHistory extends GeneralClass {
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;
    
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
     * @return ProjectHistory
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
        $this->searchData = $this->getN()." ".$this->getRemboursementAvancePercent();
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProjectHistory
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->projectPercent;
    }
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return ProjectHistory
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
    public function getRemboursementAvancePercent() {
        return $this->remboursementAvancePercent;
        
    }
    
}
