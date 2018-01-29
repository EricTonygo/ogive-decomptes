<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectManager
 *
 * @ORM\Table(name="project_manager")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\ProjectManagerRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ProjectManager extends Contributor {


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
        $this->setContributorType(2);
    }
    
}
