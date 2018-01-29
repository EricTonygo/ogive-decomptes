<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Holder
 *
 * @ORM\Table(name="holder")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\HolderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Holder extends Contributor {


    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @ORM\PrePersist() 
     */
    public function prePersist() {
        parent::prePersist();
        $this->setContributorType(3);
    }
    
}
