<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Owner
 *
 * @ORM\Table(name="owner")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\OwnerRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Owner extends Contributor {


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
        $this->setContributorType("owner");
    }
    
}
