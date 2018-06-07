<?php

namespace OGIVE\ProjectBundle\Controller;

use OGIVE\ProjectBundle\Entity\Project;
use OGIVE\ProjectBundle\Entity\Decompte;
use OGIVE\ProjectBundle\Entity\DecompteValidation;
use OGIVE\ProjectBundle\Entity\DecompteTask;
use OGIVE\ProjectBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use OGIVE\ProjectBundle\Services\CommonService;
use Doctrine\Common\Collections\Collection;

/**
 * Decompte controller.
 * @Route(service="app.controller.decompte")
 */
class DecompteController extends Controller {

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{id}/decomptes/new", name="decompte_add_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getAddDecomptesAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $decompte_manager = $this->get('app.decompte_manager');
        $user = $this->getUser();
        $can_create_decompte = $decompte_manager->user_can_create_decompte($user, $project);
        if ($can_create_decompte) {
            $repositoryDecompte = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Decompte');
            $monthNumber = count($repositoryDecompte->findBy(array('project' => $project))) + 1;
            $decompte = new Decompte();
            $decompte->setMonthNumber($monthNumber);
            if ($project->getRemboursementAvanceOption() == 1) {
                $decompte->setRemboursementAvanceIntensity(50);
            } else {
                $decompte->setRemboursementAvanceIntensity(0);
            }
            $form = $this->createForm('OGIVE\ProjectBundle\Form\DecompteType', $decompte, array('method' => 'POST'));
            return $this->render('OGIVEProjectBundle:decompte:add.html.twig', array(
                        'project' => $project,
                        'form' => $form->createView(),
                        'tab' => 5,
                        'can_create_decompte' => $can_create_decompte
            ));
        } else {
            return $this->redirect($this->generateUrl('project_decomptes_get', array("id" => $project->getId())));
        }
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{idProject}/decomptes/{id}/update" , name="decompte_update_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getUpdateDecompteByIdAction(Decompte $decompte) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if (empty($decompte)) {
            
        }
        $decompte_manager = $this->get('app.decompte_manager');
        $user = $this->getUser();
        $can_update_decompte = $decompte_manager->user_can_update_decompte($user, $decompte->getProject());
        $can_submit_decompte_for_validation = $decompte_manager->user_can_submit_decompte_for_validation($user, $decompte);
        if ($can_update_decompte) {
            $form = $this->createForm('OGIVE\ProjectBundle\Form\DecompteType', $decompte, array('method' => 'PUT'));
            return $this->render('OGIVEProjectBundle:decompte:update-monthly-decompte.html.twig', array(
                        'project' => $decompte->getProject(),
                        'decompteTasks' => $decompte->getDecompteTasks(),
                        'decompte' => $decompte,
                        'tab' => 5,
                        'can_submit_decompte' => $can_submit_decompte_for_validation,
                        'form' => $form->createView()
            ));
        } else {
            return $this->redirect($this->generateUrl('decompte_show_get', array("id" => $decompte->getId(), "idProject" => $decompte->getProject()->getId())));
        }
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{id}/decompte-total" , name="project_decompte_total_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getDecompteTotalByProjectAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $decomptes = $em->getRepository('OGIVEProjectBundle:Decompte')->getAll(null, null, null, $project->getId());
        return $this->render('OGIVEProjectBundle:decompte:project-decompte-total.html.twig', array(
                    'project' => $project,
                    'tab' => 6,
                    'decomptes' => $decomptes
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{idProject}/decomptes/{id}/show" , name="decompte_show_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getShowDecompteByIdAction(Decompte $decompte) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $decompte_manager = $this->get('app.decompte_manager');
        $user = $this->getUser();
        $can_update_decompte = $decompte_manager->user_can_update_decompte($user, $decompte->getProject());
        $can_submit_decompte_for_validation = $decompte_manager->user_can_submit_decompte_for_validation($user, $decompte);
        $repositoryDecompte = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Decompte');
        if (empty($decompte)) {
            
        }
        $AllDecomptes = $repositoryDecompte->getAll(null, null, null, $decompte->getProject()->getId());
        $previousDecompte = $repositoryDecompte->getDecomptePrecByMonthAndProject($decompte->getMonthNumber(), $decompte->getProject()->getId());
        $nextDecompte = $repositoryDecompte->getDecompteNextByMonthAndProject($decompte->getMonthNumber(), $decompte->getProject()->getId());
        $form = $this->createForm('OGIVE\ProjectBundle\Form\DecompteType', $decompte, array('method' => 'PUT'));

        $decompteValidation = new \OGIVE\ProjectBundle\Entity\DecompteValidation();
        $formForValidation = $this->createForm('OGIVE\ProjectBundle\Form\DecompteValidationType', $decompteValidation, array('method' => 'POST'));
        return $this->render('OGIVEProjectBundle:decompte:monthly-decompte.html.twig', array(
                    'project' => $decompte->getProject(),
                    'allDecomptes' => $AllDecomptes,
                    'previousDecompte' => $previousDecompte,
                    'nextDecompte' => $nextDecompte,
                    'decompteTasks' => $decompte->getDecompteTasks(),
                    'decompte' => $decompte,
                    'tab' => 5,
                    'month' => $decompte->getMonthNumber(),
                    'can_submit_decompte' => $can_submit_decompte_for_validation,
                    'can_update_decompte' => $can_update_decompte,
                    'form' => $form->createView(),
                    'decompteValidations' => $decompte->getDecompteValidations(),
                    'formForValidation' => $formForValidation->createView()
        ));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Put("/validate-decompte/{id}", name="decompte_validation_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function putDecompteValidationAction(Request $request, DecompteValidation $decompteValidation) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryDecompteValidation = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:DecompteValidation');
        $repositoryDecompte = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Decompte');
        if ($request->isMethod("PUT")) {
            if ($decompteValidation->getDecompte()->getState() == $decompteValidation->getPriorityOrder()) {
                $decompteValidation->setState(2);
                $decompteValidation->setValidationDate(new \DateTime('now'));
                $decompteValidation->setValidationPlace($request->get("validation_place"));
                $decompteValidation->setCreatedUser($this->getUser());
                $repositoryDecompteValidation->updateDecompteValidation($decompteValidation);
                $repositoryDecompte->updateDecompte($decompteValidation->getDecompte()->setState($decompteValidation->getPriorityOrder() + 1));
                $view = View::create(["message" => "Votre décompte a été validé avec succès !"]);
                $view->setFormat('json');
            }else{
                return new JsonResponse(["success" => false, 'message' => "Vous n'êtes pas encore autorisé à valider ce décompte !"], Response::HTTP_BAD_REQUEST);
            }

            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => "Erreur lors de la soumission du décompte !"], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Post("/projects/{idProject}/decomptes/{id}/submit-for-validation", name="decompte_submit_for_validation_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postSubmitDecompteForValidationAction(Request $request, Decompte $decompte) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $decompte_manager = $this->get('app.decompte_manager');
        $currentUser = $this->getUser();
        $project_mail_service = $this->get("app.project_mail_service");
        $repositoryHolder = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Holder');
        if ($decompte_manager->user_can_submit_decompte_for_validation($currentUser, $decompte)) {
            $owner = $decompte->getProject()->getOwner();
            $holder = $repositoryHolder->getHolderByUser($currentUser->getId());
            $project_mail_service->sendSubmittedDecompteLink($decompte, $holder, $owner);
            $projectManagers = $decompte->getProject()->getProjectManagers();
            foreach ($projectManagers as $projectManager) {
                $project_mail_service->sendSubmittedDecompteLink($decompte, $holder, $projectManager);
            }
            $othersContributors = $decompte->getProject()->getOtherContributors();
            foreach ($othersContributors as $contributor) {
                $project_mail_service->sendSubmittedDecompteLink($decompte, $holder, $contributor);
            }
            $view = View::create(["message" => "Votre décompte a été soumis pour validation !"]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas le droit de soumettre ce décompte !"], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/projects/{id}/decomptes/new", name="decompte_add_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postDecomptesAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $decompte_manager = $this->get('app.decompte_manager');
        $repositoryDecompte = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Decompte');
        $month = 0;
        $decompte = new Decompte();
        $task = new \OGIVE\ProjectBundle\Entity\Task();
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $form = $this->createForm('OGIVE\ProjectBundle\Form\DecompteType', $decompte, array('method' => 'POST'));
        $form->handleRequest($request);
        $decompte->setProject($project);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($decompte->getMonthNumber() == null || $decompte->getMonthNumber() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre décompte est sans numero. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($decompte->getMonthName() == null || $decompte->getMonthName() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre décompte est sans désignation. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            $month = $decompte->getMonthNumber();
            $decompteMois = $em->getRepository('OGIVEProjectBundle:Decompte')->getDecompteByMonthAndProject($month, $project->getId());
            if ($decompteMois == null) {
                $mtPrevueMarcheDcpt = 0;
                $mtPrevueProjetExecDcpt = 0;
                $mtCumulMoisPrecDcpt = 0;
                $mtMoisDcpt = 0;
                $mtCumulMoisDcpt = 0;
                $tasks = $project->getTasks();
                $decomptePrec = $em->getRepository('OGIVEProjectBundle:Decompte')->getDecomptePrecByMonthAndProject($month, $project->getId());
                foreach ($tasks as $task) {
                    $decompteTask = $decompte_manager->createDecompteTaskUsingTask($task, $decompte, $decomptePrec);
                    $task->addDecompteTask($decompteTask);
                    $decompteTask->setDecompte($decompte);
                    $decompte->addDecompteTask($decompteTask);
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
                $decompte->setDecompteState(1);
                $decompte->setCreatedUser($user);
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
                if ($project->getRemboursementAvanceOption() == 1) {
                    $decompte->setRemboursementAvanceIntensity(50);
                }
                //***************gestion des pénalités du décompte ************************** */
                $penalities = $decompte->getPenalities();
                $mtPenalites = 0;
                foreach ($penalities as $penality) {
                    $penality->setDecompte($decompte);
                    $mtPenalites += $penality->getAmount();
                }
                if ($project->getRemboursementAvanceOption() == 1) {
                    $decompte->setRemboursementAvanceIntensity(50);
                } else {
                    if (is_null($decompte->getRemboursementAvanceIntensity())) {
                        $decompte->setRemboursementAvanceIntensity(0);
                    }
                }
                $decompte->setMtPenaliteACD($mtPenalites);
                $decompte_manager->updateDecompteAttributes($decompte, $decomptePrec);
                /*                 * ******************initialise a list of decompte validation *********************************************** */
                $decompte = $decompte_manager->initDecompteValidators($decompte);
                $decompte = $repositoryDecompte->saveDecompte($decompte);
                $view = View::create(["message" => 'Décompte créé avec succès. Vous serez redirigé dans bientôt, pour préciser les quantités mensuelles de vos prestations', 'id_project' => $project->getId(), 'id_decompte' => $decompte->getId()]);
                $view->setFormat('json');
                return $view;
            } else {
                return new JsonResponse(["success" => false, 'message' => 'Un décompte avec numero existe déjà !'], Response::HTTP_BAD_REQUEST);
            }
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes !'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Delete("/projects/{idProject}/decomptes/{id}/remove", name="decompte_delete", options={ "method_prefix" = false, "expose" = true })
     */
    public function removeDecompteAction(Decompte $decompte) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryDecompte = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Decompte');
        $decompte_manager = $this->get('app.decompte_manager');
        if ($decompte) {
            $repositoryDecompte->deleteDecompte($decompte);
            $decompte_manager->updateNextDecomptesOfProjectForDecompteDeleted($decompte);
            $view = View::create(["message" => "Décompte supprimé avec succès !"]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["message" => "Décompte introuvable !"], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/projects/{idProject}/decomptes/{id}/update", name="decompte_update_post", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function putDecompteAction(Request $request, Decompte $decompte) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $this->updateDecompteAction($request, $decompte);
    }

    public function updateDecompteAction(Request $request, Decompte $decompte) {
        $decompte_manager = $this->get('app.decompte_manager');
        $form = $this->createForm('OGIVE\ProjectBundle\Form\DecompteType', $decompte, array('method' => 'PUT'));
        $form->handleRequest($request);
        $originalPenalities = new \Doctrine\Common\Collections\ArrayCollection();
        $repositoryPenality = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Penality');
        foreach ($decompte->getPenalities() as $penality) {
            $originalPenalities->add($penality);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            if ($decompte->getMonthNumber() == null || $decompte->getMonthNumber() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre décompte est sans numero. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($decompte->getMonthName() == null || $decompte->getMonthName() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre décompte est sans désignation. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            //***************gestion des pénalités des décomptes ************************** */
            // remove the relationship between the decompte and the penalities
            foreach ($originalPenalities as $penality) {
                if (false === $decompte->getPenalities()->contains($penality)) {
                    // remove the penality from the decompte
                    $decompte->getPenalities()->removeElement($penality);
                    // if it was a many-to-one relationship, remove the relationship like this
                    $repositoryPenality->deletePenality($penality);
                }
            }
            $penalities = $decompte->getPenalities();
            $mtPenalities = 0;
            foreach ($penalities as $penality) {
                if ($penality->getDecompte() == null) {
                    $penality->setDecompte($decompte);
                }
                $mtPenalities += $penality->getAmount();
            }
            $decompte->setMtPenaliteACD($mtPenalities);
            if ($decompte->getProject()->getRemboursementAvanceOption() == 1) {
                $decompte->setRemboursementAvanceIntensity(50);
            } else {
                if (is_null($decompte->getRemboursementAvanceIntensity())) {
                    $decompte->setRemboursementAvanceIntensity(0);
                }
            }
            //$decompte->setDecompteValidations(new \Doctrine\Common\Collections\ArrayCollection());
            /********************initialise a list of decompte validation *********************************************** */
            //$decompte = $decompte_manager->initDecompteValidators($decompte);
            $decompte = $decompte_manager->updateDecompte($decompte);
            $decompte_manager->updateNextDecomptesOfProject($decompte);
            $decompte_manager->exportDecompteToExcel($decompte);
            //return $this->redirect($this->generateUrl('decompte_show_get', array('idProject' => $decompte->getProject()->getId(), 'id' => $decompte->getId())));
            $view = View::create(["message" => 'Décompte modifié avec succès. Vous serez redirigé dans bientôt !', 'id_project' => $decompte->getProject()->getId(), 'id_decompte' => $decompte->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes !'], Response::HTTP_BAD_REQUEST);
        }
    }

}
