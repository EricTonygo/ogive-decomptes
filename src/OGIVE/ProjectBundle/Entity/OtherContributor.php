<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OtherContributor
 *
 * @ORM\Table(name="other_contributor")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\OtherContributorRepository")
 * @ORM\HasLifecycleCallbacks
 */
class OtherContributor extends Contributor {


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
    }
    
}
