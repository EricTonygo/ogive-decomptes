<?php

namespace OGIVE\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * GeneralClass
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class GeneralClass {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    protected $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer")
     */
    protected $state;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    protected $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update_date", type="datetime")
     */
    protected $lastUpdateDate;
    
    /**
     * @var \OGIVE\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\OGIVE\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_user", referencedColumnName="id")
     * })
     */
    protected $createdUser;
    
    /**
     * @var \OGIVE\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\OGIVE\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="updated_user", referencedColumnName="id")
     * })
     */
    protected $updatedUser;
    
    /**
     * @var string
     *
     * @ORM\Column(name="search_data", type="text", nullable=true)
     */
    protected $searchData;

    public function __construct() {
        $this->state = 0;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return GeneralClass
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return GeneralClass
     */
    public function setState($state) {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return int
     */
    public function getState() {
        return $this->state;
    }
    
    /**
     * Set createdUser
     *
     * @param \OGIVE\UserBundle\Entity\User $user
     *
     * @return GeneralClass
     */
    public function setCreatedUser(\OGIVE\UserBundle\Entity\User $user = null) {
        $this->createdUser = $user;

        return $this;
    }

    /**
     * Get createdUser
     *
     * @return \OGIVE\UserBundle\Entity\User
     */
    public function getCreatedUser() {
        return $this->createdUser;
    }
    
    /**
     * Set updatedUser
     *
     * @param \OGIVE\UserBundle\Entity\User $user
     *
     * @return GeneralClass
     */
    public function setUpdatedUser(\OGIVE\UserBundle\Entity\User $user = null) {
        $this->updatedUser = $user;

        return $this;
    }

    /**
     * Get updatedUser
     *
     * @return \OGIVE\UserBundle\Entity\User
     */
    public function getUpdatedUser() {
        return $this->updatedUser;
    }


    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return GeneralClass
     */
    public function setCreateDate($createDate) {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate() {
        return $this->createDate;
    }

    /**
     * Set lastUpdateDate
     *
     * @param \DateTime $lastUpdateDate
     *
     * @return GeneralClass
     */
    public function setLastUpdateDate($lastUpdateDate) {
        $this->lastUpdateDate = $lastUpdateDate;

        return $this;
    }

    /**
     * Get lastUpdateDate
     *
     * @return \DateTime
     */
    public function getLastUpdateDate() {
        return $this->lastUpdateDate;
    }
    
    /**
     * Set searchData
     */
    public abstract function setSearchData();

    /**
     * Get $searchData
     *
     * @return string
     */
    public function getSearchData() {
        return $this->searchData;
    }

    /**
     * @ORM\PreUpdate() 
     */
    public function preUpdate() {
        $this->lastUpdateDate = new \DateTime('now');
        $this->sendingDate = new \DateTime('now');
        $this->setSearchData();
    }

    /**
     * @ORM\PrePersist() 
     */
    public function prePersist() {
        $this->createDate = new \DateTime('now');
        $this->lastUpdateDate = new \DateTime('now');
        $this->sendingDate = new \DateTime('now');
        $this->status = 1;
        $this->setSearchData();
    }
    
}
