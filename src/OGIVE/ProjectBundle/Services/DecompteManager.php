<?php

namespace OGIVE\ProjectBundle\Services;

use Doctrine\ORM\EntityManager;
use OGIVE\ProjectBundle\Entity\Decompte;
use OGIVE\ProjectBundle\Entity\DecompteTask;
use OGIVE\ProjectBundle\Entity\Task;
use OGIVE\ProjectBundle\Entity\Project;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Description of MailService
 *
 * @author Eric TONYE
 */
class DecompteManager {

    protected $em;
    protected $tokenStorage;

    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function createDecompteTaskUsingTask(Task $task, Decompte $decompte, Decompte $decomptePrec = null) {
        $decompteTask = new DecompteTask();
        $decompteTask->setNom($task->getNom());
        $decompteTask->setNumero($task->getNumero());
        if ($task->getSubTasks() && count($task->getSubTasks()) > 0) {
            $subTasks = $task->getSubTasks();
            $mtPrevueMarche = 0;
            $mtPrevueProjetExec = 0;
            $mtCumulMoisPrec = 0;
            $mtMois = 0;
            $mtCumulMois = 0;
            foreach ($subTasks as $subTask) {
                $subDecompteTask = $this->createDecompteTaskUsingTask($subTask, $decompte, $decomptePrec);
                $subDecompteTask->setParentDecompteTask($decompteTask);
                $decompteTask->addSubDecompteTask($subDecompteTask);
                $mtPrevueMarche = $subDecompteTask->getMtPrevueMarche() >= 0 ? $mtPrevueMarche + $subDecompteTask->getMtPrevueMarche() : $mtPrevueMarche;
                $mtPrevueProjetExec = $subDecompteTask->getMtPrevueProjetExec() >= 0 ? $mtPrevueProjetExec + $subDecompteTask->getMtPrevueProjetExec() : $mtPrevueProjetExec;
                $mtMois = $subDecompteTask->getMtMois() >= 0 ? $mtMois + $subDecompteTask->getMtMois() : $mtMois;
                $mtCumulMoisPrec = $subDecompteTask->getMtCumulMoisPrec() >= 0 ? $mtCumulMoisPrec + $subDecompteTask->getMtCumulMoisPrec() : $mtCumulMoisPrec;
                $mtCumulMois = $subDecompteTask->getMtCumulMois() >= 0 ? $mtCumulMois + $subDecompteTask->getMtCumulMois() : $mtCumulMois;
            }
            $decompteTask->setMtPrevueMarche($mtPrevueMarche);
            $decompteTask->setMtPrevueProjetExec($mtPrevueProjetExec);
            $decompteTask->setMtMois($mtMois);
            $decompteTask->setMtCumulMoisPrec($mtCumulMoisPrec);
            $decompteTask->setMtCumulMois($mtCumulMois);
            if ($decompteTask->getMtCumulMois() >= 0 && $decompteTask->getMtPrevueProjetExec() > 0) {
                $decompteTask->setPourcentRealisation(round($decompteTask->getMtCumulMois() * 100 / $decompteTask->getMtPrevueProjetExec(), 2));
            } else {
                $decompteTask->setPourcentRealisation(0);
            }
        } else {
            if ($decomptePrec) {
                $decompteTaskPrec = $this->em->getRepository('OGIVEProjectBundle:DecompteTask')->getDecompteTaskByDecompteAndTask($task->getId(), $decomptePrec->getId());
                $decompteTask->setQteCumulMoisPrec($decompteTaskPrec->getQteCumulMois());
                $decompteTask->setQteMois(null);
                $decompteTask->setQteCumulMois($decompteTask->getQteCumulMoisPrec());
            } else {
                $decompteTask->setQteCumulMoisPrec(0);
                $decompteTask->setQteMois(null);
                $decompteTask->setQteCumulMois(0);
            }
            $decompteTask->setUnite($task->getUnite());
            $decompteTask->setPrixUnitaire($task->getPrixUnitaire());
            $decompteTask->setQtePrevueMarche($task->getQtePrevueMarche());
            $decompteTask->setQtePrevueProjetExec($task->getQtePrevueProjetExec());
            if ($decompteTask->getPrixUnitaire() >= 0) {
                if ($decompteTask->getQteCumulMois() >= 0) {
                    $decompteTask->setMtCumulMois($decompteTask->getPrixUnitaire() * $decompteTask->getQteCumulMois());
                }
                if ($decompteTask->getQteCumulMoisPrec() >= 0) {
                    $decompteTask->setMtCumulMoisPrec($decompteTask->getPrixUnitaire() * $decompteTask->getQteCumulMoisPrec());
                }
                if ($decompteTask->getQteMois() >= 0) {
                    $decompteTask->setMtMois($decompteTask->getQteMois() * $decompteTask->getPrixUnitaire());
                }
                if ($decompteTask->getQtePrevueMarche() >= 0) {
                    $decompteTask->setMtPrevueMarche($decompteTask->getQtePrevueMarche() * $decompteTask->getPrixUnitaire());
                }
                if ($decompteTask->getQtePrevueProjetExec() >= 0) {
                    $decompteTask->setMtPrevueProjetExec($decompteTask->getQtePrevueProjetExec() * $decompteTask->getPrixUnitaire());
                }
            }
            if ($decompteTask->getQteCumulMois() >= 0 && $decompteTask->getQtePrevueProjetExec() > 0) {
                $decompteTask->setPourcentRealisation(round($decompteTask->getQteCumulMois() * 100 / $decompteTask->getQtePrevueProjetExec(), 2));
            } else {
                $decompteTask->setPourcentRealisation(0);
            }
        }
        $decompteTask->setTask($task);
        $task->addDecompteTask($decompteTask);
        $decompteTask->setMyDecompte($decompte);
        return $decompteTask;
    }

    public function updateDecompteTask(DecompteTask $decompteTask, Decompte $decomptePrec = null) {
        if ($decompteTask->getSubDecompteTasks() && count($decompteTask->getSubDecompteTasks()) > 0) {
            $subDecompteTasks = $decompteTask->getSubDecompteTasks();
            $mtPrevueMarche = 0;
            $mtPrevueProjetExec = 0;
            $mtCumulMoisPrec = 0;
            $mtMois = 0;
            $mtCumulMois = 0;
            foreach ($subDecompteTasks as $subDecompteTask) {
                $subDecompteTask = $this->updateDecompteTask($subDecompteTask, $decomptePrec);
                $mtPrevueMarche = $subDecompteTask->getMtPrevueMarche() >= 0 ? $mtPrevueMarche + $subDecompteTask->getMtPrevueMarche() : $mtPrevueMarche;
                $mtPrevueProjetExec = $subDecompteTask->getMtPrevueProjetExec() >= 0 ? $mtPrevueProjetExec + $subDecompteTask->getMtPrevueProjetExec() : $mtPrevueProjetExec;
                $mtMois = $subDecompteTask->getMtMois() >= 0 ? $mtMois + $subDecompteTask->getMtMois() : $mtMois;
                $mtCumulMoisPrec = $subDecompteTask->getMtCumulMoisPrec() >= 0 ? $mtCumulMoisPrec + $subDecompteTask->getMtCumulMoisPrec() : $mtCumulMoisPrec;
                $mtCumulMois = $subDecompteTask->getMtCumulMois() >= 0 ? $mtCumulMois + $subDecompteTask->getMtCumulMois() : $mtCumulMois;
            }
            $decompteTask->setMtPrevueMarche($mtPrevueMarche);
            $decompteTask->setMtPrevueProjetExec($mtPrevueProjetExec);
            $decompteTask->setMtMois($mtMois);
            $decompteTask->setMtCumulMoisPrec($mtCumulMoisPrec);
            $decompteTask->setMtCumulMois($mtCumulMois);
            if ($decompteTask->getMtCumulMois() >= 0 && $decompteTask->getMtPrevueProjetExec() > 0) {
                $decompteTask->setPourcentRealisation(round($decompteTask->getMtCumulMois() * 100 / $decompteTask->getMtPrevueProjetExec(), 2));
            } else {
                $decompteTask->setPourcentRealisation(0);
            }
        } else {
            if ($decomptePrec) {
                $decompteTaskPrec = $this->em->getRepository('OGIVEProjectBundle:DecompteTask')->getDecompteTaskByDecompteAndTask($decompteTask->getTask()->getId(), $decomptePrec->getId());
                $decompteTask->setQteCumulMoisPrec($decompteTaskPrec->getQteCumulMois());
            } else {
                $decompteTask->setQteCumulMoisPrec(0);
            }
            $decompteTask->setQteCumulMois($decompteTask->getQteCumulMoisPrec() + $decompteTask->getQteMois());
            if ($decompteTask->getPrixUnitaire() >= 0) {
                if ($decompteTask->getQteCumulMois() >= 0) {
                    $decompteTask->setMtCumulMois($decompteTask->getPrixUnitaire() * $decompteTask->getQteCumulMois());
                }
                if ($decompteTask->getQteCumulMoisPrec() >= 0) {
                    $decompteTask->setMtCumulMoisPrec($decompteTask->getPrixUnitaire() * $decompteTask->getQteCumulMoisPrec());
                }
                if ($decompteTask->getQteMois() >= 0) {
                    $decompteTask->setMtMois($decompteTask->getQteMois() * $decompteTask->getPrixUnitaire());
                }
                if ($decompteTask->getQtePrevueMarche() >= 0) {
                    $decompteTask->setMtPrevueMarche($decompteTask->getQtePrevueMarche() * $decompteTask->getPrixUnitaire());
                }
                if ($decompteTask->getQtePrevueProjetExec() >= 0) {
                    $decompteTask->setMtPrevueProjetExec($decompteTask->getQtePrevueProjetExec() * $decompteTask->getPrixUnitaire());
                }
            }
            if ($decompteTask->getMtCumulMois() >= 0 && $decompteTask->getMtPrevueProjetExec() > 0) {
                $decompteTask->setPourcentRealisation(round($decompteTask->getMtCumulMois() * 100 / $decompteTask->getMtPrevueProjetExec(), 2));
            } else {
                $decompteTask->setPourcentRealisation(0);
            }
        }
        return $decompteTask;
    }

    public function updateDecompte(Decompte $decompte) {
        $repositoryDecompte = $this->em->getRepository('OGIVEProjectBundle:Decompte');
        $decomptePrec = $this->em->getRepository('OGIVEProjectBundle:Decompte')->getDecomptePrecByMonthAndProject($decompte->getMonthNumber(), $decompte->getProject()->getId());
        $mtPrevueMarcheDcpt = 0;
        $mtPrevueProjetExecDcpt = 0;
        $mtCumulMoisPrecDcpt = 0;
        $mtMoisDcpt = 0;
        $mtCumulMoisDcpt = 0;
        $decompteTasks = $decompte->getDecompteTasks();
        foreach ($decompteTasks as $decompteTask) {
            $decompteTask = $this->updateDecompteTask($decompteTask, $decomptePrec);
            if ($decompteTask->getMtPrevueMarche()) {
                $mtPrevueMarcheDcpt += $decompteTask->getMtPrevueMarche();
            }
            if ($decompteTask->getMtPrevueProjetExec()) {
                $mtPrevueProjetExecDcpt += $decompteTask->getMtPrevueProjetExec();
            }
            if ($decompteTask->getMtCumulMoisPrec()) {
                $mtCumulMoisPrecDcpt += $decompteTask->getMtCumulMoisPrec();
            }
            if ($decompteTask->getMtCumulMois()) {
                $mtCumulMoisDcpt += $decompteTask->getMtCumulMois();
            }
            if ($decompteTask->getMtMois()) {
                $mtMoisDcpt += $decompteTask->getMtMois();
            }
        }
        $decompte->setUpdatedUser($this->tokenStorage->getToken()->getUser());
        $decompte->setMtPrevueMarche($mtPrevueMarcheDcpt);
        $decompte->setMtPrevueProjetExec($mtPrevueProjetExecDcpt);
        $decompte->setMtCumulMoisPrec($mtCumulMoisPrecDcpt);
        $decompte->setMtCumulMois($mtCumulMoisDcpt);
        $decompte->setMtMois($mtMoisDcpt);
        if ($decompte->getMtCumulMois() >= 0 && $decompte->getMtPrevueProjetExec() > 0) {
            $decompte->setPourcentRealisation(round($decompte->getMtCumulMois() * 100 / $decompte->getMtPrevueProjetExec(), 2));
        } else {
            $decompte->setPourcentRealisation(0);
        }
        $this->updateDecompteAttributes($decompte, $decomptePrec);
        return $repositoryDecompte->updateDecompte($decompte);
    }

    public function updateDecompteAttributes(Decompte $decompte, Decompte $decomptePrec = null) {
        $this->updateMontantTVAOfDecompte($decompte);
        $this->updateMontantIROfDecompte($decompte);
        $this->updateMontantNetAPercevoirOfDecompte($decompte);
        $this->updateMontantTTCOfDecompte($decompte);
        $this->updateMontantTotalHTVA($decompte);
        $this->updateAvanceDemarrage($decompte, $decomptePrec);
        $this->updatePrestationsWithAIR($decompte);
        $this->updateRetenueGarantie($decompte, $decomptePrec);
        $this->updateRemboursementAvance($decompte, $decomptePrec);
        $this->updatePenalite($decompte, $decomptePrec);
        $this->updateTotalPaiements($decompte);
        $this->updateTaxes($decompte);
        $this->updateAcompteAMandater($decompte);
    }

    public function addOrUpdateNewDecompteTask(Task $task, Decompte $decompte = null, DecompteTask $parentDecompteTask = null) {
        $repositoryDecompteTask = $this->em->getRepository('OGIVEProjectBundle:DecompteTask');
        $repositoryDecompte = $this->em->getRepository('OGIVEProjectBundle:Decompte');
        $decompteTask = $repositoryDecompteTask->getDecompteTaskByDecompteAndTask($task->getId(), $decompte->getId());
        if ($decompteTask == null) {
            $decomptePrec = $repositoryDecompte->getDecomptePrecByMonthAndProject($decompte->getMonthNumber(), $decompte->getProject()->getId());
            $decompteTask = $this->createDecompteTaskUsingTask($task, $decompte, $decomptePrec);
            $decompteTask->setNom($task->getNom());
            $decompteTask->setNumero($task->getNumero());
            $decompteTask->setTask($task);
            if ($parentDecompteTask) {
                $decompteTask->setParentDecompteTask($parentDecompteTask);
                $parentDecompteTask->addSubDecompteTask($decompteTask);
                $repositoryDecompteTask->updateDecompteTask($parentDecompteTask);
            } else {
                $decompteTask->setParentDecompteTask(null);
                $decompteTask->setDecompte($decompte);
                $decompte->addDecompteTask($decompteTask);
            }
        } else {
            $decompteTask->setNom($task->getNom());
            $decompteTask->setNumero($task->getNumero());
            $decompteTask->setUnite($task->getUnite());
            $decompteTask->setPrixUnitaire($task->getPrixUnitaire());
            $decompteTask->setQtePrevueMarche($task->getQtePrevueMarche());
            $decompteTask->setQtePrevueProjetExec($task->getQtePrevueProjetExec());
            $decompteTask->setParentDecompteTask($parentDecompteTask);

            $subTasks = $task->getSubTasks();
            if (!empty($subTasks)) {
                foreach ($subTasks as $subTask) {
                    $this->addOrUpdateNewDecompteTask($subTask, $decompte, $decompteTask);
                }
            }
            $repositoryDecompteTask->updateDecompteTask($decompteTask);
        }
    }

//    public function findNonExistTaskInDecompte(Task $task, Decompte $decompte) {
//        $repositoryDecompteTask = $this->em->getRepository('OGIVEProjectBundle:DecompteTask');
//
//        $subTasks = $task->getSubTasks();
//        $subTaksNumber = count($subTasks);
//        $i = 0;
//        if ($subTaksNumber > 0) {
//            $decompteTask = $repositoryDecompteTask->getDecompteTaskByDecompteAndTask($subTasks[$i]->getId(), $decompte->getId());
//            while ($decompteTask != null && $i < $subTaksNumber) {
//                
//            }
//        }
//    }

    public function updateMontantTotalHTVA(Decompte $decompte) {
        if ($decompte->getProject() && is_numeric($decompte->getProject()->getMtAvenant())) {
            $decompte->setMtTotalMarcheHTVA($decompte->getMtPrevueMarche() + $decompte->getProject()->getMtAvenant());
        } else {
            $decompte->setMtTotalMarcheHTVA($decompte->getMtPrevueMarche());
        }
    }

    public function updateMontantTVAOfDecompte(Decompte $decompte) {
        $tva = 19.5;
        if ($decompte->getMtPrevueMarche() >= 0) {
            $decompte->setMtPrevueMarcheTVA(ceil($decompte->getMtPrevueMarche() * $tva / 100));
        }
        if ($decompte->getMtPrevueProjetExec() >= 0) {
            $decompte->setMtPrevueProjetExecTVA(ceil($decompte->getMtPrevueProjetExec() * $tva / 100));
        }
        if ($decompte->getMtMois() >= 0) {
            $decompte->setMtMoisTVA(ceil($decompte->getMtMois() * $tva / 100));
        }
        if ($decompte->getMtCumulMois() >= 0) {
            $decompte->setMtCumulMoisTVA(ceil($decompte->getMtCumulMois() * $tva / 100));
        }
        if ($decompte->getMtCumulMoisPrec() >= 0) {
            $decompte->setMtCumulMoisPrecTVA(ceil($decompte->getMtCumulMoisPrec() * $tva / 100));
        }
    }

    public function updateMontantIROfDecompte(Decompte $decompte) {
        $ir = 2.2;
        if ($decompte->getMtPrevueMarche() >= 0) {
            $decompte->setMtPrevueMarcheIR(ceil($decompte->getMtPrevueMarche() * $ir / 100));
        }
        if ($decompte->getMtPrevueProjetExec() >= 0) {
            $decompte->setMtPrevueProjetExecIR(ceil($decompte->getMtPrevueProjetExec() * $ir / 100));
        }
        if ($decompte->getMtMois() >= 0) {
            $decompte->setMtMoisIR(ceil($decompte->getMtMois() * $ir / 100));
        }
        if ($decompte->getMtCumulMois() >= 0) {
            $decompte->setMtCumulMoisIR(ceil($decompte->getMtCumulMois() * $ir / 100));
        }
        if ($decompte->getMtCumulMoisPrec() >= 0) {
            $decompte->setMtCumulMoisPrecIR(ceil($decompte->getMtCumulMoisPrec() * $ir / 100));
        }
    }

    public function updateMontantNetAPercevoirOfDecompte(Decompte $decompte) {
        if ($decompte->getMtPrevueMarche() >= 0 && $decompte->getMtPrevueMarcheIR() >= 0) {
            $decompte->setMtPrevueMarcheNetAPercevoir($decompte->getMtPrevueMarche() - $decompte->getMtPrevueMarcheIR());
        }
        if ($decompte->getMtPrevueProjetExec() >= 0 && $decompte->getMtPrevueProjetExecIR() >= 0) {
            $decompte->setMtPrevueProjetExecNetAPercevoir($decompte->getMtPrevueProjetExec() - $decompte->getMtPrevueProjetExecIR());
        }
        if ($decompte->getMtMois() >= 0 && $decompte->getMtMoisIR() >= 0) {
            $decompte->setMtMoisNetAPercevoir($decompte->getMtMois() - $decompte->getMtMoisIR());
        }
        if ($decompte->getMtCumulMois() >= 0 && $decompte->getMtCumulMoisIR() >= 0) {
            $decompte->setMtCumulMoisNetAPercevoir($decompte->getMtCumulMois() - $decompte->getMtCumulMoisIR());
        }
        if ($decompte->getMtCumulMoisPrec() >= 0 && $decompte->getMtCumulMoisPrecIR() >= 0) {
            $decompte->setMtCumulMoisPrecNetAPercevoir($decompte->getMtCumulMoisPrec() - $decompte->getMtCumulMoisPrecIR());
        }
    }

    public function updateMontantTTCOfDecompte(Decompte $decompte) {
        if ($decompte->getMtPrevueMarche() >= 0 && $decompte->getMtPrevueMarcheTVA() >= 0) {
            $decompte->setMtPrevueMarcheTTC($decompte->getMtPrevueMarche() + $decompte->getMtPrevueMarcheTVA());
        }
        if ($decompte->getMtPrevueProjetExec() >= 0 && $decompte->getMtPrevueProjetExecTVA() >= 0) {
            $decompte->setMtPrevueProjetExecTTC($decompte->getMtPrevueProjetExec() + $decompte->getMtPrevueProjetExecTVA());
        }
        if ($decompte->getMtMois() >= 0 && $decompte->getMtMoisTVA() >= 0) {
            $decompte->setMtMoisTTC($decompte->getMtMois() + $decompte->getMtMoisTVA());
        }
        if ($decompte->getMtCumulMois() >= 0 && $decompte->getMtCumulMoisTVA() >= 0) {
            $decompte->setMtCumulMoisTTC($decompte->getMtCumulMois() + $decompte->getMtCumulMoisTVA());
        }
        if ($decompte->getMtCumulMoisPrec() >= 0 && $decompte->getMtCumulMoisPrecTTC() >= 0) {
            $decompte->setMtCumulMoisPrecTTC($decompte->getMtCumulMoisPrec() + $decompte->getMtCumulMoisPrecTVA());
        }
    }

    public function updateAvanceDemarrage(Decompte $decompte, Decompte $decomptePrec = null) {
        if (!is_null($decomptePrec)) {
            $decompte->setMtAvanceDemarragePrec($decomptePrec->getMtAvanceDemarrage());
        } else {
            $decompte->setMtAvanceDemarragePrec(0);
        }
        if (is_null($decompte->getMtAvanceDemarrage())) {
            $decompte->setMtAvanceDemarrage(0);
        }
        if (is_numeric($decompte->getMtAvanceDemarrage()) && is_numeric($decompte->getMtAvanceDemarragePrec())) {
            $decompte->setMtAvanceDemarrageACD($decompte->getMtAvanceDemarrage() - $decompte->getMtAvanceDemarragePrec());
        } else {
            $decompte->setMtAvanceDemarrageACD(0);
        }
    }

    public function updatePrestationsWithAIR(Decompte $decompte) {
        if (is_numeric($decompte->getMtCumulMois()) && is_numeric($decompte->getMtCumulMoisPrec())) {
            $decompte->setMtPrestationsWithAirACD($decompte->getMtCumulMois() - $decompte->getMtCumulMoisPrec());
        } else {
            $decompte->setMtPrestationsWithAirACD(0);
        }
    }

    public function updateRetenueGarantie(Decompte $decompte) {
        $retenueGarantie = 10;
        if (is_numeric($decompte->getMtCumulMois())) {
            $decompte->setMtRetenueGarantie(ceil($decompte->getMtCumulMois() * $retenueGarantie / 100));
        } else {
            $decompte->setMtRetenueGarantie(0);
        }

        if (is_numeric($decompte->getMtCumulMoisPrec())) {
            $decompte->setMtRetenueGarantiePrec(ceil($decompte->getMtCumulMoisPrec() * $retenueGarantie / 100));
        } else {
            $decompte->setMtRetenueGarantiePrec(0);
        }

        if (is_numeric($decompte->getMtRetenueGarantie()) && is_numeric($decompte->getMtRetenueGarantiePrec())) {
            $decompte->setMtRetenueGarantieACD($decompte->getMtRetenueGarantie() - $decompte->getMtRetenueGarantiePrec());
        } else {
            $decompte->setMtRetenueGarantieACD(0);
        }
    }

    public function updateRemboursementAvance(Decompte $decompte, Decompte $decomptePrec = null) {
        if (!is_null($decomptePrec)) {
            $decompte->setMtRemboursementAvancePrec($decomptePrec->getMtRemboursementAvance());
        } else {
            $decompte->setMtRemboursementAvancePrec(0);
        }
        if (is_null($decompte->getMtRemboursementAvance())) {
            $decompte->setMtRemboursementAvance(0);
        }
//        if ($decompte->getProject()->getAvanceDemarrage()) {
//            $avanceDemarrage = $decompte->getProject()->getAvanceDemarrage();
//            $mtAvanceDemarrage = $decompte->getMtPrevueMarche() * $avanceDemarrage;
//            $decompte->setMtAvanceDemarrage($mtAvanceDemarrage);
//            if ($decompte->getPourcentRealisation() > 40 && $decompte->getPourcentRealisation() < 80) {
//                if ($decomptePrec && $decomptePrec->getMtRemboursementAvance() > 0) {
//                    $decompte->setMtRemboursementAvance($mtAvanceDemarrage - $decomptePrec->getMtRemboursementAvance());
//                    $decompte->setMtRemboursementAvance(ceil($decompte->getMtAvanceDemarrage() * 0.5));
//                }else{
//                    $decompte->setMtRemboursementAvance(ceil($decompte->getMtAvanceDemarrage() * 0.5));
//                }
//            } elseif ($decompte->getPourcentRealisation() >= 80) {
//                if ($decomptePrec && $decomptePrec->getMtRemboursementAvance() >= 0) {
//                    $decompte->setMtRemboursementAvance($mtAvanceDemarrage - $decomptePrec->getMtAvanceDemarrage());
//                }else{
//                    $decompte->setMtRemboursementAvance($mtAvanceDemarrage);
//                }
//            }
//        }
        if (is_numeric($decompte->getMtRemboursementAvance()) && is_numeric($decompte->getMtRemboursementAvancePrec())) {
            $decompte->setMtRemboursementAvanceACD($decompte->getMtRemboursementAvance() - $decompte->getMtRemboursementAvancePrec());
        } else {
            $decompte->setMtRemboursementAvanceACD(0);
        }
    }

    public function updatePenalite(Decompte $decompte, Decompte $decomptePrec = null) {
        if (!is_null($decomptePrec)) {
            $decompte->setMtPenalitePrec($decomptePrec->getMtPenalite());
        } else {
            $decompte->setMtPenalitePrec(0);
        }
        if (is_null($decompte->getMtPenalite())) {
            $decompte->setMtPenalite(0);
        }

        $decompte->setMtPenaliteACD($decompte->getMtPenalite());
    }

    public function updateTotalPaiements(Decompte $decompte) {

        if (is_numeric($decompte->getMtCumulMois()) && is_numeric($decompte->getMtAvanceDemarrage()) && is_numeric($decompte->getMtRetenueGarantie()) && is_numeric($decompte->getMtRemboursementAvance()) && is_numeric($decompte->getMtPenalite())) {
            $decompte->setMtTotalPaiements($decompte->getMtCumulMois() + $decompte->getMtAvanceDemarrage() - $decompte->getMtRetenueGarantie() - $decompte->getMtRemboursementAvance() - $decompte->getMtPenalite());
        } else {
            $decompte->setMtTotalPaiements(0);
        }

        if (is_numeric($decompte->getMtCumulMoisPrec()) && is_numeric($decompte->getMtAvanceDemarragePrec()) && is_numeric($decompte->getMtRetenueGarantiePrec()) && is_numeric($decompte->getMtRemboursementAvancePrec()) && is_numeric($decompte->getMtPenalitePrec())) {
            $decompte->setMtTotalPaiementsPrec($decompte->getMtCumulMoisPrec() + $decompte->getMtAvanceDemarragePrec() - $decompte->getMtRetenueGarantiePrec() - $decompte->getMtRemboursementAvancePrec() - $decompte->getMtPenalitePrec());
        } else {
            $decompte->setMtTotalPaiementsPrec(0);
        }

        if (is_numeric($decompte->getMtPrestationsWithAirACD()) && is_numeric($decompte->getMtAvanceDemarrageACD()) && is_numeric($decompte->getMtRetenueGarantieACD()) && is_numeric($decompte->getMtRemboursementAvanceACD()) && is_numeric($decompte->getMtPenaliteACD())) {
            $decompte->setMtTotalPaiementsACD($decompte->getMtPrestationsWithAirACD() + $decompte->getMtAvanceDemarrageACD() - $decompte->getMtRetenueGarantieACD() - $decompte->getMtRemboursementAvanceACD() - $decompte->getMtPenaliteACD());
        } else {
            $decompte->setMtTotalPaiementsACD(0);
        }
    }

    public function updateTaxes(Decompte $decompte) {
        if (is_numeric($decompte->getMtCumulMoisIR()) && is_numeric($decompte->getMtCumulMoisPrecIR())) {
            $decompte->setMtTaxesAIRACD($decompte->getMtCumulMoisIR() - $decompte->getMtCumulMoisPrecIR());
        } else {
            $decompte->setMtTaxesAIRACD(0);
        }

        if (is_numeric($decompte->getMtCumulMoisTVA()) && is_numeric($decompte->getMtCumulMoisPrecTVA())) {
            $decompte->setMtTaxesTVAACD($decompte->getMtCumulMoisTVA() - $decompte->getMtCumulMoisPrecTVA());
        } else {
            $decompte->setMtTaxesTVAACD(0);
        }
    }

    public function updateAcompteAMandater(Decompte $decompte) {
        $decompte->setMtACM($decompte->getMtTotalPaiementsACD() - $decompte->getMtTaxesAIRACD());
    }

    public function getTheExactNumberOfDecompteTasks($decompteTasks) {
        $numberDecompteTasks = 0;
        foreach ($decompteTasks as $decompteTask) {
            if ($decompteTask->getPourcentRealisation() && $decompteTask->getPourcentRealisation() >= 0) {
                $numberDecompteTasks++;
            }
        }
        return $numberDecompteTasks;
    }

    public function updateDecomptesDuringTaskUpdatingOrAdding(Task $task) {
        $repositoryDecompte = $this->em->getRepository('OGIVEProjectBundle:Decompte');
        $repositoryDecompteTask = $this->em->getRepository('OGIVEProjectBundle:DecompteTask');
        $decomptes = $repositoryDecompte->getAll(null, null, null, $task->getProjectTask()->getId());
        foreach ($decomptes as $decompte) {
            $parentTask = $task->getParentTask();
            if ($parentTask) {
                $parentDecompteTask = $repositoryDecompteTask->getDecompteTaskByDecompteAndTask($parentTask->getId(), $decompte->getId());
            } else {
                $parentDecompteTask = null;
            }
            $this->addOrUpdateNewDecompteTask($task, $decompte, $parentDecompteTask);
            $this->updateDecompte($decompte);
        }
    }

    public function updateNextDecomptesOfProject(Decompte $decompte) {
        $repositoryDecompte = $this->em->getRepository('OGIVEProjectBundle:Decompte');
        $decomptes = $repositoryDecompte->getNextDecomptesOfProject($decompte->getMonthNumber(), $decompte->getProject()->getId());
        if ($decomptes) {
            foreach ($decomptes as $decompte) {
                $this->updateDecompte($decompte);
            }
        }
    }

    public function updateNextDecomptesOfProjectForDecompteDeleted(Decompte $decompte) {
        $repositoryDecompte = $this->em->getRepository('OGIVEProjectBundle:Decompte');
        $decomptes = $repositoryDecompte->getNextDecomptesOfProject($decompte->getMonthNumber(), $decompte->getProject()->getId());
        if ($decomptes) {
            $monthNumber = $decompte->getMonthNumber();
            foreach ($decomptes as $decompte) {
                $decompte->setMonthNumber($monthNumber);
                $this->updateDecompte($decompte);
                $monthNumber++;
            }
        }
    }

    public function updateAllDecomptes(Project $project) {
        $repositoryDecompte = $this->em->getRepository('OGIVEProjectBundle:Decompte');
        $decomptes = $repositoryDecompte->getAll(null, null, null, $project->getId());
        foreach ($decomptes as $decompte) {
            $this->updateDecompte($decompte);
        }
    }

}
