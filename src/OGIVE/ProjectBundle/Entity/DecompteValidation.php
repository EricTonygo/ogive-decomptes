<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DecompteValidation
 *
 * @ORM\Table(name="decompte_validation")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\DecompteValidationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class DecompteValidation extends GeneralClass {
    
    /**
     * @var string
     *
     * @ORM\Column(name="validation_place", type="string", length=255, nullable=true)
     */
    private $validationPlace;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="validation_date", type="datetime", nullable=true)
     */
    private $validationDate;
    
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
     * @var \OGIVE\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\OGIVE\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id")
     * })
     */
    private $user;
    
    /**
     * @var string
     *
     * @ORM\Column(name="CONTRIBUTOR_TYPE", type="string", length=255, nullable=true)
     */
    private $contributorType;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="priorityOrder", type="integer", length=255, nullable=true)
     */
    private $priorityOrder;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    
    /**
     * Set decompte
     *
     * @param \OGIVE\ProjectBundle\Entity\Decompte $decompte
     *
     * @return DecompteValidation
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

    public function setSearchData() {
        $this->searchData = $this->getValidationPlace();
    }


    /**
     * Set validationPlace
     *
     * @param string $validationPlace
     *
     * @return DecompteValidation
     */
    public function setValidationPlace($validationPlace) {
        $this->validationPlace = $validationPlace;

        return $this;
    }

    /**
     * Get validationPlace
     *
     * @return string
     */
    public function getValidationPlace() {
        return $this->validationPlace;
    }
    
    /**
     * Set validationDate
     *
     * @param \DateTime $validationDate
     *
     * @return DecompteValidation
     */
    public function setValidationDate($validationDate) {
        //$this->validationDate = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $validationDate))));
        $this->validationDate = $validationDate;
        return $this;
    }

    /**
     * Get validationDate
     *
     * @return \DateTime
     */
    public function getValidationDate() {
        //return $this->validationDate ? $this->validationDate->format('d-m-Y'): $this->validationDate;
        return $this->validationDate;
        
    }
    
    /**
     * Set contributorType
     *
     * @param string $contributorType
     *
     * @return DecompteValidation
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
     * Set priorityOrder
     *
     * @param integer $priorityOrder
     *
     * @return DecompteValidation
     */
    public function setPriorityOrder($priorityOrder) {
        $this->priorityOrder = $priorityOrder;
        return $this;
    }

    /**
     * Get priorityOrder
     *
     * @return integer
     */
    public function getPriorityOrder() {
        return $this->priorityOrder;
    }
    
    /**
     * Set user
     *
     * @param \OGIVE\UserBundle\Entity\User $user
     *
     * @return DecompteValidation
     */
    public function setUser(\OGIVE\UserBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }
    
    /**
     * Get user
     *
     * @return \OGIVE\UserBundle\Entity\User
     */
    public function getUser() {
        return $this->user;
    }
}
