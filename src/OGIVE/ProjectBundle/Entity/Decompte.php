<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decompte
 *
 * @ORM\Table(name="decompte")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\DecompteRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Decompte extends GeneralClass {

     /**
     * @var integer
     *
     * @ORM\Column(name="month_number", type="integer", nullable=true)
     */
    private $monthNumber;
    
    /**
     * @var string
     *
     * @ORM\Column(name="month_name", type="string", nullable=true)
     */
    private $monthName;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=
     * true)
     */
    private $startDate;
   
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;
    
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
     * @ORM\Column(name="mt_prevue_marche_tva", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPrevueMarcheTVA;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_prevue_projet_exec_tva", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPrevueProjetExecTVA;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_cumul_mois_prec_tva", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtCumulMoisPrecTVA;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_mois_tva", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtMoisTVA;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_cumul_mois_tva", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtCumulMoisTVA;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_prevue_marche_ir", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPrevueMarcheIR;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_prevue_projet_exec_ir", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPrevueProjetExecIR;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_cumul_mois_prec_ir", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtCumulMoisPrecIR;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_mois_ir", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtMoisIR;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_cumul_mois_ir", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtCumulMoisIR;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_prevue_marche_net_a_percevoir", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPrevueMarcheNetAPercevoir;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_prevue_projet_exec_net_a_percevoir", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPrevueProjetExecNetAPercevoir;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_cumul_mois_prec_net_a_percevoir", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtCumulMoisPrecNetAPercevoir;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_mois_net_a_percevoir", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtMoisNetAPercevoir;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_cumul_mois_net_a_percevoir", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtCumulMoisNetAPercevoir;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_prevue_marche_ttc", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPrevueMarcheTTC;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_prevue_projet_exec_ttc", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPrevueProjetExecTTC;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_cumul_mois_prec_ttc", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtCumulMoisPrecTTC;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_mois_ttc", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtMoisTTC;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_cumul_mois_ttc", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtCumulMoisTTC;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_avenant", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtAvenant;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_avance_demarrage", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtAvanceDemarrage;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_retenue_garantie", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtRetenueGarantie;
    
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_remboursement_avance", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtRemboursementAvance;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_penalite", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPenalite;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_total_paiements", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtTotalPaiements;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_taxes_air", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtTaxesAIR;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_taxes_tva", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtTaxesTVA;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_avance_demarrage_prec", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtAvanceDemarragePrec;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_retenue_garantie_prec", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtRetenueGarantiePrec;
    
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_remboursement_avance_prec", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtRemboursementAvancePrec;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_penalite_prec", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPenalitePrec;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_total_paiements_prec", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtTotalPaiementsPrec;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_taxes_air_prec", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtTaxesAIRPrec;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_taxes_tva_prec", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtTaxesTVAPrec;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_avance_demarrageACD", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtAvanceDemarrageACD;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_prestations_with_air_acd", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPrestationsWithAirACD;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_retenue_garantie_acd", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtRetenueGarantieACD;
    
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_remboursement_avance_acd", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtRemboursementAvanceACD;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_penalite_acd", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtPenaliteACD;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_total_paiements_acd", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtTotalPaiementsACD;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_taxes_air_acd", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtTaxesAIRACD;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_taxes_tva_acd", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtTaxesTVAACD;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mt_taxes_tva_acm", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtACM;
   
    /**
     * @var float
     *
     * @ORM\Column(name="mt_total_marche_htva", type="float", precision=20, scale=0, nullable=true)
     */
    private $mtTotalMarcheHTVA;

    /**
     * @var float
     *
     * @ORM\Column(name="pourcent_realisation", type="float", precision=20, scale=0, nullable=true)
     */
    private $pourcentRealisation; 
    
    /**
     * @var integer
     *
     * @ORM\Column(name="decompte_state", type="integer")
     */
    protected $decompteState;
    
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
     * @var \Decompte
     *
     * @ORM\ManyToOne(targetEntity="DecompteTotal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="decompte_total", referencedColumnName="id")
     * })
     */
    private $decompteTotal;
    
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\OGIVE\ProjectBundle\Entity\DecompteTask", mappedBy="decompte", cascade={"remove", "persist"})
    */
    private $decompteTasks;
    

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->decompteTasks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->decomptes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set monthName
     *
     * @param string $monthName
     *
     * @return Decompte
     */
    public function setMonthName($monthName) {
        $this->monthName = $monthName;

        return $this;
    }

    /**
     * Get monthName
     *
     * @return string
     */
    public function getMonthName() {
        return $this->monthName;
    }
    
    /**
     * Set monthNumber
     *
     * @param integer $monthNumber
     *
     * @return Decompte
     */
    public function setMonthNumber($monthNumber) {
        $this->monthNumber = $monthNumber;

        return $this;
    }

    /**
     * Get monthNumber
     *
     * @return integer
     */
    public function getMonthNumber() {
        return $this->monthNumber;
    }
    
    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Decompte
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
     * @return Decompte
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
     * Set mtPrevueMarche
     *
     * @param float $mtPrevueMarche
     *
     * @return Decompte
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
     * Set mtPrevueMarcheTVA
     *
     * @param float $mtPrevueMarcheTVA
     *
     * @return Decompte
     */
    public function setMtPrevueMarcheTVA($mtPrevueMarcheTVA) {
        $this->mtPrevueMarcheTVA = $mtPrevueMarcheTVA;

        return $this;
    }

    /**
     * Get mtPrevueMarcheTVA
     *
     * @return float
     */
    public function getMtPrevueMarcheTVA() {
        return $this->mtPrevueMarcheTVA;
    }
    
    /**
     * Set mtPrevueMarcheIR
     *
     * @param float $mtPrevueMarcheIR
     *
     * @return Decompte
     */
    public function setMtPrevueMarcheIR($mtPrevueMarcheIR) {
        $this->mtPrevueMarcheIR = $mtPrevueMarcheIR;

        return $this;
    }

    /**
     * Get mtPrevueMarcheIR
     *
     * @return float
     */
    public function getMtPrevueMarcheIR() {
        return $this->mtPrevueMarcheIR;
    }
    
    /**
     * Set mtPrevueMarcheNetAPercevoir
     *
     * @param float $mtPrevueMarcheNetAPercevoir
     *
     * @return Decompte
     */
    public function setMtPrevueMarcheNetAPercevoir($mtPrevueMarcheNetAPercevoir) {
        $this->mtPrevueMarcheNetAPercevoir = $mtPrevueMarcheNetAPercevoir;

        return $this;
    }

    /**
     * Get mtPrevueMarcheNetAPercevoir
     *
     * @return float
     */
    public function getMtPrevueMarcheNetAPercevoir() {
        return $this->mtPrevueMarcheNetAPercevoir;
    }
    
    /**
     * Set mtPrevueMarcheTTC
     *
     * @param float $mtPrevueMarcheTTC
     *
     * @return Decompte
     */
    public function setMtPrevueMarcheTTC($mtPrevueMarcheTTC) {
        $this->mtPrevueMarcheTTC = $mtPrevueMarcheTTC;

        return $this;
    }

    /**
     * Get mtPrevueMarcheTTC
     *
     * @return float
     */
    public function getMtPrevueMarcheTTC() {
        return $this->mtPrevueMarcheTTC;
    }
    
    /**
     * Set qtePrevueMarche
     *
     * @param float $qtePrevueMarche
     *
     * @return Decompte
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
     * @return Decompte
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
     * Set mtPrevueProjetExecTVA
     *
     * @param float $mtPrevueProjetExecTVA
     *
     * @return Decompte
     */
    public function setMtPrevueProjetExecTVA($mtPrevueProjetExecTVA) {
        $this->mtPrevueProjetExecTVA = $mtPrevueProjetExecTVA;

        return $this;
    }

    /**
     * Get mtPrevueProjetExecTVA
     *
     * @return float
     */
    public function getMtPrevueProjetExecTVA() {
        return $this->mtPrevueProjetExec;
    }
    
    /**
     * Set mtPrevueProjetExecIR
     *
     * @param float $mtPrevueProjetExecIR
     *
     * @return Decompte
     */
    public function setMtPrevueProjetExecIR($mtPrevueProjetExecIR) {
        $this->mtPrevueProjetExecIR = $mtPrevueProjetExecIR;

        return $this;
    }

    /**
     * Get mtPrevueProjetExecIR
     *
     * @return float
     */
    public function getMtPrevueProjetExecIR() {
        return $this->mtPrevueProjetExecIR;
    }
    
    /**
     * Set mtPrevueProjetExecNetAPercevoir
     *
     * @param float $mtPrevueProjetExecNetAPercevoir
     *
     * @return Decompte
     */
    public function setMtPrevueProjetExecNetAPercevoir($mtPrevueProjetExecNetAPercevoir) {
        $this->mtPrevueProjetExecNetAPercevoir = $mtPrevueProjetExecNetAPercevoir;

        return $this;
    }

    /**
     * Get mtPrevueProjetExecNetAPercevoir
     *
     * @return float
     */
    public function getMtPrevueProjetExecNetAPercevoir() {
        return $this->mtPrevueProjetExecNetAPercevoir;
    }
    
    /**
     * Set mtPrevueProjetExecTTC
     *
     * @param float $mtPrevueProjetExecTTC
     *
     * @return Decompte
     */
    public function setMtPrevueProjetExecTTC($mtPrevueProjetExecTTC) {
        $this->mtPrevueProjetExecTTC = $mtPrevueProjetExecTTC;

        return $this;
    }

    /**
     * Get mtPrevueProjetExecTTC
     *
     * @return float
     */
    public function getMtPrevueProjetExecTTC() {
        return $this->mtPrevueProjetExecTTC;
    }
    
    
    /**
     * Set qtePrevueProjetExec
     *
     * @param float $qtePrevueProjetExec
     *
     * @return Decompte
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
     * @return Decompte
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
     * Set mtCumulMoisPrecTVA
     *
     * @param float $mtCumulMoisPrecTVA
     *
     * @return Decompte
     */
    public function setMtCumulMoisPrecTVA($mtCumulMoisPrecTVA) {
        $this->mtCumulMoisPrecTVA = $mtCumulMoisPrecTVA;

        return $this;
    }

    /**
     * Get mtCumulMoisPrecTVA
     *
     * @return float
     */
    public function getMtCumulMoisPrecTVA() {
        return $this->mtCumulMoisPrecTVA;
    }
    
    /**
     * Set mtCumulMoisPrecIR
     *
     * @param float $mtCumulMoisPrecIR
     *
     * @return Decompte
     */
    public function setMtCumulMoisPrecIR($mtCumulMoisPrecIR) {
        $this->mtCumulMoisPrecIR = $mtCumulMoisPrecIR;

        return $this;
    }

    /**
     * Get mtCumulMoisPrecIR
     *
     * @return float
     */
    public function getMtCumulMoisPrecIR() {
        return $this->mtCumulMoisPrecIR;
    }
    
    /**
     * Set mtCumulMoisPrecNetAPercevoir
     *
     * @param float $mtCumulMoisPrecNetAPercevoir
     *
     * @return Decompte
     */
    public function setMtCumulMoisPrecNetAPercevoir($mtCumulMoisPrecNetAPercevoir) {
        $this->mtCumulMoisPrecNetAPercevoir = $mtCumulMoisPrecNetAPercevoir;

        return $this;
    }

    /**
     * Get mtCumulMoisPrecNetAPercevoir
     *
     * @return float
     */
    public function getMtCumulMoisPrecNetAPercevoir() {
        return $this->mtCumulMoisPrecNetAPercevoir;
    }
    
    /**
     * Set mtCumulMoisPrecTTC
     *
     * @param float $mtCumulMoisPrecTTC
     *
     * @return Decompte
     */
    public function setMtCumulMoisPrecTTC($mtCumulMoisPrecTTC) {
        $this->mtCumulMoisPrecTTC = $mtCumulMoisPrecTTC;

        return $this;
    }

    /**
     * Get mtCumulMoisPrecTTC
     *
     * @return float
     */
    public function getMtCumulMoisPrecTTC() {
        return $this->mtCumulMoisPrecTTC;
    }
    
    /**
     * Set qteCumulMoisPrec
     *
     * @param float $qteCumulMoisPrec
     *
     * @return Decompte
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
     * @return Decompte
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
     * Set mtMoisTVA
     *
     * @param float $mtMoisTVA
     *
     * @return Decompte
     */
    public function setMtMoisTVA($mtMoisTVA) {
        $this->mtMoisTVA = $mtMoisTVA;

        return $this;
    }

    /**
     * Get mtMoisTVA
     *
     * @return float
     */
    public function getMtMoisTVA() {
        return $this->mtMoisTVA;
    }
    
    /**
     * Set mtMoisIR
     *
     * @param float $mtMoisIR
     *
     * @return Decompte
     */
    public function setMtMoisIR($mtMoisIR) {
        $this->mtMoisIR = $mtMoisIR;

        return $this;
    }

    /**
     * Get mtMoisIR
     *
     * @return float
     */
    public function getMtMoisIR() {
        return $this->mtMoisIR;
    }
    
    /**
     * Set mtMoisNetAPercevoir
     *
     * @param float $mtMoisNetAPercevoir
     *
     * @return Decompte
     */
    public function setMtMoisNetAPercevoir($mtMoisNetAPercevoir) {
        $this->mtMoisNetAPercevoir = $mtMoisNetAPercevoir;

        return $this;
    }

    /**
     * Get mtMoisNetAPercevoir
     *
     * @return float
     */
    public function getMtMoisNetAPercevoir() {
        return $this->mtMoisNetAPercevoir;
    }
    
    /**
     * Set mtMoisTTC
     *
     * @param float $mtMoisTTC
     *
     * @return Decompte
     */
    public function setMtMoisTTC($mtMoisTTC) {
        $this->mtMoisTTC = $mtMoisTTC;

        return $this;
    }

    /**
     * Get mtMoisTTC
     *
     * @return float
     */
    public function getMtMoisTTC() {
        return $this->mtMois;
    }
    
    /**
     * Set qteMois
     *
     * @param float $qteMois
     *
     * @return Decompte
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
     * @return Decompte
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
     * Set mtCumulMoisTVA
     *
     * @param float $mtCumulMoisTVA
     *
     * @return Decompte
     */
    public function setMtCumulMoisTVA($mtCumulMoisTVA) {
        $this->mtCumulMoisTVA = $mtCumulMoisTVA;

        return $this;
    }

    /**
     * Get mtCumulMoisTVA
     *
     * @return float
     */
    public function getMtCumulMoisTVA() {
        return $this->mtCumulMoisTVA;
    }
    
    /**
     * Set mtCumulMoisIR
     *
     * @param float $mtCumulMoisIR
     *
     * @return Decompte
     */
    public function setMtCumulMoisIR($mtCumulMoisIR) {
        $this->mtCumulMoisIR = $mtCumulMoisIR;

        return $this;
    }

    /**
     * Get mtCumulMoisIR
     *
     * @return float
     */
    public function getMtCumulMoisIR() {
        return $this->mtCumulMoisIR;
    }
    
    /**
     * Set mtCumulMoisNetAPercevoir
     *
     * @param float $mtCumulMoisNetAPercevoir
     *
     * @return Decompte
     */
    public function setMtCumulMoisNetAPercevoir($mtCumulMoisNetAPercevoir) {
        $this->mtCumulMoisNetAPercevoir = $mtCumulMoisNetAPercevoir;

        return $this;
    }

    /**
     * Get mtCumulMoisNetAPercevoir
     *
     * @return float
     */
    public function getMtCumulMoisNetAPercevoir() {
        return $this->mtCumulMoisNetAPercevoir;
    }
    
    /**
     * Set mtCumulMoisTTC
     *
     * @param float $mtCumulMoisTTC
     *
     * @return Decompte
     */
    public function setMtCumulMoisTTC($mtCumulMoisTTC) {
        $this->mtCumulMoisTTC = $mtCumulMoisTTC;

        return $this;
    }

    /**
     * Get mtCumulMoisTTC
     *
     * @return float
     */
    public function getMtCumulMoisTTC() {
        return $this->mtCumulMoisTTC;
    }
    
    /**
     * Set qteCumulMois
     *
     * @param float $qteCumulMois
     *
     * @return Decompte
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
     * @return Decompte
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
     * @return Decompte
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
     * Set decompte
     *
     * @param \OGIVE\ProjectBundle\Entity\DecompteTotal $decompteTotal
     *
     * @return Decompte
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
     * Set decompteState
     *
     * @param integer $decompteState
     *
     * @return Decompte
     */
    public function setDecompteState($decompteState) {
        $this->decompteState = $decompteState;

        return $this;
    }

    /**
     * Get decompteState
     *
     * @return int
     */
    public function getDecompteState() {
        return $this->decompteState;
    }
    
    /**
     * Add decompteTask
     *
     * @param \OGIVE\ProjectBundle\Entity\DecompteTask $decompteTask 
     * @return Decompte
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
     * @return Decompte
     */
    public function setDecompteTasks(\Doctrine\Common\Collections\Collection $decompteTasks = null) {
        $this->decompteTasks = $decompteTasks;

        return $this;
    }

    /**
     * Remove decompteTask
     *
     * @param \OGIVE\ProjectBundle\Entity\DecompteTask $decompteTask
     * @return Decompte
     */
    public function removeDecompteTask(\OGIVE\ProjectBundle\Entity\DecompteTask $decompteTask) {
        $this->decompteTasks->removeElement($decompteTask);
        return $this;
    }

    public function setSearchData() {
        $this->searchData = $this->getMonthName();
    }

    /**
     * Set mtAvenant
     *
     * @param float $mtAvenant
     *
     * @return Decompte
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
     * Set mtTotalMarcheHTVA
     *
     * @param float $mtTotalMarcheHTVA
     *
     * @return Decompte
     */
    public function setMtTotalMarcheHTVA($mtTotalMarcheHTVA) {
        $this->mtTotalMarcheHTVA = $mtTotalMarcheHTVA;

        return $this;
    }
    
    /**
     * Set mtPenalite
     *
     * @param float $mtPenalite
     *
     * @return Decompte
     */
    public function setMtPenalite($mtPenalite) {
        $this->mtPenalite = $mtPenalite;

        return $this;
    }

    /**
     * Get mtPenalite
     *
     * @return float
     */
    public function getMtPenalite() {
        return $this->mtPenalite;
    }

    /**
     * Get mtTotalMarcheHTVA
     *
     * @return float
     */
    public function getMtTotalMarcheHTVA() {
        return $this->mtTotalMarcheHTVA;
    }

    function getMtAvanceDemarrage() {
        return $this->mtAvanceDemarrage;
    }

    function getMtRetenueGarantie() {
        return $this->mtRetenueGarantie;
    }

    function getMtRemboursementAvance() {
        return $this->mtRemboursementAvance;
    }

    function getMtTotalPaiements() {
        return $this->mtTotalPaiements;
    }

    function getMtTaxesAIR() {
        return $this->mtTaxesAIR;
    }

    function getMtTaxesTVA() {
        return $this->mtTaxesTVA;
    }

    function getMtAvanceDemarragePrec() {
        return $this->mtAvanceDemarragePrec;
    }

    function getMtRetenueGarantiePrec() {
        return $this->mtRetenueGarantiePrec;
    }

    function getMtRemboursementAvancePrec() {
        return $this->mtRemboursementAvancePrec;
    }

    function getMtPenalitePrec() {
        return $this->mtPenalitePrec;
    }

    function getMtTotalPaiementsPrec() {
        return $this->mtTotalPaiementsPrec;
    }

    function getMtTaxesAIRPrec() {
        return $this->mtTaxesAIRPrec;
    }

    function getMtTaxesTVAPrec() {
        return $this->mtTaxesTVAPrec;
    }

    function getMtAvanceDemarrageACD() {
        return $this->mtAvanceDemarrageACD;
    }

    function getMtRetenueGarantieACD() {
        return $this->mtRetenueGarantieACD;
    }

    function getMtRemboursementAvanceACD() {
        return $this->mtRemboursementAvanceACD;
    }

    function getMtPenaliteACD() {
        return $this->mtPenaliteACD;
    }

    function getMtTotalPaiementsACD() {
        return $this->mtTotalPaiementsACD;
    }

    function getMtTaxesAIRACD() {
        return $this->mtTaxesAIRACD;
    }

    function getMtTaxesTVAACD() {
        return $this->mtTaxesTVAACD;
    }

    function getMtACM() {
        return $this->mtACM;
    }

    function setMtAvanceDemarrage($mtAvanceDemarrage) {
        $this->mtAvanceDemarrage = $mtAvanceDemarrage;
    }

    function setMtRetenueGarantie($mtRetenueGarantie) {
        $this->mtRetenueGarantie = $mtRetenueGarantie;
    }

    function setMtRemboursementAvance($mtRemboursementAvance) {
        $this->mtRemboursementAvance = $mtRemboursementAvance;
    }

    
    function setMtTotalPaiements($mtTotalPaiements) {
        $this->mtTotalPaiements = $mtTotalPaiements;
    }

    function setMtTaxesAIR($mtTaxesAIR) {
        $this->mtTaxesAIR = $mtTaxesAIR;
    }

    function setMtTaxesTVA($mtTaxesTVA) {
        $this->mtTaxesTVA = $mtTaxesTVA;
    }

    function setMtAvanceDemarragePrec($mtAvanceDemarragePrec) {
        $this->mtAvanceDemarragePrec = $mtAvanceDemarragePrec;
    }

    function setMtRetenueGarantiePrec($mtRetenueGarantiePrec) {
        $this->mtRetenueGarantiePrec = $mtRetenueGarantiePrec;
    }

    function setMtRemboursementAvancePrec($mtRemboursementAvancePrec) {
        $this->mtRemboursementAvancePrec = $mtRemboursementAvancePrec;
    }

    function setMtPenalitePrec($mtPenalitePrec) {
        $this->mtPenalitePrec = $mtPenalitePrec;
    }

    function setMtTotalPaiementsPrec($mtTotalPaiementsPrec) {
        $this->mtTotalPaiementsPrec = $mtTotalPaiementsPrec;
    }

    function setMtTaxesAIRPrec($mtTaxesAIRPrec) {
        $this->mtTaxesAIRPrec = $mtTaxesAIRPrec;
    }

    function setMtTaxesTVAPrec($mtTaxesTVAPrec) {
        $this->mtTaxesTVAPrec = $mtTaxesTVAPrec;
    }

    function setMtAvanceDemarrageACD($mtAvanceDemarrageACD) {
        $this->mtAvanceDemarrageACD = $mtAvanceDemarrageACD;
    }

    function setMtRetenueGarantieACD($mtRetenueGarantieACD) {
        $this->mtRetenueGarantieACD = $mtRetenueGarantieACD;
    }

    function setMtRemboursementAvanceACD($mtRemboursementAvanceACD) {
        $this->mtRemboursementAvanceACD = $mtRemboursementAvanceACD;
    }

    function setMtPenaliteACD($mtPenaliteACD) {
        $this->mtPenaliteACD = $mtPenaliteACD;
    }

    function setMtTotalPaiementsACD($mtTotalPaiementsACD) {
        $this->mtTotalPaiementsACD = $mtTotalPaiementsACD;
    }

    function setMtTaxesAIRACD($mtTaxesAIRACD) {
        $this->mtTaxesAIRACD = $mtTaxesAIRACD;
    }

    function setMtTaxesTVAACD($mtTaxesTVAACD) {
        $this->mtTaxesTVAACD = $mtTaxesTVAACD;
    }

    function setMtACM($mtACM) {
        $this->mtACM = $mtACM;
    }

    function getMtPrestationsWithAirACD() {
        return $this->mtPrestationsWithAirACD;
    }

    function setMtPrestationsWithAirACD($mtPrestationsWithAirACD) {
        $this->mtPrestationsWithAirACD = $mtPrestationsWithAirACD;
    }


}
