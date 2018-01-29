<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\ProjectRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Project extends GeneralClass {

    /**
     * @var string
     *
     * @ORM\Column(name="numero_marche", type="string", length=255)
     */
    private $numeroMarche;
    
    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="text", nullable=true)
     */
    private $subject;
    
    /**
     * @var float
     *
     * @ORM\Column(name="project_cost", type="float", precision=10, scale=0, nullable=true)
     */
    private $projectCost;
    
    /**
     * @var string
     *
     * @ORM\Column(name="project_cost_currency", type="string", length=255, nullable=true, options={"default" : "XAF"})
     */
    private $projectCostCurrency;
    
    /**
     * @var string
     *
     * @ORM\Column(name="numero_lot", type="string", length=255, nullable=true)
     */
    private $numeroLot;
    
    /**
     * @var string
     *
     * @ORM\Column(name="lieu_execution", type="string", length=255, nullable=true)
     */
    private $lieuExecution;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=255, nullable=true)
     */
    private $region;
    
     /**
     * @var string
     *
     * @ORM\Column(name="departement", type="string", length=255, nullable=true)
     */
     private $departement;
     
     /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\Lot", mappedBy="project", cascade={"remove", "persist"})
     */
    private $lots;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\Task", mappedBy="project", cascade={"remove", "persist"})
     */
    private $tasks;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\Owner", mappedBy="project", cascade={"remove", "persist"})
     */
    private $owners;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\ProjectManager", mappedBy="project", cascade={"remove", "persist"})
     */
    private $projectManagers;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\Holder", mappedBy="project", cascade={"remove", "persist"})
     */
    private $holders;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\ServiceProvider", mappedBy="project", cascade={"remove", "persist"})
     */
    private $serviceProviders;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->lots = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set numeroMarche
     *
     * @param string $numeroMarche
     *
     * @return Project
     */
    public function setNumeroMarche($numeroMarche) {
        $this->numeroMarche = $numeroMarche;

        return $this;
    }

    /**
     * Get numeroMarche
     *
     * @return string
     */
    public function getNumeroMarche() {
        return $this->numeroMarche;
    }
    
    /**
     * Set projectCost
     *
     * @param float $projectCost
     *
     * @return Project
     */
    public function setProjectCost($projectCost) {
        $this->projectCost = $projectCost;

        return $this;
    }

    /**
     * Get projectCost
     *
     * @return float
     */
    public function getProjectCost() {
        return $this->projectCost;
    }
    
    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Project
     */
    public function setSubject($subject) {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }
    
    /**
     * Set projectCostCurrency
     *
     * @param string $projectCostCurrency
     *
     * @return Project
     */
    public function setProjectCostCurrency($projectCostCurrency) {
        $this->projectCostCurrency = $projectCostCurrency;

        return $this;
    }

    /**
     * Get projectCostCurrency
     *
     * @return string
     */
    public function getProjectCostCurrency() {
        return $this->projectCostCurrency;
    }
    
    /**
     * Set numeroLot
     *
     * @param string $numeroLot
     *
     * @return Project
     */
    public function setNumeroLot($numeroLot) {
        $this->numeroLot = $numeroLot;

        return $this;
    }

    /**
     * Get numeroLot
     *
     * @return string
     */
    public function getNumeroLot() {
        return $this->numeroLot;
    }
    
    /**
     * Set lieuExecution
     *
     * @param string $lieuExecution
     *
     * @return Project
     */
    public function setLieuExecution($lieuExecution) {
        $this->lieuExecution = $lieuExecution;

        return $this;
    }

    /**
     * Get lieuExecution
     *
     * @return string
     */
    public function getLieuExecution() {
        return $this->lieuExecution;
    }
    
    /**
     * Set region
     *
     * @param string $region
     *
     * @return Project
     */
    public function setRegion($region) {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion() {
        return $this->region;
    }
    
    /**
     * Set departement
     *
     * @param string $departement
     *
     * @return Project
     */
    public function setDepartement($departement) {
        $this->departement = $departement;

        return $this;
    }

    /**
     * Get departement
     *
     * @return string
     */
    public function getDepartement() {
        return $this->departement;
    }
    
    /**
     * Add lot
     *
     * @param \OGIVE\ProjectBundle\Entity\Lot $lot 
     * @return Project
     */
    public function addLot(\OGIVE\ProjectBundle\Entity\Lot $lot) {
        $this->lots[] = $lot;
        return $this;
    }

    /**
     * Get lots
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLots() {
        return $this->lots;
    }

    /**
     * Set lots
     *
     * @param \Doctrine\Common\Collections\Collection $lots
     * @return Project
     */
    public function setLots(\Doctrine\Common\Collections\Collection $lots = null) {
        $this->lots = $lots;

        return $this;
    }

    /**
     * Remove lot
     *
     * @param \OGIVE\ProjectBundle\Entity\Lot $lot
     * @return Project
     */
    public function removeLot(\OGIVE\ProjectBundle\Entity\Lot $lot) {
        $this->lots->removeElement($lot);
        return $this;
    }
    
    /**
     * Add task
     *
     * @param \OGIVE\ProjectBundle\Entity\Task $task 
     * @return Project
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
     * @return Project
     */
    public function setTasks(\Doctrine\Common\Collections\Collection $tasks = null) {
        $this->tasks = $tasks;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \OGIVE\ProjectBundle\Entity\Task $task
     * @return Project
     */
    public function removeTask(\OGIVE\ProjectBundle\Entity\Task $task) {
        $this->tasks->removeElement($task);
        return $this;
    }
    
    /**
     * Add owner
     *
     * @param \OGIVE\ProjectBundle\Entity\Owner $owner 
     * @return Project
     */
    public function addOwner(\OGIVE\ProjectBundle\Entity\Owner $owner) {
        $this->owners[] = $owner;
        return $this;
    }

    /**
     * Get owners
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwners() {
        return $this->owners;
    }

    /**
     * Set owners
     *
     * @param \Doctrine\Common\Collections\Collection $owners
     * @return Project
     */
    public function setOwners(\Doctrine\Common\Collections\Collection $owners = null) {
        $this->owners = $owners;

        return $this;
    }

    /**
     * Remove owner
     *
     * @param \OGIVE\ProjectBundle\Entity\Owner $owner
     * @return Project
     */
    public function removeOwner(\OGIVE\ProjectBundle\Entity\Owner $owner) {
        $this->owners->removeElement($owner);
        return $this;
    }
    
    /**
     * Add projectManager
     *
     * @param \OGIVE\ProjectBundle\Entity\ProjectManager $projectManager 
     * @return Project
     */
    public function addProjectManager(\OGIVE\ProjectBundle\Entity\ProjectManager $projectManager) {
        $this->projectManagers[] = $projectManager;
        return $this;
    }

    /**
     * Get projectManagers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjectManagers() {
        return $this->projectManagers;
    }

    /**
     * Set projectManagers
     *
     * @param \Doctrine\Common\Collections\Collection $projectManagers
     * @return Project
     */
    public function setProjectManagers(\Doctrine\Common\Collections\Collection $projectManagers = null) {
        $this->projectManagers = $projectManagers;

        return $this;
    }

    /**
     * Remove projectManager
     *
     * @param \OGIVE\ProjectBundle\Entity\ProjectManager $projectManager
     * @return Project
     */
    public function removeProjectManager(\OGIVE\ProjectBundle\Entity\ProjectManager $projectManager) {
        $this->projectManagers->removeElement($projectManager);
        return $this;
    }
    
    /**
     * Add holder
     *
     * @param \OGIVE\ProjectBundle\Entity\Holder $holder 
     * @return Project
     */
    public function addHolder(\OGIVE\ProjectBundle\Entity\Holder $holder) {
        $this->holders[] = $holder;
        return $this;
    }

    /**
     * Get holders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHolders() {
        return $this->holders;
    }

    /**
     * Set holders
     *
     * @param \Doctrine\Common\Collections\Collection $holders
     * @return Project
     */
    public function setHolders(\Doctrine\Common\Collections\Collection $holders = null) {
        $this->holders = $holders;

        return $this;
    }

    /**
     * Remove holder
     *
     * @param \OGIVE\ProjectBundle\Entity\Holder $holder
     * @return Project
     */
    public function removeHolder(\OGIVE\ProjectBundle\Entity\Holder $holder) {
        $this->holders->removeElement($holder);
        return $this;
    }
    
    /**
     * Add serviceProvider
     *
     * @param \OGIVE\ProjectBundle\Entity\ServiceProvider $serviceProvider 
     * @return Project
     */
    public function addServiceProvider(\OGIVE\ProjectBundle\Entity\ServiceProvider $serviceProvider) {
        $this->serviceProviders[] = $serviceProvider;
        return $this;
    }

    /**
     * Get serviceProviders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServiceProviders() {
        return $this->serviceProviders;
    }

    /**
     * Set serviceProviders
     *
     * @param \Doctrine\Common\Collections\Collection $serviceProviders
     * @return Project
     */
    public function setServiceProviders(\Doctrine\Common\Collections\Collection $serviceProviders = null) {
        $this->serviceProviders = $serviceProviders;

        return $this;
    }

    /**
     * Remove serviceProvider
     *
     * @param \OGIVE\ProjectBundle\Entity\ServiceProvider $serviceProvider
     * @return Project
     */
    public function removeServiceProvider(\OGIVE\ProjectBundle\Entity\ServiceProvider $serviceProvider) {
        $this->serviceProviders->removeElement($serviceProvider);
        return $this;
    }

    public function setSearchData() {
        $this->searchData = $this->getSubject()." ".$this->getNumeroMarche()." ".$this->getProjectCost()." ".$this->getLieuExecution()." ".$this->getRegion()." ".$this->getDepartement();
    }

}
