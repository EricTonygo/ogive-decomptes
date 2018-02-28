<?php

namespace OGIVE\ProjectBundle\Controller;

use OGIVE\ProjectBundle\Entity\Project;
use OGIVE\ProjectBundle\Entity\Decompte;
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
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/projects/{id}/decomptes/new", name="decompte_add_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getAddDecomptesAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryDecompte = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Decompte');
        $monthNumber = count($repositoryDecompte->findBy(array('project' => $project))) + 1;
        $decompte = new Decompte();
        $decompte->setMonthNumber($monthNumber);
        $form = $this->createForm('OGIVE\ProjectBundle\Form\DecompteType', $decompte, array('method' => 'POST'));
        return $this->render('OGIVEProjectBundle:decompte:add.html.twig', array(
                    'project' => $project,
                    'form' => $form->createView(),
                    'tab' => 5
        ));
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
//            return $this->render('OGIVEProjectBundle:decompte:add.html.twig', array(
//                        'project' => $project,
//                        'form' => $form->createView(),
//                        'tab' => 5
//            ));
        }
        $form = $this->createForm('OGIVE\ProjectBundle\Form\DecompteType', $decompte, array('method' => 'PUT'));
        return $this->render('OGIVEProjectBundle:decompte:update_monthly_decompte.html.twig', array(
                    'project' => $decompte->getProject(),
                    'decompteTasks' => $decompte->getDecompteTasks(),
                    'decompte' => $decompte,
                    'tab' => 5,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{idProject}/decomptes/{id}" , name="decompte_show_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getShowDecompteByIdAction(Decompte $decompte) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if (empty($decompte)) {
//            return $this->render('OGIVEProjectBundle:decompte:add.html.twig', array(
//                        'project' => $project,
//                        'form' => $form->createView(),
//                        'tab' => 5
//            ));
        }
        $form = $this->createForm('OGIVE\ProjectBundle\Form\DecompteType', $decompte, array('method' => 'PUT'));
        return $this->render('OGIVEProjectBundle:decompte:monthly_decompte.html.twig', array(
                    'project' => $decompte->getProject(),
                    'decompteTasks' => $decompte->getDecompteTasks(),
                    'decompte' => $decompte,
                    'tab' => 5,
                    'month' => $decompte->getMonthNumber(),
                    'form' => $form->createView()
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{idProject}/decomptes/{id}/tasks" , name="decompte_tasks_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getDecompteTasksByIdAction(Request $request, Decompte $decompte) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $page = 1;
        $maxResults = 8;
        $route_param_page = array();
        $route_param_search_query = array();
        $search_query = null;
        $start_date = null;
        $end_date = null;
        $placeholder = "Rechercher une tache...";
        if ($request->get('page')) {
            $page = intval(htmlspecialchars(trim($request->get('page'))));
            $route_param_page['page'] = $page;
        }
        if ($request->get('search_query')) {
            $search_query = htmlspecialchars(trim($request->get('search_query')));
            $route_param_search_query['search_query'] = $search_query;
        }
        if ($request->get('start-date')) {
            $start_date = htmlspecialchars(trim($request->get('start-date')));
            $route_param_search_query['start-date'] = $start_date;
        }
        if ($request->get('end-date')) {
            $end_date = htmlspecialchars(trim($request->get('end-date')));
            $route_param_search_query['end-date'] = $end_date;
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $start_from = ($page - 1) * $maxResults >= 0 ? ($page - 1) * $maxResults : 0;
        $total_tasks = count($em->getRepository('OGIVEProjectBundle:Task')->getAll(null, null, $search_query, $decompte->getProject()->getId()));
        $total_pages = ceil($total_tasks / $maxResults);
        $tasks = $em->getRepository('OGIVEProjectBundle:Task')->getAll($start_from, $maxResults, $search_query, $decompte->getProject()->getId());
        $projects = $em->getRepository('OGIVEProjectBundle:Project')->getAll(0, 8, null, $user->getId());
        return $this->render('OGIVEProjectBundle:lot:lot-tasks.html.twig', array(
                    'decompte' => $decompte,
                    'projects' => $projects,
                    'tasks' => $tasks,
                    'page' => $page,
                    'total_pages' => $total_pages,
                    'total_tasks' => $total_tasks,
                    'placeholder' => $placeholder
        ));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/projects/{id}/decomptes/new", name="decompte_add_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postDecomptesAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
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
            $month = $decompte->getMonthNumber();
            $decompteMois = $em->getRepository('OGIVEProjectBundle:Decompte')->getDecompteByMonthAndProject($month, $project->getId());
            $decomptePrec = $em->getRepository('OGIVEProjectBundle:Decompte')->getDecomptePrecByMonthAndProject($month, $project->getId());
            if ($decompteMois == null) {
                $mtPrevueMarcheDcpt = 0;
                $mtPrevueProjetExecDcpt = 0;
                $mtCumulMoisPrecDcpt = 0;
                $mtMoisDcpt = 0;
                $mtCumulMoisDcpt = 0;
                $pourcentageRealDcpt = 0;

                $tasks = $project->getTasks();
                foreach ($tasks as $task) {
                    $decompteTask = $this->createDecompteTaskUsingTask($task, $decompte, $decomptePrec);
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
                    if ($decompteTask->getPourcentRealisation()) {
                        $pourcentageRealDcpt += $decompteTask->getPourcentRealisation();
                    }
                }
                $decompte->setDecompteState(1);
                $decompte->setCreatedUser($user);
                $decompte->setMtPrevueMarche($mtPrevueMarcheDcpt);
                $decompte->setMtPrevueProjetExec($mtPrevueProjetExecDcpt);
                $decompte->setMtCumulMoisPrec($mtCumulMoisPrecDcpt);
                $decompte->setMtCumulMois($mtCumulMoisDcpt);
                $decompte->setMtMois($mtMoisDcpt);
                $decompte->setPourcentRealisation(round($pourcentageRealDcpt / count($tasks), 2));
                $decompte = $repositoryDecompte->saveDecompte($decompte);
                return $this->redirect($this->generateUrl('decompte_update_get', array('idProject' => $project->getId(), 'id' => $decompte->getId())));
            } else {
                return $this->render('OGIVEProjectBundle:decompte:add.html.twig', array(
                            'project' => $project,
                            'form' => $form->createView(),
                            'tab' => 5
                ));
            }
        } else {
            return $this->render('OGIVEProjectBundle:decompte:add.html.twig', array(
                        'project' => $project,
                        'form' => $form->createView(),
                        'tab' => 5
            ));
        }
    }

    public function addOrUpdateNewDecompteTask(Task $task, Decompte $decompte = null) {
        $em = $this->getDoctrine()->getManager();
        $repositoryDecompteTask = $em->getRepository('OGIVEProjectBundle:DecompteTask');
        $repositoryDecompte = $em->getRepository('OGIVEProjectBundle:DecompteTask');
        $decompteTask = $repositoryDecompteTask->getDecompteTaskByDecompteAndTask($task->getId(), $decompte->getId());
        $decompteTask->setNom($task->getNom());
        $decompteTask->setNumero($task->getNumero());
        if ($decompteTask == null) {
            $decomptePrec = $repositoryDecompte->getDecomptePrecByMonthAndProject($decompte->getMonthNumber(), $decompte->getProject()->getId());
            $decompteTask = $this->createDecompteTaskUsingTask($task, $decompte, $decomptePrec);
            $decompteTask->setTask($task);
            if ($task->getParentTask()) {
                $parentTask = $task->getParentTask();
                $parentDecompteTask = $repositoryDecompteTask->getDecompteTaskByDecompteAndTask($parentTask->getId(), $decompte->getId());
                $parentDecompteTask->addDecompteTask($decompteTask);
                $repositoryDecompteTask->updateDecompteTask($parentDecompteTask);
            } else {
                $decompteTask->setParentDecompteTask(null);
                $decompte->addDecompteTask($decompteTask);
            }
        } else {
            if ($decompteTask->getParentDecompteTask() != null) {
                $decompteTask->setUnite($task->getUnite());
                $decompteTask->setPrixUnitaire($task->getPrixUnitaire());
                $decompteTask->setQtePrevueMarche($task->getQtePrevueMarche());
                $decompteTask->setQtePrevueProjetExec($task->getQtePrevueProjetExec());
                $repositoryDecompteTask->updateDecompteTask($decompteTask);
            }
        }

        return $decompte;
    }

    public function createDecompteTaskUsingTask(Task $task, Decompte $decompte, Decompte $decomptePrec = null) {
        $decompteTask = new DecompteTask();
        $em = $this->getDoctrine()->getManager();
        $decompteTask->setNom($task->getNom());
        $decompteTask->setNumero($task->getNumero());
        if ($task->getSubTasks() && count($task->getSubTasks()) > 0) {
            $subTasks = $task->getSubTasks();
            foreach ($subTasks as $subTask) {
                $subDecompteTask = $this->createDecompteTaskUsingTask($subTask, $decompte, $decomptePrec);
                $subDecompteTask->setParentDecompteTask($decompteTask);
                $decompteTask->addSubDecompteTask($subDecompteTask);
                $decompteTask->setMtPrevueMarche($decompteTask->getMtPrevueMarche() >= 0 ? $decompteTask->getMtPrevueMarche() + $subDecompteTask->getMtPrevueMarche() : $subDecompteTask->getMtPrevueMarche());
                $decompteTask->setMtPrevueProjetExec($decompteTask->getMtPrevueProjetExec() >= 0 ? $decompteTask->getMtPrevueMarche() + $subDecompteTask->getMtPrevueProjetExec() : $subDecompteTask->getMtPrevueProjetExec());
                $decompteTask->setMtCumulMoisPrec($decompteTask->getMtCumulMoisPrec() >= 0 ? $decompteTask->getMtCumulMoisPrec() + $subDecompteTask->getMtCumulMoisPrec() : $subDecompteTask->getMtCumulMoisPrec());
                $decompteTask->setMtCumulMois($decompteTask->getMtCumulMois() >= 0 ? $decompteTask->getMtCumulMois() + $subDecompteTask->getMtCumulMois() : $subDecompteTask->getMtCumulMois());
                $decompteTask->setMtMois($decompteTask->getMtMois() >= 0 ? $decompteTask->getMtMois() + $subDecompteTask->getMtMois() : $subDecompteTask->getMtMois());
                $decompteTask->setPourcentRealisation($decompteTask->getPourcentRealisation() >= 0 ? $decompteTask->getPourcentRealisation() + $subDecompteTask->getPourcentRealisation() : $subDecompteTask->getPourcentRealisation());
            }
            if ($decompteTask->getPourcentRealisation() >= 0 && $this->getTheExactNumberOfDecompteTasks($decompteTask->getSubDecompteTasks()) > 0) {
                $decompteTask->setPourcentRealisation(round($decompteTask->getPourcentRealisation() / $this->getTheExactNumberOfDecompteTasks($decompteTask->getSubDecompteTasks()), 2));
            }
        } else {
            if ($decomptePrec) {
                $decompteTaskPrec = $em->getRepository('OGIVEProjectBundle:DecompteTask')->getDecompteTaskByDecompteAndTask($task->getId(), $decomptePrec->getId());
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
            if ($decompteTask->getQteCumulMois() >= 0 && $decompteTask->getPrixUnitaire() >= 0) {
                $decompteTask->setMtCumulMois($decompteTask->getPrixUnitaire() * $decompteTask->getQteCumulMois());
            }
            if ($decompteTask->getQteCumulMoisPrec() >= 0 && $decompteTask->getPrixUnitaire() >= 0) {
                $decompteTask->setMtCumulMoisPrec($decompteTask->getPrixUnitaire() * $decompteTask->getQteCumulMoisPrec());
            }
            if ($decompteTask->getQteMois() >= 0 && $decompteTask->getPrixUnitaire() >= 0) {
                $decompteTask->setMtMois($decompteTask->getQteMois() * $decompteTask->getPrixUnitaire());
            }
            if ($decompteTask->getQtePrevueMarche() >= 0 && $decompteTask->getPrixUnitaire() >= 0) {
                $decompteTask->setMtPrevueMarche($decompteTask->getQtePrevueMarche() * $decompteTask->getPrixUnitaire());
            }
            if ($decompteTask->getQtePrevueProjetExec() >= 0 && $decompteTask->getPrixUnitaire() >= 0) {
                $decompteTask->setMtPrevueProjetExec($decompteTask->getQtePrevueProjetExec() * $decompteTask->getPrixUnitaire());
            }
            if ($decompteTask->getQteCumulMois() >= 0 && $decompteTask->getQtePrevueProjetExec() > 0) {
                $decompteTask->setPourcentRealisation(round($decompteTask->getQteCumulMois() * 100 / $decompteTask->getQtePrevueProjetExec(), 2));
            }
        }
        $decompteTask->setTask($task);
        $decompteTask->setMyDecompte($decompte);
        return $decompteTask;
    }

    public function updateDecompteTask(DecompteTask $decompteTask, Decompte $decomptePrec = null) {
        $em = $this->getDoctrine()->getManager();
        if ($decompteTask->getSubDecompteTasks() && count($decompteTask->getSubDecompteTasks()) > 0) {
            $subDecompteTasks = $decompteTask->getSubDecompteTasks();
            foreach ($subDecompteTasks as $subDecompteTask) {
                $subDecompteTask = $this->updateDecompteTask($subDecompteTask, $decomptePrec);
                $decompteTask->setMtPrevueMarche($decompteTask->getMtPrevueMarche() >= 0 ? $decompteTask->getMtPrevueMarche() + $subDecompteTask->getMtPrevueMarche() : $subDecompteTask->getMtPrevueMarche());
                $decompteTask->setMtPrevueProjetExec($decompteTask->getMtPrevueProjetExec() >= 0 ? $decompteTask->getMtPrevueMarche() + $subDecompteTask->getMtPrevueProjetExec() : $subDecompteTask->getMtPrevueProjetExec());
                $decompteTask->setMtCumulMoisPrec($decompteTask->getMtCumulMoisPrec() >= 0 ? $decompteTask->getMtCumulMoisPrec() + $subDecompteTask->getMtCumulMoisPrec() : $subDecompteTask->getMtCumulMoisPrec());
                $decompteTask->setMtCumulMois($decompteTask->getMtCumulMois() >= 0 ? $decompteTask->getMtCumulMois() + $subDecompteTask->getMtCumulMois() : $subDecompteTask->getMtCumulMois());
                $decompteTask->setMtMois($decompteTask->getMtMois() >= 0 ? $decompteTask->getMtMois() + $subDecompteTask->getMtMois() : $subDecompteTask->getMtMois());
                $decompteTask->setPourcentRealisation($decompteTask->getPourcentRealisation() >= 0 ? $decompteTask->getPourcentRealisation() + $subDecompteTask->getPourcentRealisation() : $subDecompteTask->getPourcentRealisation());
            }
            if ($decompteTask->getPourcentRealisation() >= 0 && $this->getTheExactNumberOfDecompteTasks($decompteTask->getSubDecompteTasks()) > 0) {
                $decompteTask->setPourcentRealisation(round($decompteTask->getPourcentRealisation() / $this->getTheExactNumberOfDecompteTasks($subDecompteTasks), 2));
            }
        } else {
            if ($decomptePrec) {
                $decompteTaskPrec = $em->getRepository('OGIVEProjectBundle:DecompteTask')->getDecompteTaskByDecompteAndTask($decompteTask->getTask()->getId(), $decomptePrec->getId());
                $decompteTask->setQteCumulMoisPrec($decompteTaskPrec->getQteCumulMois());
            } else {
                $decompteTask->setQteCumulMoisPrec(0);
            }
            $decompteTask->setQteCumulMois($decompteTask->getQteCumulMoisPrec() + $decompteTask->getQteMois());
            if ($decompteTask->getQteCumulMois() >= 0 && $decompteTask->getPrixUnitaire() >= 0) {
                $decompteTask->setMtCumulMois($decompteTask->getPrixUnitaire() * $decompteTask->getQteCumulMois());
            }
            if ($decompteTask->getQteCumulMoisPrec() >= 0 && $decompteTask->getPrixUnitaire() >= 0) {
                $decompteTask->setMtCumulMoisPrec($decompteTask->getPrixUnitaire() * $decompteTask->getQteCumulMoisPrec());
            }
            if ($decompteTask->getQteMois() >= 0 && $decompteTask->getPrixUnitaire() >= 0) {
                $decompteTask->setMtMois($decompteTask->getQteMois() * $decompteTask->getPrixUnitaire());
            }
            if ($decompteTask->getQtePrevueMarche() >= 0 && $decompteTask->getPrixUnitaire() >= 0) {
                $decompteTask->setMtPrevueMarche($decompteTask->getQtePrevueMarche() * $decompteTask->getPrixUnitaire());
            }
            if ($decompteTask->getQtePrevueProjetExec() >= 0 && $decompteTask->getPrixUnitaire() >= 0) {
                $decompteTask->setMtPrevueProjetExec($decompteTask->getQtePrevueProjetExec() * $decompteTask->getPrixUnitaire());
            }
            if ($decompteTask->getQteCumulMois() >= 0 && $decompteTask->getQtePrevueProjetExec() > 0) {
                $decompteTask->setPourcentRealisation(round($decompteTask->getQteCumulMois() * 100 / $decompteTask->getQtePrevueProjetExec(), 2));
            }
        }
        return $decompteTask;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/projects/{idProject}/decomptes/{id}/remove", name="decompte_delete", options={ "method_prefix" = false, "expose" = true })
     */
    public function removeDecompteAction(Decompte $decompte) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryDecompte = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Decompte');
        if ($decompte) {
            $repositoryDecompte->deleteDecompte($decompte);
            return $this->redirect($this->generateUrl('project_decomptes_get', array('id' => $decompte->getProject()->getId())));
        } else {
            return $this->redirect($this->generateUrl('project_decomptes_get', array('id' => $decompte->getProject()->getId())));
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

        $form = $this->createForm('OGIVE\ProjectBundle\Form\DecompteType', $decompte, array('method' => 'PUT'));
        $form->handleRequest($request);
//        $decompteTask = new DecompteTask();
        if ($form->isSubmitted() && $form->isValid()) {
            $decompte = $this->updateDecompte($decompte);
            return $this->redirect($this->generateUrl('decompte_show_get', array('idProject' => $decompte->getProject()->getId(), 'id' => $decompte->getId())));
        } else {
            return $this->redirect($this->generateUrl('decompte_update_get', array('idProject' => $decompte->getProject()->getId(), 'id' => $decompte->getId())));
        }
    }

    public function updateDecompte($decompte) {
        $repositoryDecompte = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Decompte');
        $em = $this->getDoctrine()->getManager();
        $decomptePrec = $em->getRepository('OGIVEProjectBundle:Decompte')->getDecomptePrecByMonthAndProject($decompte->getMonthNumber(), $decompte->getProject()->getId());
        $mtPrevueMarcheDcpt = 0;
        $mtPrevueProjetExecDcpt = 0;
        $mtCumulMoisPrecDcpt = 0;
        $mtMoisDcpt = 0;
        $mtCumulMoisDcpt = 0;
        $pourcentageRealDcpt = 0;
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
            if ($decompteTask->getPourcentRealisation()) {
                $pourcentageRealDcpt += $decompteTask->getPourcentRealisation();
            }
        }
        $user = $this->getUser();
        $decompte->setUpdatedUser($user);
        $decompte->setMtPrevueMarche($mtPrevueMarcheDcpt);
        $decompte->setMtPrevueProjetExec($mtPrevueProjetExecDcpt);
        $decompte->setMtCumulMoisPrec($mtCumulMoisPrecDcpt);
        $decompte->setMtCumulMois($mtCumulMoisDcpt);
        $decompte->setMtMois($mtMoisDcpt);
        $decompte->setPourcentRealisation(round($pourcentageRealDcpt / $this->getTheExactNumberOfDecompteTasks($decompte->getDecompteTasks()), 2));
        $this->updateMontantTVAOfDecompte($decompte);
        $this->updateMontantIROfDecompte($decompte);
        $this->updateMontantNetAPercevoirOfDecompte($decompte);
        $this->updateMontantTTCOfDecompte($decompte);
        $this->updateAvanceDemarrage($decompte, $decomptePrec);
        $this->updatePrestationsWithAIR($decompte);
        $this->updateRetenueGarantie($decompte, $decomptePrec);
        $this->updateRemboursementAvance($decompte, $decomptePrec);
        $this->updatePenalite($decompte, $decomptePrec);
        $this->updateTotalPaiements($decompte);
        $this->updateTaxes($decompte);
        $this->updateAcompteAMandater($decompte);
        return $repositoryDecompte->updateDecompte($decompte);
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

}
