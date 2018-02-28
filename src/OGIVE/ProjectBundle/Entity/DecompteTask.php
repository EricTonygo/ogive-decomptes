<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DecompteTask
 *
 * @ORM\Table(name="decompte_task")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\DecompteTaskRepository")
 * @ORM\HasLifecycleCallbacks
 */
class DecompteTask extends GeneralClass {

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
     * @ORM\Column(name="prix_unitaire", type="float", precision=10, scale=0, nullable=true)
     */
    private $prixUnitaire;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_prevue_marche", type="float", precision=10, scale=0, nullable=true)
     */
    protected $qtePrevueMarche;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_prevue_projet_exec", type="float", precision=10, scale=0, nullable=true)
     */
    protected $qtePrevueProjetExec;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_cumul_mois_prec", type="float", precision=10, scale=0, nullable=true)
     */
    protected $qteCumulMoisPrec;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_mois", type="float", precision=10, scale=0, nullable=true)
     */
    protected $qteMois;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_cumul_mois", type="float", precision=10, scale=0, nullable=true)
     */
    protected $qteCumulMois;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_prevue_marche", type="float", precision=10, scale=0, nullable=true)
     */
    private $mtPrevueMarche;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_prevue_projet_exec", type="float", precision=10, scale=0, nullable=true)
     */
    private $mtPrevueProjetExec;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_cumul_mois_prec", type="float", precision=10, scale=0, nullable=true)
     */
    private $mtCumulMoisPrec;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_mois", type="float", precision=10, scale=0, nullable=true)
     */
    private $mtMois;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_cumul_mois", type="float", precision=10, scale=0, nullable=true)
     */
    private $mtCumulMois;
    
    /**
     * @var float
     *
     * @ORM\Column(name="pourcent_realisation", type="float", precision=10, scale=0, nullable=true)
     */
    private $pourcentRealisation;    
    
    /**
     * @var \Task
     *
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="task", referencedColumnName="id")
     * })
     */
    private $task;
    
    /**
     * @var \Decompte
     *
     * @ORM\ManyToOne(targetEntity="Decompte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="decompte", referencedColumnName="id")
     * })
     */
    private $decompte;
    
    /**
     * @var \Decompte
     *
     * @ORM\ManyToOne(targetEntity="Decompte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="my_decompte", referencedColumnName="id")
     * })
     */
    private $myDecompte;
    
    /**
     * @var \DecompteTask
     *
     * @ORM\ManyToOne(targetEntity="DecompteTask")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_decompte_task", referencedColumnName="id")
     * })
     */
    private $parentDecompteTask;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\DecompteTask", mappedBy="parentDecompteTask", cascade={"remove", "persist"})
    */
    private $subDecompteTasks;
    
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
    }
    
    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
     */
    public function setEndDate($endDate) {
        $this->endDate = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $endDate))));

        return $this;
    }

    /**
     * Get startDate
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * @return DecompteTask
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
     * Set task
     *
     * @param \OGIVE\ProjectBundle\Entity\Task $task
     *
     * @return DecompteTask
     */
    public function setTask(\OGIVE\ProjectBundle\Entity\Task $task=null) {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \OGIVE\ProjectBundle\Entity\Task
     */
    public function getTask() {
        return $this->task;
    }
    
    /**
     * Set decompte
     *
     * @param \OGIVE\ProjectBundle\Entity\Decompte $decompte
     *
     * @return DecompteTask
     */
    public function setDecompte(\OGIVE\ProjectBundle\Entity\Decompte $decompte=null) {
        $this->decompte = $decompte;

        return $this;
    }

    /**
     * Get decompte
     *
     * @return \OGIVE\ProjectBundle\Entity\Decompte
     */
    public function getDecompte() {
        return $this->decompte;
    }
    
    /**
     * Set myDecompte
     *
     * @param \OGIVE\ProjectBundle\Entity\Decompte $myDecompte
     *
     * @return DecompteTask
     */
    public function setMyDecompte(\OGIVE\ProjectBundle\Entity\Decompte $myDecompte=null) {
        $this->myDecompte = $myDecompte;

        return $this;
    }

    /**
     * Get myDecompte
     *
     * @return \OGIVE\ProjectBundle\Entity\Decompte
     */
    public function getMyDecompte() {
        return $this->myDecompte;
    }
    
    /**
     * Set parentDecompteTask
     *
     * @param \OGIVE\ProjectBundle\Entity\DecompteTask $parentDecompteTask
     *
     * @return DecompteTask
     */
    public function setParentDecompteTask(\OGIVE\ProjectBundle\Entity\DecompteTask $parentDecompteTask=null) {
        $this->parentDecompteTask = $parentDecompteTask;

        return $this;
    }

    /**
     * Get parentDecompteTask
     *
     * @return \OGIVE\ProjectBundle\Entity\DecompteTask
     */
    public function getParentDecompteTask() {
        return $this->parentDecompteTask;
    }
    
    /**
     * Add subDecompteTask
     *
     * @param \OGIVE\ProjectBundle\Entity\DecompteTask $subDecompteTask 
     * @return DecompteTask
     */
    public function addSubDecompteTask(\OGIVE\ProjectBundle\Entity\DecompteTask $subDecompteTask) {
        $this->subDecompteTasks[] = $subDecompteTask;
        return $this;
    }

    /**
     * Get subDecompteTasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubDecompteTasks() {
        return $this->subDecompteTasks;
    }

    /**
     * Set subDecompteTasks
     *
     * @param \Doctrine\Common\Collections\Collection $subDecompteTasks
     * @return DecompteTask
     */
    public function setSubDecompteTasks(\Doctrine\Common\Collections\Collection $subDecompteTasks = null) {
        $this->subDecompteTasks = $subDecompteTasks;

        return $this;
    }

    /**
     * Remove subDecompteTask
     *
     * @param \OGIVE\ProjectBundle\Entity\DecompteTask $subDecompteTask
     * @return DecompteTask
     */
    public function removeSubDecompteTask(\OGIVE\ProjectBundle\Entity\DecompteTask $subDecompteTask) {
        $this->subDecompteTasks->removeElement($subDecompteTask);
        return $this;
    }

    public function setSearchData() {
        $this->searchData = $this->getUnite()." ".$this->getNom()." ".$this->getDescription();
    }

}
