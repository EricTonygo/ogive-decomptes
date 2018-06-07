<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DecompteHistory
 *
 * @ORM\Table(name="decompte_history")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\DecompteHistory")
 * @ORM\HasLifecycleCallbacks
 */
class DecompteHistory extends GeneralClass {
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;
    
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
     * @return DecompteHistory
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
        $this->searchData = $this->getN()." ".$this->getRemboursementAvancePercent();
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return DecompteHistory
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
        return $this->projectPercent;
    }
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return DecompteHistory
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
    public function getRemboursementAvancePercent() {
        return $this->remboursementAvancePercent;
        
    }
    
}
