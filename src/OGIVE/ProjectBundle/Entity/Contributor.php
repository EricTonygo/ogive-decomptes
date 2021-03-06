<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contributor
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class Contributor  extends GeneralClass{

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable= true)
     */
    protected $nom;
    
    /**
     * @var string
     *
     * @ORM\Column(name="CONTRIBUTOR_TYPE", type="string", length=255, nullable=true)
     */
    protected $contributorType;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=255, nullable=true)
     */
    protected $codePostal;
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    protected $phone;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    protected $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="faxNumber", type="string", length=255, nullable=true)
     */
    protected $faxNumber;
    
    /**
     * @var string
     *
     * @ORM\Column(name="rc", type="string", length=255, nullable=true)
     */
    protected $rc;
    
    /**
     * @var string
     *
     * @ORM\Column(name="numero_contribuable", type="string", length=255, nullable=true)
     */
    protected $numeroContribuable;
    
    /**
     * @var string
     *
     * @ORM\Column(name="numero_compte_bancaire", type="string", length=255, nullable=true)
     */
    protected $numeroCompteBancaire;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nom_banque", type="string", length=255, nullable=true)
     */
    protected $nomBanque;
    
    /**
     * @var string
     *
     * @ORM\Column(name="intitule", type="string", length=255, nullable=true)
     */
    protected $intitule;
    
    /**
     * @var \OGIVE\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\OGIVE\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id")
     * })
     */
    private $user;
   
    
     /**
     * @var integer
     *
     * @ORM\Column(name="ordre_priorite", type="integer", nullable=true)
     */
    private $ordrePriorite;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="signatory", type="boolean")
     */
    private $signatory;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Contributor
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
     * Set contributorType
     *
     * @param string $contributorType
     *
     * @return Contributor
     */
    public function setContributorType($contributorType) {
        $this->contributorType = $contributorType;
        return $this;
    }

    /**
     * Get contributorType
     *
     * @return string
     */
    public function getContributorType() {
        return $this->contributorType;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return Contributor
     */
    public function setCodePostal($codePostal) {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal() {
        return $this->codePostal;
    }
    
    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Contributor
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }
    
    /**
     * Set email
     *
     * @param string $email
     *
     * @return Contributor
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
    
    /**
     * Set faxNumber
     *
     * @param string $faxNumber
     *
     * @return Contributor
     */
    public function setFaxNumber($faxNumber) {
        $this->faxNumber = $faxNumber;

        return $this;
    }

    /**
     * Get faxNumber
     *
     * @return string
     */
    public function getFaxNumber() {
        return $this->faxNumber;
    }


    /**
     * Set rc
     *
     * @param string $rc
     *
     * @return Contributor
     */
    public function setRc($rc) {
        $this->rc = $rc;

        return $this;
    }

    /**
     * Get rc
     *
     * @return string
     */
    public function getRc() {
        return $this->rc;
    }

    /**
     * Set numeroContribuable
     *
     * @param string $numeroContribuable
     *
     * @return Contributor
     */
    public function setNumeroContribuable($numeroContribuable) {
        $this->numeroContribuable = $numeroContribuable;

        return $this;
    }

    /**
     * Get numeroContribuable
     *
     * @return string
     */
    public function getNumeroContribuable() {
        return $this->numeroContribuable;
    }
    
    /**
     * Set numeroCompteBancaire
     *
     * @param string $numeroCompteBancaire
     *
     * @return Contributor
     */
    public function setNumeroCompteBancaire($numeroCompteBancaire) {
        $this->numeroCompteBancaire = $numeroCompteBancaire;

        return $this;
    }

    /**
     * Get numeroCompteBancaire
     *
     * @return string
     */
    public function getNumeroCompteBancaire() {
        return $this->numeroCompteBancaire;
    }
    
    
    /**
     * Set nomBanque
     *
     * @param string $nomBanque
     *
     * @return Contributor
     */
    public function setNomBanque($nomBanque) {
        $this->nomBanque = $nomBanque;

        return $this;
    }

    /**
     * Get nomBanque
     *
     * @return string
     */
    public function getNomBanque() {
        return $this->nomBanque;
    }
    
    
    /**
     * Set intitule
     *
     * @param string $intitule
     *
     * @return Contributor
     */
    public function setIntitule($intitule) {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * Get intitule
     *
     * @return string
     */
    public function getIntitule() {
        return $this->intitule;
    }
    
    
    /**
     * Set user
     *
     * @param \OGIVE\UserBundle\Entity\User $user
     *
     * @return Contributor
     */
    public function setUser(\OGIVE\UserBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }
    
    /**
     * Set ordrePriorite
     *
     * @param integer $ordrePriorite
     *
     * @return Contributor
     */
    public function setOrdrePriorite($ordrePriorite) {
        $this->ordrePriorite = $ordrePriorite;

        return $this;
    }

    /**
     * Get ordrePriorite
     *
     * @return integer
     */
    public function getOrdrePriorite() {
        return $this->ordrePriorite;
    }
    
    
    /**
     * Set signatory
     *
     * @param boolean $signatory
     *
     * @return Contributor
     */
    public function setSignatory($signatory) {
        $this->signatory = $signatory;

        return $this;
    }

    /**
     * Get signatory
     *
     * @return boolean
     */
    public function isSignatory() {
        return $this->signatory;
    }

    /**
     * Get user
     *
     * @return \OGIVE\UserBundle\Entity\User
     */
    public function getUser() {
        return $this->user;
    }

    public function setSearchData() {
        $this->searchData = $this->getCodePostal()." ".$this->getFaxNumber()." ".$this->getPhone()." ".$this->getNomBanque()." ".$this->getNumeroCompteBancaire()." ".$this->getNumeroContribuable();
    }

}
