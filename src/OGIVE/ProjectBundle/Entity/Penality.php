<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Penality
 *
 * @ORM\Table(name="penality")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\PenalityRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Penality extends GeneralClass {
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var double
     *
     * @ORM\Column(name="amount", type="float", precision=20, scale=0, nullable=true)
     */
    private $amount;
    
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
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

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
     * @return Penality
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
        $this->searchData = $this->getName()." ".$this->getAmount();
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Penality
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
        return $this->name;
    }
    
    /**
     * Set amount
     *
     * @param double $amount
     *
     * @return Penality
     */
    public function setAmount($amount) {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return double
     */
    public function getAmount() {
        return $this->amount;
        
    }
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return Penality
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
    
}
