<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Decompte
 *
 * @ORM\Table(name="decompte")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\DecompteValidationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class DecompteValidation extends GeneralClass {
    
    /**
     * @var string
     *
     * @ORM\Column(name="validation_place", type="string", length=255)
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
     * @return Decompte
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
        $this->searchData = $this->getMonthName();
    }


    /**
     * Set validationPlace
     *
     * @param string $validationPlace
     *
     * @return Decompte
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
     * @return Decompte
     */
    public function setValidationDate($validationDate) {
        $this->validationDate = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $validationDate))));

        return $this;
    }

    /**
     * Get validationDate
     *
     * @return \DateTime
     */
    public function getValidationDate() {
        return $this->validationDate ? $this->validationDate->format('d-m-Y'): $this->validationDate;
        
    }
}
