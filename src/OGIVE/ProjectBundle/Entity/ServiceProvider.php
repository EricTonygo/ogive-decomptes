<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceProvider
 *
 * @ORM\Table(name="service_provider")
 * @ORM\Entity(repositoryClass="\OGIVE\ProjectBundle\Repository\ServiceProviderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ServiceProvider extends Contributor {


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
        $this->setContributorType(4);
    }
    
}
