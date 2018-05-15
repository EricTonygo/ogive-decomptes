<?php

namespace OGIVE\ProjectBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * Description of MailService
 *
 * @author Eric TONYE
 */
class CommonService {

    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function setUserAttributesToContributor(\OGIVE\ProjectBundle\Entity\Contributor $contributor) {
        if ($contributor->getUser()) {
            $user_contributor = $contributor->getUser();
            $contributor->setEmail($user_contributor->getEmail());
            $contributor->setNom($user_contributor->getLastname());
            $contributor->setPhone($user_contributor->getPhone());
            $contributor->setCodePostal($user_contributor->getCodePostal());
            $contributor->setFaxNumber($user_contributor->getFaxNumber());
            $contributor->setRc($user_contributor->getRc());
            $contributor->setNumeroContribuable($user_contributor->getNumeroContribuable());
            $contributor->setNumeroCompteBancaire($user_contributor->getNumeroCompteBancaire());
            $contributor->setNomBanque($user_contributor->getNomBanque());
            $contributor->setIntitule($user_contributor->getIntituleBanque());
        }
    }

    public function setUserAttributesToContributorIfNotExists(\OGIVE\ProjectBundle\Entity\Contributor $contributor) {
        if ($contributor->getUser()) {
            $user_contributor = $contributor->getUser();
            $contributor->setEmail($user_contributor->getEmail());
            $contributor->setNom($user_contributor->getLastname());
            if (!$contributor->getPhone()) {
                $contributor->setPhone($user_contributor->getPhone());
            }
            if (!$contributor->getCodePostal()) {
                $contributor->setCodePostal($user_contributor->getCodePostal());
            }
            if (!$contributor->getFaxNumber()) {
                $contributor->setFaxNumber($user_contributor->getFaxNumber());
            }
            if (!$contributor->getRc()) {
                $contributor->setRc($user_contributor->getRc());
            }
            if (!$contributor->getNumeroContribuable()) {
                $contributor->setNumeroContribuable($user_contributor->getNumeroContribuable());
            }
            if (!$contributor->getNumeroCompteBancaire()) {
                $contributor->setNumeroCompteBancaire($user_contributor->getNumeroCompteBancaire());
            }
            if (!$contributor->getNomBanque()) {
                $contributor->setNomBanque($user_contributor->getNomBanque());
            }
            if (!$contributor->getIntitule()) {
                $contributor->setIntitule($user_contributor->getIntituleBanque());
            }
        }
    }

    //get string date as dd/mm/yy
    public function getStringDateForSms($date) {
        return date("d", strtotime($date)) . "/" . date("m", strtotime($date)) . "/" . substr(date("Y", strtotime($date)), -2);
    }

    public function getMonthNameByNumber($monthNumber) {
        switch ($monthNumber) {
            case 1 :
                return "Janvier";
            case 2 :
                return "Février";
            case 3 :
                return "Mars";
            case 4 :
                return "Avril";
            case 5 :
                return "Mai";
            case 6 :
                return "Juin";
            case 7 :
                return "Juillet";
            case 8 :
                return "Août";
            case 9 :
                return "Septembre";
            case 10 :
                return "Octobre";
            case 11 :
                return "Novembre";
            case 12 :
                return "Décembre";
            default :
                return "";
        }
    }

    public function getMonthList() {
        return array(
            array(
                'number' => 1,
                'name' => "Janvier"
            ),
            array(
                'number' => 2,
                'name' => "Février"
            ),
            array(
                'number' => 3,
                'name' => "Mars"
            ),
            array(
                'number' => 4,
                'name' => "Avril"
            ),
            array(
                'number' => 5,
                'name' => "Mai"
            ),
            array(
                'number' => 6,
                'name' => "Juin"
            ),
            array(
                'number' => 7,
                'name' => "Juillet"
            ),
            array(
                'number' => 8,
                'name' => "Août"
            ),
            array(
                'number' => 9,
                'name' => "Septembre"
            ),
            array(
                'number' => 10,
                'name' => "Octobre"
            ),
            array(
                'number' => 11,
                'name' => "Novembre"
            ),
            array(
                'number' => 12,
                'name' => "Decembre"
            )
        );
    }

    public function getExportExcelRootDir() {
        return __DIR__ . '/../../../../web/exports/excel';
    }

}
