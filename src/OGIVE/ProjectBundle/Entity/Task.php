<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\TaskRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Task extends GeneralClass {

     /**
     * @var string
     *
     * @ORM\Column(name="nom", type="text", nullable=true)
     */
    private $nom;
    
    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", nullable=true)
     */
    private $numero;
    
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
     * @var string
     *
     * @ORM\Column(name="unite", type="string", length=255, nullable=true)
     */
    private $unite;
    
    
    /**
     * @var float
     *
     * @ORM\Column(name="prix_unitaire", type="float", precision=20, scale=0, nullable=true)
     */
    private $prixUnitaire;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_prevue_marche", type="float", precision=20, scale=0, nullable=true)
     */
    protected $qtePrevueMarche;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_prevue_projet_exec", type="float", precision=20, scale=0, nullable=true)
     */
    protected $qtePrevueProjetExec;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_cumul_mois_prec", type="float", precision=20, scale=0, nullable=true)
     */
    protected $qteCumulMoisPrec;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_mois", type="float", precision=20, scale=0, nullable=true)
     */
    protected $qteMois;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_cumul_mois", type="float", precision=20, scale=0, nullable=true)
     */
    protected $qteCumulMois;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_prevue_marche", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPrevueMarche;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_prevue_projet_exec", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPrevueProjetExec;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_cumul_mois_prec", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtCumulMoisPrec;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_mois", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtMois;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_cumul_mois", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtCumulMois;
    
    /**
     * @var float
     *
     * @ORM\Column(name="pourcent_realisation", type="float", precision=20, scale=0, nullable=true)
     */
    private $pourcentRealisation;    
    
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
     * @var \Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_task", referencedColumnName="id")
     * })
     */
    private $projectTask;
    
    /**
     * @var \Task
     *
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_task", referencedColumnName="id")
     * })
     */
    private $parentTask;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\Task", mappedBy="parentTask", cascade={"remove", "persist"})
     */
    private $subTasks;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\DecompteTask", mappedBy="task", cascade={"remove", "persist"})
     */
    private $decompteTasks;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->subTasks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->decompteTasks = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Task
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
     * Set numero
     *
     * @param string $numero
     *
     * @return Task
     */
    public function setNumero($numero) {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero() {
        return $this->numero;
    }
    
    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Task
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
     * @return Task
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
     * Set description
     *
     * @param string $description
     *
     * @return Task
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
     * Set unite
     *
     * @param string $unite
     *
     * @return Task
     */
    public function setUnite($unite) {
        $this->unite = $unite;

        return $this;
    }

    /**
     * Get unite
     *
     * @return string
     */
    public function getUnite() {
        return $this->unite;
    }
    
    /**
     * Set prixUnitaire
     *
     * @param float $prixUnitaire
     *
     * @return Task
     */
    public function setPrixUnitaire($prixUnitaire) {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    /**
     * Get prixUnitaire
     *
     * @return float
     */
    public function getPrixUnitaire() {
        return $this->prixUnitaire;
    }
    
    /**
     * Set mtPrevueMarche
     *
     * @param float $mtPrevueMarche
     *
     * @return Task
     */
    public function setMtPrevueMarche($mtPrevueMarche) {
        $this->mtPrevueMarche = $mtPrevueMarche;

        return $this;
    }

    /**
     * Get mtPrevueMarche
     *
     * @return float
     */
    public function getMtPrevueMarche() {
        return $this->mtPrevueMarche;
    }
    
    /**
     * Set qtePrevueMarche
     *
     * @param float $qtePrevueMarche
     *
     * @return Task
     */
    public function setQtePrevueMarche($qtePrevueMarche) {
        $this->qtePrevueMarche = $qtePrevueMarche;

        return $this;
    }

    /**
     * Get qtePrevueMarche
     *
     * @return float
     */
    public function getQtePrevueMarche() {
        return $this->qtePrevueMarche;
    }
    
    /**
     * Set mtPrevueProjetExec
     *
     * @param float $mtPrevueProjetExec
     *
     * @return Task
     */
    public function setMtPrevueProjetExec($mtPrevueProjetExec) {
        $this->mtPrevueProjetExec = $mtPrevueProjetExec;

        return $this;
    }

    /**
     * Get mtPrevueProjetExec
     *
     * @return float
     */
    public function getMtPrevueProjetExec() {
        return $this->mtPrevueProjetExec;
    }
    
    /**
     * Set qtePrevueProjetExec
     *
     * @param float $qtePrevueProjetExec
     *
     * @return Task
     */
    public function setQtePrevueProjetExec($qtePrevueProjetExec) {
        $this->qtePrevueProjetExec = $qtePrevueProjetExec;

        return $this;
    }

    /**
     * Get qtePrevueProjetExec
     *
     * @return float
     */
    public function getQtePrevueProjetExec() {
        return $this->qtePrevueProjetExec;
    }
    
    
    /**
     * Set mtCumulMoisPrec
     *
     * @param float $mtCumulMoisPrec
     *
     * @return Task
     */
    public function setMtCumulMoisPrec($mtCumulMoisPrec) {
        $this->mtCumulMoisPrec = $mtCumulMoisPrec;

        return $this;
    }

    /**
     * Get mtCumulMoisPrec
     *
     * @return float
     */
    public function getMtCumulMoisPrec() {
        return $this->mtCumulMoisPrec;
    }
    
    /**
     * Set qteCumulMoisPrec
     *
     * @param float $qteCumulMoisPrec
     *
     * @return Task
     */
    public function setQteCumulMoisPrec($qteCumulMoisPrec) {
        $this->qteCumulMoisPrec = $qteCumulMoisPrec;

        return $this;
    }

    /**
     * Get qteCumulMoisPrec
     *
     * @return float
     */
    public function getQteCumulMoisPrec() {
        return $this->qteCumulMoisPrec;
    }
    
    /**
     * Set mtMois
     *
     * @param float $mtMois
     *
     * @return Task
     */
    public function setMtMois($mtMois) {
        $this->mtMois = $mtMois;

        return $this;
    }

    /**
     * Get mtMois
     *
     * @return float
     */
    public function getMtMois() {
        return $this->mtMois;
    }
    
    /**
     * Set qteMois
     *
     * @param float $qteMois
     *
     * @return Task
     */
    public function setQteMois($qteMois) {
        $this->qteMois = $qteMois;

        return $this;
    }

    /**
     * Get qteMois
     *
     * @return float
     */
    public function getQteMois() {
        return $this->qteMois;
    }
    
    /**
     * Set mtCumulMois
     *
     * @param float $mtCumulMois
     *
     * @return Task
     */
    public function setMtCumulMois($mtCumulMois) {
        $this->mtCumulMois = $mtCumulMois;

        return $this;
    }

    /**
     * Get mtCumulMois
     *
     * @return float
     */
    public function getMtCumulMois() {
        return $this->mtCumulMois;
    }
    
    /**
     * Set qteCumulMois
     *
     * @param float $qteCumulMois
     *
     * @return Task
     */
    public function setQteCumulMois($qteCumulMois) {
        $this->qteCumulMois = $qteCumulMois;

        return $this;
    }

    /**
     * Get qteCumulMois
     *
     * @return float
     */
    public function getQteCumulMois() {
        return $this->qteCumulMois;
    }
    
    /**
     * Set pourcentRealisation
     *
     * @param float $pourcentRealisation
     *
     * @return Task
     */
    public function setPourcentRealisation($pourcentRealisation) {
        $this->pourcentRealisation = $pourcentRealisation;

        return $this;
    }

    /**
     * Get pourcentRealisation
     *
     * @return float
     */
    public function getPourcentRealisation() {
        return $this->pourcentRealisation;
    }
    
    
    /**
     * Set project
     *
     * @param \OGIVE\ProjectBundle\Entity\Project $project
     *
     * @return Task
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
     * Set projectTask
     *
     * @param \OGIVE\ProjectBundle\Entity\Project $projectTask
     *
     * @return Task
     */
    public function setProjectTask(\OGIVE\ProjectBundle\Entity\Project $projectTask=null) {
        $this->projectTask = $projectTask;

        return $this;
    }

    /**
     * Get projectTask
     *
     * @return \OGIVE\ProjectBundle\Entity\Project
     */
    public function getProjectTask() {
        return $this->projectTask;
    }
    
    /**
     * Add decompteTask
     *
     * @param \OGIVE\ProjectBundle\Entity\DecompteTask $decompteTask 
     * @return Task
     */
    public function addDecompteTask(\OGIVE\ProjectBundle\Entity\DecompteTask $decompteTask) {
        $this->decompteTasks[] = $decompteTask;
        return $this;
    }

    /**
     * Get decompteTasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDecompteTasks() {
        return $this->decompteTasks;
    }

    /**
     * Set decompteTasks
     *
     * @param \Doctrine\Common\Collections\Collection $decompteTasks
     * @return Task
     */
    public function setDecompteTasks(\Doctrine\Common\Collections\Collection $decompteTasks = null) {
        $this->decompteTasks = $decompteTasks;

        return $this;
    }

    /**
     * Remove decompteTask
     *
     * @param \OGIVE\ProjectBundle\Entity\DecompteTask $decompteTask
     * @return Task
     */
    public function removeDecompteTask(\OGIVE\ProjectBundle\Entity\DecompteTask $decompteTask) {
        $this->decompteTasks->removeElement($decompteTask);
        return $this;
    }
   
    
    /**
     * Set parentTask
     *
     * @param \OGIVE\ProjectBundle\Entity\Task $parentTask
     *
     * @return Task
     */
    public function setParentTask(\OGIVE\ProjectBundle\Entity\Task $parentTask=null) {
        $this->parentTask = $parentTask;

        return $this;
    }

    /**
     * Get parentTask
     *
     * @return \OGIVE\ProjectBundle\Entity\Task
     */
    public function getParentTask() {
        return $this->parentTask;
    }
    
    
    /**
     * Add subTask
     *
     * @param \OGIVE\ProjectBundle\Entity\Task $subTask 
     * @return Task
     */
    public function addSubTask(\OGIVE\ProjectBundle\Entity\Task $subTask) {
        $this->subTasks[] = $subTask;
        return $this;
    }

    /**
     * Get subTasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubTasks() {
        return $this->subTasks;
    }

    /**
     * Set subTasks
     *
     * @param \Doctrine\Common\Collections\Collection $subTasks
     * @return Task
     */
    public function setSubTasks(\Doctrine\Common\Collections\Collection $subTasks = null) {
        $this->subTasks = $subTasks;

        return $this;
    }

    /**
     * Remove subTask
     *
     * @param \OGIVE\ProjectBundle\Entity\Task $subTask
     * @return Task
     */
    public function removeSubTask(\OGIVE\ProjectBundle\Entity\Task $subTask) {
        $this->subTasks->removeElement($subTask);
        return $this;
    }

    public function setSearchData() {
        $this->searchData = $this->getUnite()." ".$this->getNom()." ".$this->getDescription();
    }

}
