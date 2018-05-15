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
     * @var integer
     *
     * @ORM\Column(name="delais", type="integer", nullable=true)
     */
    private $delais;

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
     * @var double
     *
     * @ORM\Column(name="project_cost", type="decimal", precision=20, scale=0, nullable=true)
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
     * @ORM\Column(name="annee_budgetaire", type="string", length=255)
     */
    private $anneeBudgetaire;
    
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
     * @var \DateTime
     *
     * @ORM\Column(name="suscription_date", type="datetime", nullable=true)
     */
    private $suscriptionDate;
    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="signature_date", type="datetime", nullable=true)
     */
    private $signatureDate;
    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="notification_date", type="datetime", nullable=true)
     */
    private $notificationDate;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_avenant", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtAvenant;
    
    /**
     * @var float
     *
     * @ORM\Column(name="avance_demarrage", type="float", precision=20, scale=0, nullable=true)
     */
    private $avanceDemarrage;
    
    /**
     * @var float
     *
     * @ORM\Column(name="payment_state_avance_demarrage", type="float", precision=20, scale=0, nullable=true)
     */
    private $paymentStateAvanceDemarrage;
     
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\Task", mappedBy="project", cascade={"remove", "persist"})
     */
    private $tasks;
    
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\OtherContributor", mappedBy="project", cascade={"remove", "persist"})
     */
    private $otherContributors;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\Decompte", mappedBy="project", cascade={"remove", "persist"})
     */
    private $decomptes;
    
    /**
     * @var \DecompteTotal 
     * @ORM\OneToOne(targetEntity="DecompteTotal",cascade={"persist"})
     * @ORM\JoinColumn(name="decompte_total", referencedColumnName="id")
     */
    private $decompteTotal;
    
    /**
     * @var \OGIVE\ProjectBundle\Entity\Owner
     * @ORM\OneToOne(targetEntity="\OGIVE\ProjectBundle\Entity\Owner",cascade={"persist"})
     * @ORM\JoinColumn(name="owner", referencedColumnName="id")
     */
    private $owner;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->projectManagers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->owners = new \Doctrine\Common\Collections\ArrayCollection();
        $this->serviceProviders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->holders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->otherContributors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->decomptes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set delais
     *
     * @param integer $delais
     *
     * @return Project
     */
    public function setDelais($delais) {
        $this->delais = $delais;

        return $this;
    }

    /**
     * Get delais
     *
     * @return integer
     */
    public function getDelais() {
        return $this->delais;
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
     * Set anneeBudgetaire
     *
     * @param string $anneeBudgetaire
     *
     * @return Project
     */
    public function setAnneeBudgetaire($anneeBudgetaire) {
        $this->anneeBudgetaire = $anneeBudgetaire;

        return $this;
    }

    /**
     * Get anneeBudgetaire
     *
     * @return string
     */
    public function getAnneeBudgetaire() {
        return $this->anneeBudgetaire;
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
     * Set avanceDemarrage
     *
     * @param float $avanceDemarrage
     *
     * @return Project
     */
    public function setAvanceDemarrage($avanceDemarrage) {
        $this->avanceDemarrage = $avanceDemarrage;

        return $this;
    }

    /**
     * Get avanceDemarrage
     *
     * @return float
     */
    public function getAvanceDemarrage() {
        return $this->avanceDemarrage;
    }
    
    /**
     * Set paymentStateAvanceDemarrage
     *
     * @param float $paymentStateAvanceDemarrage
     *
     * @return Project
     */
    public function setPaymentStateAvanceDemarrage($paymentStateAvanceDemarrage) {
        $this->paymentStateAvanceDemarrage = $paymentStateAvanceDemarrage;

        return $this;
    }

    /**
     * Get paymentStateAvanceDemarrage
     *
     * @return float
     */
    public function getPaymentStateAvanceDemarrage() {
        return $this->paymentStateAvanceDemarrage;
    }
    
    /**
     * Set suscriptionDate
     *
     * @param \DateTime $suscriptionDate
     *
     * @return Project
     */
    public function setSuscriptionDate($suscriptionDate) {
        $this->suscriptionDate = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $suscriptionDate))));

        return $this;
    }

    /**
     * Get suscriptionDate
     *
     * @return \DateTime
     */
    public function getSuscriptionDate() {
        return $this->suscriptionDate ? $this->suscriptionDate->format('d-m-Y'): $this->suscriptionDate;
        
    }
    
    /**
     * Set signatureDate
     *
     * @param \DateTime $signatureDate
     *
     * @return Project
     */
    public function setSignatureDate($signatureDate) {
        $this->signatureDate = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $signatureDate))));

        return $this;
    }

    /**
     * Get signatureDate
     *
     * @return \DateTime
     */
    public function getSignatureDate() {
        return $this->signatureDate ? $this->signatureDate->format('d-m-Y'): $this->signatureDate;
        
    }
    
    /**
     * Set notificationDate
     *
     * @param \DateTime $notificationDate
     *
     * @return Project
     */
    public function setNotificationDate($notificationDate) {
        $this->notificationDate = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $notificationDate))));

        return $this;
    }

    /**
     * Get notificationDate
     *
     * @return \DateTime
     */
    public function getNotificationDate() {
        return $this->notificationDate ? $this->notificationDate->format('d-m-Y'): $this->notificationDate;
        
    }
    
    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Project
     */
    public function setStartDate($startDate) {
        $this->startDate = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $startDate))));

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate() {
        return $this->startDate ? $this->startDate->format('d-m-Y'): $this->startDate;
        
    }
    
    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Project
     */
    public function setEndDate($endDate) {
        $this->endDate = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $endDate))));

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate() {
        return $this->endDate ? $this->endDate->format('d-m-Y'): $this->endDate;
        
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
    
    /**
     * Add decompte
     *
     * @param \OGIVE\ProjectBundle\Entity\Decompte $decompte 
     * @return Project
     */
    public function addDecompte(\OGIVE\ProjectBundle\Entity\Decompte $decompte) {
        $this->decomptes[] = $decompte;
        return $this;
    }

    /**
     * Get decomptes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDecomptes() {
        return $this->decomptes;
    }

    /**
     * Set decompte
     *
     * @param \Doctrine\Common\Collections\Collection $decomptes
     * @return Project
     */
    public function setDecomptes(\Doctrine\Common\Collections\Collection $decomptes = null) {
        $this->decomptes = $decomptes;

        return $this;
    }

    /**
     * Remove decompte
     *
     * @param \OGIVE\ProjectBundle\Entity\Decompte $decompte
     * @return Project
     */
    public function removeDecompte(\OGIVE\ProjectBundle\Entity\Decompte $decompte) {
        $this->decomptes->removeElement($decompte);
        return $this;
    }
    
    /**
     * Add otherContributor
     *
     * @param \OGIVE\ProjectBundle\Entity\OtherContributor $otherContributor 
     * @return Project
     */
    public function addOtherContributor(\OGIVE\ProjectBundle\Entity\OtherContributor $otherContributor) {
        $this->otherContributors[] = $otherContributor;
        return $this;
    }

    /**
     * Get otherContributors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOtherContributors() {
        return $this->otherContributors;
    }

    /**
     * Set otherContributors
     *
     * @param \Doctrine\Common\Collections\Collection $otherContributors
     * @return Project
     */
    public function setOtherContributors(\Doctrine\Common\Collections\Collection $otherContributors = null) {
        $this->otherContributors = $otherContributors;

        return $this;
    }

    /**
     * Remove otherContributor
     *
     * @param \OGIVE\ProjectBundle\Entity\OtherContributor $otherContributor
     * @return Project
     */
    public function removeOtherContributor(\OGIVE\ProjectBundle\Entity\OtherContributor $otherContributor) {
        $this->otherContributors->removeElement($otherContributor);
        return $this;
    }
    
    /**
     * Set mtAvenant
     *
     * @param float $mtAvenant
     *
     * @return Project
     */
    public function setMtAvenant($mtAvenant) {
        $this->mtAvenant = $mtAvenant;

        return $this;
    }

    /**
     * Get mtAvenant
     *
     * @return float
     */
    public function getMtAvenant() {
        return $this->mtAvenant;
    }
    
    /**
     * Set decompteTotal
     *
     * @param \OGIVE\ProjectBundle\Entity\DecompteTotal $decompteTotal
     *
     * @return Project
     */
    public function setDecompteTotal(\OGIVE\ProjectBundle\Entity\Decompte $decompteTotal=null) {
        $this->decompteTotal = $decompteTotal;

        return $this;
    }

    /**
     * Get decompteTotal
     *
     * @return \OGIVE\ProjectBundle\Entity\DecompteTotal
     */
    public function getDecompteTotal() {
        return $this->decompteTotal;
    }
    
    /**
     * Set owner
     *
     * @param \OGIVE\ProjectBundle\Entity\Owner $owner
     *
     * @return Project
     */
    public function setOwner(\OGIVE\ProjectBundle\Entity\Owner $owner=null) {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \OGIVE\ProjectBundle\Entity\Owner
     */
    public function getOwner() {
        return $this->owner;
    }

    public function setSearchData() {
        $this->searchData = $this->getSubject()." ".$this->getNumeroMarche()." ".$this->getProjectCost()." ".$this->getLieuExecution()." ".$this->getRegion()." ".$this->getDepartement();
    }

}
