<?php

namespace OGIVE\ProjectBundle\Services;

use Doctrine\ORM\EntityManager;
use OGIVE\ProjectBundle\Entity\Decompte;
use OGIVE\ProjectBundle\Entity\DecompteTask;
use OGIVE\ProjectBundle\Entity\DecompteTotal;
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
                $mtPrevueMarche = is_numeric($subDecompteTask->getMtPrevueMarche()) ? $mtPrevueMarche + $subDecompteTask->getMtPrevueMarche() : $mtPrevueMarche;
                $mtPrevueProjetExec = is_numeric($subDecompteTask->getMtPrevueProjetExec()) ? $mtPrevueProjetExec + $subDecompteTask->getMtPrevueProjetExec() : $mtPrevueProjetExec;
                $mtMois = is_numeric($subDecompteTask->getMtMois()) ? $mtMois + $subDecompteTask->getMtMois() : $mtMois;
                $mtCumulMoisPrec = is_numeric($subDecompteTask->getMtCumulMoisPrec()) ? $mtCumulMoisPrec + $subDecompteTask->getMtCumulMoisPrec() : $mtCumulMoisPrec;
                $mtCumulMois = is_numeric($subDecompteTask->getMtCumulMois()) ? $mtCumulMois + $subDecompteTask->getMtCumulMois() : $mtCumulMois;
            }
            $decompteTask->setMtPrevueMarche($mtPrevueMarche);
            $decompteTask->setMtPrevueProjetExec($mtPrevueProjetExec);
            $decompteTask->setMtMois($mtMois);
            $decompteTask->setMtCumulMoisPrec($mtCumulMoisPrec);
            $decompteTask->setMtCumulMois($mtCumulMois);
            if (is_numeric($decompteTask->getMtCumulMois()) && $decompteTask->getMtPrevueProjetExec() > 0) {
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
            if (is_numeric($decompteTask->getPrixUnitaire())) {
                if (is_numeric($decompteTask->getQtePrevueMarche())) {
                    $decompteTask->setMtPrevueMarche($decompteTask->getQtePrevueMarche() * $decompteTask->getPrixUnitaire());
                }
                if (is_numeric($decompteTask->getQtePrevueProjetExec())) {
                    $decompteTask->setMtPrevueProjetExec($decompteTask->getQtePrevueProjetExec() * $decompteTask->getPrixUnitaire());
                }
                if (is_numeric($decompteTask->getQteCumulMoisPrec())) {
                    $decompteTask->setMtCumulMoisPrec($decompteTask->getPrixUnitaire() * $decompteTask->getQteCumulMoisPrec());
                }
                if (is_numeric($decompteTask->getQteMois())) {
                    $decompteTask->setMtMois($decompteTask->getQteMois() * $decompteTask->getPrixUnitaire());
                }
                if (is_numeric($decompteTask->getQteCumulMois())) {
                    $decompteTask->setMtCumulMois($decompteTask->getPrixUnitaire() * $decompteTask->getQteCumulMois());
                }
            }
            if (is_numeric($decompteTask->getMtCumulMois()) && $decompteTask->getMtPrevueProjetExec() > 0) {
                $decompteTask->setPourcentRealisation(round($decompteTask->getMtCumulMois() * 100 / $decompteTask->getMtPrevueProjetExec(), 2));
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
                $mtPrevueMarche = is_numeric($subDecompteTask->getMtPrevueMarche()) ? $mtPrevueMarche + $subDecompteTask->getMtPrevueMarche() : $mtPrevueMarche;
                $mtPrevueProjetExec = is_numeric($subDecompteTask->getMtPrevueProjetExec()) ? $mtPrevueProjetExec + $subDecompteTask->getMtPrevueProjetExec() : $mtPrevueProjetExec;
                $mtMois = is_numeric($subDecompteTask->getMtMois()) ? $mtMois + $subDecompteTask->getMtMois() : $mtMois;
                $mtCumulMoisPrec = is_numeric($subDecompteTask->getMtCumulMoisPrec()) ? $mtCumulMoisPrec + $subDecompteTask->getMtCumulMoisPrec() : $mtCumulMoisPrec;
                $mtCumulMois = is_numeric($subDecompteTask->getMtCumulMois()) ? $mtCumulMois + $subDecompteTask->getMtCumulMois() : $mtCumulMois;
            }
            $decompteTask->setMtPrevueMarche($mtPrevueMarche);
            $decompteTask->setMtPrevueProjetExec($mtPrevueProjetExec);
            $decompteTask->setMtMois($mtMois);
            $decompteTask->setMtCumulMoisPrec($mtCumulMoisPrec);
            $decompteTask->setMtCumulMois($mtCumulMois);
            if (is_numeric($decompteTask->getMtCumulMois()) && $decompteTask->getMtPrevueProjetExec() > 0) {
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
            if (is_numeric($decompteTask->getPrixUnitaire())) {
                if (is_numeric($decompteTask->getQtePrevueMarche())) {
                    $decompteTask->setMtPrevueMarche($decompteTask->getQtePrevueMarche() * $decompteTask->getPrixUnitaire());
                }
                if (is_numeric($decompteTask->getQtePrevueProjetExec())) {
                    $decompteTask->setMtPrevueProjetExec($decompteTask->getQtePrevueProjetExec() * $decompteTask->getPrixUnitaire());
                }
                if (is_numeric($decompteTask->getQteCumulMoisPrec())) {
                    $decompteTask->setMtCumulMoisPrec($decompteTask->getPrixUnitaire() * $decompteTask->getQteCumulMoisPrec());
                }
                if (is_numeric($decompteTask->getQteMois())) {
                    $decompteTask->setMtMois($decompteTask->getQteMois() * $decompteTask->getPrixUnitaire());
                }
                if (is_numeric($decompteTask->getQteCumulMois())) {
                    $decompteTask->setMtCumulMois($decompteTask->getPrixUnitaire() * $decompteTask->getQteCumulMois());
                }
            }
            if (is_numeric($decompteTask->getMtCumulMois()) && $decompteTask->getMtPrevueProjetExec() > 0) {
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
            if (is_numeric($decompteTask->getMtPrevueMarche())) {
                $mtPrevueMarcheDcpt += $decompteTask->getMtPrevueMarche();
            }
            if (is_numeric($decompteTask->getMtPrevueProjetExec())) {
                $mtPrevueProjetExecDcpt += $decompteTask->getMtPrevueProjetExec();
            }
            if (is_numeric($decompteTask->getMtCumulMoisPrec())) {
                $mtCumulMoisPrecDcpt += $decompteTask->getMtCumulMoisPrec();
            }
            if (is_numeric($decompteTask->getMtCumulMois())) {
                $mtCumulMoisDcpt += $decompteTask->getMtCumulMois();
            }
            if (is_numeric($decompteTask->getMtMois())) {
                $mtMoisDcpt += $decompteTask->getMtMois();
            }
        }
        $decompte->setUpdatedUser($this->tokenStorage->getToken()->getUser());
        $decompte->setMtPrevueMarche($mtPrevueMarcheDcpt);
        $decompte->setMtPrevueProjetExec($mtPrevueProjetExecDcpt);
        $decompte->setMtCumulMoisPrec($mtCumulMoisPrecDcpt);
        $decompte->setMtCumulMois($mtCumulMoisDcpt);
        $decompte->setMtMois($mtMoisDcpt);
        if (is_numeric($decompte->getMtCumulMois()) && $decompte->getMtPrevueProjetExec() > 0) {
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
        if (is_numeric($decompte->getMtPrevueMarche())) {
            $decompte->setMtPrevueMarcheTVA(ceil($decompte->getMtPrevueMarche() * $tva / 100));
        }
        if (is_numeric($decompte->getMtPrevueProjetExec())) {
            $decompte->setMtPrevueProjetExecTVA(ceil($decompte->getMtPrevueProjetExec() * $tva / 100));
        }
        if (is_numeric($decompte->getMtMois())) {
            $decompte->setMtMoisTVA(ceil($decompte->getMtMois() * $tva / 100));
        }
        if (is_numeric($decompte->getMtCumulMois())) {
            $decompte->setMtCumulMoisTVA(ceil($decompte->getMtCumulMois() * $tva / 100));
        }
        if (is_numeric($decompte->getMtCumulMoisPrec())) {
            $decompte->setMtCumulMoisPrecTVA(ceil($decompte->getMtCumulMoisPrec() * $tva / 100));
        }
    }

    public function updateMontantIROfDecompte(Decompte $decompte) {
        $ir = 2.2;
        if (is_numeric($decompte->getMtPrevueMarche())) {
            $decompte->setMtPrevueMarcheIR(ceil($decompte->getMtPrevueMarche() * $ir / 100));
        }
        if (is_numeric($decompte->getMtPrevueProjetExec())) {
            $decompte->setMtPrevueProjetExecIR(ceil($decompte->getMtPrevueProjetExec() * $ir / 100));
        }
        if (is_numeric($decompte->getMtMois())) {
            $decompte->setMtMoisIR(ceil($decompte->getMtMois() * $ir / 100));
        }
        if (is_numeric($decompte->getMtCumulMois())) {
            $decompte->setMtCumulMoisIR(ceil($decompte->getMtCumulMois() * $ir / 100));
        }
        if (is_numeric($decompte->getMtCumulMoisPrec())) {
            $decompte->setMtCumulMoisPrecIR(ceil($decompte->getMtCumulMoisPrec() * $ir / 100));
        }
    }

    public function updateMontantNetAPercevoirOfDecompte(Decompte $decompte) {
        if (is_numeric($decompte->getMtPrevueMarche()) && is_numeric($decompte->getMtPrevueMarcheIR())) {
            $decompte->setMtPrevueMarcheNetAPercevoir($decompte->getMtPrevueMarche() - $decompte->getMtPrevueMarcheIR());
        }
        if (is_numeric($decompte->getMtPrevueProjetExec()) && is_numeric($decompte->getMtPrevueProjetExecIR())) {
            $decompte->setMtPrevueProjetExecNetAPercevoir($decompte->getMtPrevueProjetExec() - $decompte->getMtPrevueProjetExecIR());
        }
        if (is_numeric($decompte->getMtMois()) && is_numeric($decompte->getMtMoisIR())) {
            $decompte->setMtMoisNetAPercevoir($decompte->getMtMois() - $decompte->getMtMoisIR());
        }
        if (is_numeric($decompte->getMtCumulMois()) && is_numeric($decompte->getMtCumulMoisIR())) {
            $decompte->setMtCumulMoisNetAPercevoir($decompte->getMtCumulMois() - $decompte->getMtCumulMoisIR());
        }
        if (is_numeric($decompte->getMtCumulMoisPrec()) && is_numeric($decompte->getMtCumulMoisPrecIR())) {
            $decompte->setMtCumulMoisPrecNetAPercevoir($decompte->getMtCumulMoisPrec() - $decompte->getMtCumulMoisPrecIR());
        }
    }

    public function updateMontantTTCOfDecompte(Decompte $decompte) {
        if (is_numeric($decompte->getMtPrevueMarche()) && is_numeric($decompte->getMtPrevueMarcheTVA())) {
            $decompte->setMtPrevueMarcheTTC($decompte->getMtPrevueMarche() + $decompte->getMtPrevueMarcheTVA());
        }
        if (is_numeric($decompte->getMtPrevueProjetExec()) && is_numeric($decompte->getMtPrevueProjetExecTVA())) {
            $decompte->setMtPrevueProjetExecTTC($decompte->getMtPrevueProjetExec() + $decompte->getMtPrevueProjetExecTVA());
        }
        if (is_numeric($decompte->getMtMois()) && is_numeric($decompte->getMtMoisTVA())) {
            $decompte->setMtMoisTTC($decompte->getMtMois() + $decompte->getMtMoisTVA());
        }
        if (is_numeric($decompte->getMtCumulMois()) && is_numeric($decompte->getMtCumulMoisTVA())) {
            $decompte->setMtCumulMoisTTC($decompte->getMtCumulMois() + $decompte->getMtCumulMoisTVA());
        }
        if (is_numeric($decompte->getMtCumulMoisPrec()) && is_numeric($decompte->getMtCumulMoisPrecTVA())) {
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
            $decompte->setMtCumulRemboursementAvance($decomptePrec->getMtCumulRemboursementAvance());
        } else {
            $decompte->setMtRemboursementAvancePrec(0);
            $decompte->setMtCumulRemboursementAvance(0);
        }
        $decompte->setMtCumulRemboursementAvance($decompte->getMtCumulRemboursementAvance() + $decompte->getMtRemboursementAvance());
        if (is_null($decompte->getMtRemboursementAvance())) {
            $decompte->setMtRemboursementAvance(0);
        }
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
            if ($decompteTask->getPourcentRealisation() && is_numeric($decompteTask->getPourcentRealisation())) {
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

    public function updateDecompteTotalOfProject(Project $project) {
        $repositoryDecompte = $this->em->getRepository('OGIVEProjectBundle:Decompte');
        $decomptes = $repositoryDecompte->getAll(null, null, null, $project->getId());
        $decompteTotal = $project->getDecompteTotal();
        if (is_null($decompteTotal)) {
            foreach ($decomptes as $decompte) {
                $decompte->setDecompteTotal($decompteTotal);
                $decompte = $repositoryDecompte->updateDecompte($decompte);
                //$decompteTotal->addDecompte($decompte);
            }
            //return $project->setDecompteTotal($decompteTotal);
        }
    }

    public function exportDecompteToExcel(Decompte $decompte) {
        set_include_path(get_include_path() . PATH_SEPARATOR . "..");
        include_once("xlsxwriter.class.php");
        $writer = new \XLSXWriter();
        $writer->setAuthor("SOCIETE D'INGENIEURIE OGIVE");
        $writer->setTempDir(sys_get_temp_dir()); //set custom tempdir
        $header = array("string", "string", "string", "string", "string", "string", "string", "string", "string", "string", "string", "string", "string", "string", "string");
        $sheet_attachement = "ATTACHEMENT";
        $attachement_rows = array();
        $this->createAttachementRows($decompte, $attachement_rows);
        $writer->writeSheetHeader($sheet_attachement, $header, $suppress_header_row = true);
        foreach ($attachement_rows as $row) {
            $writer->writeSheetRow($sheet_attachement, $row);
        }
        if (!is_dir($this->getExportExcelRootDir())) {
            mkdir($this->getExportExcelRootDir(), $mode = 0777, $recursive = true);
        }
        $writer->writeToFile($this->getExportExcelRootDir() . '/decompte_mensuel_N°'.$decompte->getMonthNumber().'.xlsx');
    }

    public function createAttachementRows(Decompte $decompte, $rows) {
        $holder = $decompte->getProject()->getHolders()[0];
        $projectManager = $decompte->getProject()->getProjectManagers()[0];
        /* Header attachement for project informations */
        $rows[] = array("OBJET: ", "", "", "", "", "", $decompte->getProject()->getNumeroMarche(), "", "", "", "TITULAIRE: " . $holder->getNom(), "", "", "", "");
        $rows[] = array($decompte->getProject()->getSubject(), "", "", "", "", "", "", "", "", "", "B.P." . $holder->getCodePostal(), "", "", "", "");
        $rows[] = array("LOT N°" . $decompte->getProject()->getNumeroLot(), "", "", "", "", "", "", "", "", "", "Tel/Fax: " . $holder->getPhone() . "/" . $holder->getFaxNumber(), "", "", "", "");
        $rows[] = array("LIEU D'EXECUTION: " . $decompte->getProject()->getLieuExecution(), "", "", "", "", "", "", "", "", "", "RC N° " . $holder->getCodePostal(), "", "", "", "");
        $rows[] = array("REGION: " . $decompte->getProject()->getRegion(), "", "", "", "", "", "", "", "", "", "N° Contribuable: " . $holder->getNumeroContribuable(), "", "", "", "");
        $rows[] = array("DEPARTEMENT: " . $decompte->getProject()->getDepartement(), "", "", "", "", "", "", "", "", "", "N° compte bancaire: " . $holder->getNumeroCompteBancaire(), "", "", "", "");
        $rows[] = array("Notifié le, : " . $decompte->getProject()->getNotificationDate(), "", "", "", "", "", "", "", "", "", $holder->getNomBanque(), "", "", "", "");
        $rows[] = array("MISSION DE CONTROLE: " . $projectManager->getNom(), "", "", "", "", "", "", "", "", "", $holder->getNomBanque(), "", "", "", "");
        $rows[] = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
        $rows[] = array("ATTACHEMENT RELATIF AU DECOMPTE  PROVISOIRE  N°" . $decompte->getMonthNumber() . " DES TRAVAUX  REALISES AU " . $decompte->getEndDate(), "", "", "", "", "", "", "", "", "", "", "", "", "", "");
        /* Header attachement for tasks informations */
        $rows[] = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
        $rows[] = array("N°", "Désignation des ouvrages", "U", "P.U", "Quantité", "", "", "", "", "Montant[" . $decompte->getProject()->getProjectCostCurrency() . "]", "", "", "", "", "% réalisé");
        $rows[] = array("", "", "", "", "marché", "projet d'exec", "cumul préc.", "du mois", "cumul mois", "marché", "projet d'exec", "cumul préc.", "du mois", "cumul mois", "");
        /* content attachement for tasks informations */
        $decompteTasks = $decompte->getDecompteTasks();
        if ($decompteTasks && count($decompteTasks) > 0) {
            foreach ($decompteTasks as $decompteTask) {
                $this->addDecompteTaskRow($decompteTask, $rows);
            }
        }
        $rows[] = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
        /*Content of general recap*/
        $rows[] = array("", "RECAPITULATIF GENERAL", "", "", "", "", "", "", "", "", "", "", "", "", "");
        if ($decompteTasks && count($decompteTasks) > 0) {
            foreach ($decompteTasks as $decompteTask) {
                $rows[] = array($decompteTask->getNumero(), "LOT".$decompteTask->getNumero().": ", "", "", "", "", "", "", "", $decompteTask->getMtPrevueMarche(), $decompteTask->getMtPrevueProjetExec(), $decompteTask->getMtCumulMoisPrec(), $decompteTask->getMtMois(), $decompteTask->getMtCumulMois(), $decompteTask->getPourcentRealisation() . "%");
            }
        }
        /*Content of total general hors taxes */
        $rows[] = array("", "TOTAL GENERAL HORS TAXES", "", "", "", "", "", "", "", $decompte->getMtPrevueMarche(), $decompte->getMtPrevueProjetExec(), $decompte->getMtCumulMoisPrec(), $decompte->getMtMois(), $decompte->getMtCumulMois(), "");
        /*Content of tva */
        $rows[] = array("", "TVA: 19.25%", "", "", "", "", "", "", "", $decompte->getMtPrevueMarcheTVA(), $decompte->getMtPrevueProjetExecTVA(), $decompte->getMtCumulMoisPrecTVA(), $decompte->getMtMoisTVA(), $decompte->getMtCumulMoisTVA(), "");
        /*Content of IR */
        $rows[] = array("", "IR: 2.2%", "", "", "", "", "", "", "", $decompte->getMtPrevueMarcheIR(), $decompte->getMtPrevueProjetExecIR(), $decompte->getMtCumulMoisPrecIR(), $decompte->getMtMoisIR(), $decompte->getMtCumulMoisIR(), "");
        /*Content of net à percevoir */
        $rows[] = array("", "NET A PERCEVOIR", "", "", "", "", "", "", "", $decompte->getMtPrevueMarcheNetAPercevoir(), $decompte->getMtPrevueProjetExecNetAPercevoir(), $decompte->getMtCumulMoisPrecNetAPercevoir(), $decompte->getMtMoisNetAPercevoir(), $decompte->getMtCumulMoisNetAPercevoir(), "");
        /*Content of total TTC */
        $rows[] = array("", "TOTAL TTC", "", "", "", "", "", "", "", $decompte->getMtPrevueMarcheTTC(), $decompte->getMtPrevueProjetExecTTC(), $decompte->getMtCumulMoisPrecTTC(), $decompte->getMtMoisTTC(), $decompte->getMtCumulMoisTTC(), "");
    }

    public function addDecompteTaskRow(DecompteTask $decompteTask, $rows) {
        if ($decompteTask->getNumero() && $decompteTask->getUnite() && $decompteTask->getPrixUnitaire() && $decompteTask->getQtePrevueMarche() && $decompteTask->getQtePrevueProjetExec()) {
            $rows[] = array($decompteTask->getNumero(), $decompteTask->getNom(), $decompteTask->getUnite(), $decompteTask->getPrixUnitaire(), $decompteTask->getQtePrevueMarche(), $decompteTask->getQtePrevueProjetExec(), $decompteTask->getQteCumulMoisPrec(), $decompteTask->getQteMois(), $decompteTask->getQteCumulMois(), $decompteTask->getMtPrevueMarche(), $decompteTask->getMtPrevueProjetExec(), $decompteTask->getMtCumulMoisPrec(), $decompteTask->getMtMois(), $decompteTask->getMtCumulMois(), $decompteTask->getPourcentRealisation() . "%");
        } else {
            $rows[] = array("LOT" . $decompteTask->getNumero() . ": " . $decompteTask->getNom(), "", "", "", "", "", "", "", "", "", "", "", "", "", "");
        }
        $subDecompteTasks = $decompteTask->getSubDecompteTasks();
        if ($subDecompteTasks && count($subDecompteTasks) > 0) {
            foreach ($subDecompteTasks as $subDecompteTask) {
                $this->addDecompteTaskRow($subDecompteTask, $rows);
            }
            $rows[] = array("", "SOUS TOTAL DU LOT".$decompteTask->getNumero(), "", "", "", "", "", "", "", $decompteTask->getMtPrevueMarche(), $decompteTask->getMtPrevueProjetExec(), $decompteTask->getMtCumulMoisPrec(), $decompteTask->getMtMois(), $decompteTask->getMtCumulMois(), $decompteTask->getPourcentRealisation() . "%");
        }
    }
    
    public function getExportExcelRootDir() {
        return __DIR__ . '/../../../../web/exports/excel';
    }

}
