<?php

namespace OGIVE\UserBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="OGIVE\UserBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{
     /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstname;
    
     /**
     * @var integer
     *
     * @ORM\Column(name="choosed_plan", type="integer", nullable=true)
     */
    private $choosedPlan;
    
     /**
     * @var integer
     *
     * @ORM\Column(name="confirmed_informations", type="integer", nullable=true)
     */
    private $confirmedInformations;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true) 
     */
    private $photo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="activation_hast", type="string", length=255, nullable=true)
     */
    private $activationHash;

    /**
     * @Assert\File(maxSize="6000000") 
    */
    private $file;
    
    private $temp;
    
    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=255, nullable=true)
     */
    protected $codePostal;
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    protected $phone;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="faxNumber", type="string", length=255, nullable=true)
     */
    protected $faxNumber;
    
    /**
     * @var string
     *
     * @ORM\Column(name="rc", type="string", length=255, nullable=true)
     */
    protected $rc;
    
    /**
     * @var string
     *
     * @ORM\Column(name="numero_contribuable", type="string", length=255, nullable=true)
     */
    protected $numeroContribuable;
    
    /**
     * @var string
     *
     * @ORM\Column(name="numero_compte_bancaire", type="string", length=255, nullable=true)
     */
    protected $numeroCompteBancaire;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nom_banque", type="string", length=255, nullable=true)
     */
    protected $nomBanque;
    
    /**
     * @var string
     *
     * @ORM\Column(name="intitule_banque", type="string", length=255, nullable=true)
     */
    protected $intituleBanque;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }
    
    /**
     * Set choosedPlan
     *
     * @param integer $choosedPlan
     *
     * @return User
     */
    public function setChoosedPlan($choosedPlan) {
        $this->choosedPlan = $choosedPlan;

        return $this;
    }

    /**
     * Get choosedPlan
     *
     * @return integer
     */
    public function getChoosedPlan() {
        return $this->choosedPlan;
    }
    
    /**
     * Set confirmedInformations
     *
     * @param integer $confirmedInformations
     *
     * @return User
     */
    public function setConfirmedInformations($confirmedInformations) {
        $this->confirmedInformations = $confirmedInformations;

        return $this;
    }

    /**
     * Get confirmedInformations
     *
     * @return integer
     */
    public function getConfirmedInformations() {
        return $this->confirmedInformations;
    }
    
    /**
     * Set activationHash
     *
     * @param string $activationHash
     *
     * @return User
     */
    public function setActivationHash($activationHash)
    {
        $this->activationHash = $activationHash;

        return $this;
    }

    /**
     * Get activationHash
     *
     * @return string
     */
    public function getActivationHash()
    {
        return $this->activationHash;
    }
    
    /**
     * Set photo
     *
     * @param string $photo
     * @return User
     */
    public function setPhoto($photo) {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto() {
        return $this->photo;
    }

    /**
     * @param UploadedFile $file
     * @return object
     */
    public function setFile(UploadedFile $file = null) {
        $this->file = $file;
        // check if we have an old image photo
        if (isset($this->photo)) {
            // store the old name to delete after the update
            $this->temp = $this->photo;
            $this->photo = null;
        } else {
            $this->photo = 'initial';
        }
    }

    /**
     * Get the file used for profile picture uploads
     * 
     * @return UploadedFile
     */
    public function getFile() {

        return $this->file;
    }

    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../web/uploads/profils';
    }

    /**
     * @ORM\PrePersist() 
     * @ORM\PreUpdate() 
     */
    public function preUpload() {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->photo = $filename . '.' . $this->getFile()->guessExtension();
        }
    }

    /**
     * Generates a 32 char long random filename
     * 
     * @return string
     */
    public function generateRandomProfilePictureFilename() {
        $count = 0;
        do {
            $generator = new SecureRandom();
            $random = $generator->nextBytes(16);
            $randomString = bin2hex($random);
            $count++;
        } while (file_exists($this->getUploadRootDir() . '/' . $randomString . '.' . $this->getFile()->guessExtension()) && $count < 50);

        return $randomString;
    }

    /**
     * @ORM\PostPersist() 
     * @ORM\PostUpdate() 
     */
    public function upload() {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->photo);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            //unlink($this->getUploadRootDir().'/'.$this->temp);
            //ou je renomme
            rename($this->getUploadRootDir() . '/' . $this->temp, $this->getUploadRootDir() . '/old' . $this->temp);
            // clear the temp image photo
            $this->temp = null;
        }
        $this->file = null;
    }
    
    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return User
     */
    public function setCodePostal($codePostal) {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal() {
        return $this->codePostal;
    }
    
    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }
    
    /**
     * Set faxNumber
     *
     * @param string $faxNumber
     *
     * @return User
     */
    public function setFaxNumber($faxNumber) {
        $this->faxNumber = $faxNumber;

        return $this;
    }

    /**
     * Get faxNumber
     *
     * @return string
     */
    public function getFaxNumber() {
        return $this->faxNumber;
    }


    /**
     * Set rc
     *
     * @param string $rc
     *
     * @return User
     */
    public function setRc($rc) {
        $this->rc = $rc;

        return $this;
    }

    /**
     * Get rc
     *
     * @return string
     */
    public function getRc() {
        return $this->rc;
    }

    /**
     * Set numeroContribuable
     *
     * @param string $numeroContribuable
     *
     * @return User
     */
    public function setNumeroContribuable($numeroContribuable) {
        $this->numeroContribuable = $numeroContribuable;

        return $this;
    }

    /**
     * Get numeroContribuable
     *
     * @return string
     */
    public function getNumeroContribuable() {
        return $this->numeroContribuable;
    }
    
    /**
     * Set numeroCompteBancaire
     *
     * @param string $numeroCompteBancaire
     *
     * @return User
     */
    public function setNumeroCompteBancaire($numeroCompteBancaire) {
        $this->numeroCompteBancaire = $numeroCompteBancaire;

        return $this;
    }

    /**
     * Get numeroCompteBancaire
     *
     * @return string
     */
    public function getNumeroCompteBancaire() {
        return $this->numeroCompteBancaire;
    }
    
    
    /**
     * Set nomBanque
     *
     * @param string $nomBanque
     *
     * @return User
     */
    public function setNomBanque($nomBanque) {
        $this->nomBanque = $nomBanque;

        return $this;
    }

    /**
     * Get nomBanque
     *
     * @return string
     */
    public function getNomBanque() {
        return $this->nomBanque;
    }
    
    
    /**
     * Set intituleBanque
     *
     * @param string $intituleBanque
     *
     * @return User
     */
    public function setIntituleBanque($intituleBanque) {
        $this->intituleBanque = $intituleBanque;

        return $this;
    }

    /**
     * Get intitule
     *
     * @return string
     */
    public function getIntituleBanque() {
        return $this->intituleBanque;
    }
}

