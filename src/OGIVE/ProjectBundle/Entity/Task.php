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
     * @ORM\Column(name="unite", type="string", length=255)
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
     * @ORM\Column(name="qte_prevue_marche", type="integer")
     */
    protected $qtePrevueMarche;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_prevue_projet_exec", type="integer")
     */
    protected $qtePrevueProjetExec;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_cumul_mois_prec", type="integer")
     */
    protected $qteCumulMoisPrec;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_mois", type="integer")
     */
    protected $qteMois;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="qte_cumul_mois", type="integer")
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
     * @var \Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project", referencedColumnName="id")
     * })
     */
    private $project;
    
    /**
     * @var \Lot
     *
     * @ORM\ManyToOne(targetEntity="Lot")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lot", referencedColumnName="id")
     * })
     */
    private $lot;
    
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
     * Get mtCumulMois
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
     * Set lot
     *
     * @param \OGIVE\ProjectBundle\Entity\Lot $lot
     *
     * @return Task
     */
    public function setLot(\OGIVE\ProjectBundle\Entity\Lot $lot=null) {
        $this->lot = $lot;

        return $this;
    }

    /**
     * Get lot
     *
     * @return \OGIVE\ProjectBundle\Entity\Lot
     */
    public function getLot() {
        return $this->lot;
    }

    public function setSearchData() {
        $this->searchData = $this->getUnite()." ".$this->getNom()." ".$this->getDescription();
    }

}
