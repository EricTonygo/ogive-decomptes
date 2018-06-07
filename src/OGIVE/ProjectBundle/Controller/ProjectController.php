<?php

namespace OGIVE\ProjectBundle\Controller;

use OGIVE\ProjectBundle\Entity\Project;
use OGIVE\ProjectBundle\Entity\Owner;
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

/**
 * Project controller.
 *
 */
class ProjectController extends Controller {

    /**
     * @Rest\View()
     * @Rest\Get("/projects" , name="project_index", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function getProjectsAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $decompte_manager = $this->get('app.decompte_manager');
        $project = new Project();
        $page = 1;
        $route_param_page = array();
        $route_param_search_query = array();
        $search_query = null;
        $start_date = null;
        $end_date = null;
        $owner = null;
        $projects = null;
        $placeholder = "Rechercher un appel d'offre...";
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
        if ($request->get('owner')) {
            $owner = htmlspecialchars(trim($request->get('owner')));
            $route_param_search_query['owner'] = $owner;
        }

        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectType', $project);

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $projects = $em->getRepository('OGIVEProjectBundle:Project')->getAll(null, null, $search_query, $this->getUser()->getId());
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $projects = $decompte_manager->getUserProjects($this->getUser());
        }
        $owners = $em->getRepository('OGIVEProjectBundle:Owner')->findBy(array("state" => 1, "status" => 1));

        return $this->render('OGIVEProjectBundle:project:index.html.twig', array(
                    'projects' => $projects,
                    'page' => $page,
                    'form' => $form->createView(),
                    'route_param_page' => $route_param_page,
                    'route_param_search_query' => $route_param_search_query,
                    'search_query' => $search_query,
                    'placeholder' => $placeholder,
                    'owners' => $owners,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'queried_owner' => $owner
        ));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/projects/new", name="project_add", options={ "method_prefix" = false, "expose" = true })
     */
    public function addProjectAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $project = new Project();
//        $repositoryProject = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Project');
//        $repositoryOwner = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Owner');

        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectType', $project);
        return $this->render('OGIVEProjectBundle:project:add.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{id}/update" , name="project_update_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getUpdateProjectByIdAction(Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if (empty($project)) {
            return new JsonResponse(['message' => "Appel d'offre introuvable"], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectType', $project, array('method' => 'PUT'));
        return $this->render('OGIVEProjectBundle:project:update.html.twig', array(
                    'project' => $project,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{id}/update/parameters" , name="project_parameters_update_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getUpdateProjectParametersByIdAction(Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if (empty($project)) {
            return new JsonResponse(['message' => "Appel d'offre introuvable"], Response::HTTP_NOT_FOUND);
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $decompte_manager = $this->get('app.decompte_manager');
        $repositoryProject = $em->getRepository('OGIVEProjectBundle:Project');
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $projects = $repositoryProject->getAll(null, null, null, $user->getId());
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $projects = $decompte_manager->getUserProjects($this->getUser());
        }
        if (!$project->getPercentIR()) {
            $project->setPercentIR(2.2);
        }
        if (!$project->getPercentTVA()) {
            $project->setPercentTVA(19.5);
        }
        if (!$project->getRemboursementAvanceOption()) {
            $project->setRemboursementAvanceOption(1);
        }
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectParametersType', $project, array('method' => 'PUT'));
        return $this->render('OGIVEProjectBundle:project:update-parameters.html.twig', array(
                    'project' => $project,
                    'tab' => 1,
                    'projects' => $projects,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{id}/general-informations" , name="project_gen_infos_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getProjectGenInfosByIdAction(Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $projects = null;
        $decompte_manager = $this->get('app.decompte_manager');
        $repositoryProject = $em->getRepository('OGIVEProjectBundle:Project');
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $projects = $repositoryProject->getAll(null, null, null, $user->getId());
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $projects = $decompte_manager->getUserProjects($this->getUser());
        }
        $decomptes = $em->getRepository('OGIVEProjectBundle:Decompte')->getAll(null, null, null, $project->getId());
        $projectManager = $project->getProjectManagers()[0];
        $holder = $project->getHolders()[0];
        $form = $this->createForm('OGIVE\ProjectBundle\Form\AvanceDemarrageProjectType', $project, array('method' => 'PUT'));
        return $this->render('OGIVEProjectBundle:project:general-informations-project.html.twig', array(
                    'project' => $project,
                    'projects' => $projects,
                    'lastDecompte' => $decomptes[count($decomptes) - 1],
                    'tab' => 1,
                    'projectManager' => $projectManager,
                    'holder' => $holder,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{id}/tasks" , name="project_tasks_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getProjectTasksByIdAction(Request $request, Project $project) {
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
        $placeholder = "Rechercher une tâche...";
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
        $repositoryTask = $em->getRepository('OGIVEProjectBundle:Task');
        $user = $this->getUser();
        $start_from = ($page - 1) * $maxResults >= 0 ? ($page - 1) * $maxResults : 0;
        $total_tasks = count($repositoryTask->getAll(null, null, $search_query, null, $project->getId()));
        $total_pages = ceil($total_tasks / $maxResults);
        $tasks = $repositoryTask->findBy(array('parentTask' => null));
        $projects = $em->getRepository('OGIVEProjectBundle:Project')->getAll(0, 8, null, $user->getId());
        $form = $this->createForm('OGIVE\ProjectBundle\Form\TasksProjectType', $project);
        return $this->render('OGIVEProjectBundle:project:project-tasks.html.twig', array(
                    'project' => $project,
                    'projects' => $projects,
                    'tasks' => $project->getTasks(),
                    'page' => $page,
                    'tab' => 3,
                    'total_pages' => $total_pages,
                    'total_tasks' => $total_tasks,
                    'placeholder' => $placeholder,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{id}/contributors" , name="project_contributors_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getProjectContributorsByIdAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $tasks = $em->getRepository('OGIVEProjectBundle:Task')->getAll(null, null, null, $project->getId());
        $projectManagers = $em->getRepository('OGIVEProjectBundle:ProjectManager')->getAll(null, null, null, $project->getId());
        $serviceProviders = $em->getRepository('OGIVEProjectBundle:ServiceProvider')->getAll(null, null, null, $project->getId());
        $otherContributors = $em->getRepository('OGIVEProjectBundle:OtherContributor')->getAll(null, null, null, $project->getId());
        $holders = $em->getRepository('OGIVEProjectBundle:Holder')->getAll(null, null, null, $project->getId());
        $projects = $em->getRepository('OGIVEProjectBundle:Project')->getAll(0, 8, null, $user->getId());
        return $this->render('OGIVEProjectBundle:project:project-contributors.html.twig', array(
                    'project' => $project,
                    'projects' => $projects,
                    'tasks' => $tasks,
                    'tab' => 4,
                    'owner' => $project->getOwner(),
                    'projectManagers' => $projectManagers,
                    'holders' => $holders,
                    'serviceProviders' => $serviceProviders,
                    'otherContributors' => $otherContributors
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{id}/decomptes" , name="project_decomptes_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getProjectDecomptesByIdAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $decompte_manager = $this->get('app.decompte_manager');
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $can_create_decompte = $decompte_manager->user_can_create_decompte($user, $project);
        $decomptes = $em->getRepository('OGIVEProjectBundle:Decompte')->getAll(null, null, null, $project->getId());
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $projects = $em->getRepository('OGIVEProjectBundle:Project')->getAll(null, null, null, $this->getUser()->getId());
        } elseif ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $projects = $decompte_manager->getUserProjects($this->getUser());
        }
        return $this->render('OGIVEProjectBundle:project:project-decomptes.html.twig', array(
                    'project' => $project,
                    'projects' => $projects,
                    'tab' => 5,
                    'decomptes' => $decomptes,
                    'can_create_decompte' => $can_create_decompte
        ));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/projects/new", name="project_add_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postProjectsAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $project = new Project();
        $repositoryProject = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Project');
        $common_service = $this->get('app.common_service');
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectType', $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($project->getNumeroMarche() == null || $project->getNumeroMarche() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre projet est sans numero. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($project->getSubject() == null || $project->getSubject() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre projet est sans objet. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($project->getDelais() == null) {
                return new JsonResponse(["success" => false, 'message' => "Veuillez préciser les délais de votre projet."], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryProject->findOneBy(array('numeroMarche' => $project->getNumeroMarche()))) {
                return new JsonResponse(["success" => false, 'message' => 'Une tâche avec ce numéro existe déjà.'], Response::HTTP_BAD_REQUEST);
            }
            if($request->get("priority-order-owner")){
                $project->getOwner()->setOrdrePriorite(intval($request->get("priority-order-owner")));
            }
            $startDate = $project->getStartDate();
            $endDate = $project->getEndDate();
            if ($project->getDelais() && $startDate == null && $endDate == null) {
                $startDate = new \DateTime('now');
                $project->setStartDate($startDate->format('Y-m-d'));
                $startDateTime = strtotime(str_replace('/', '-', $project->getStartDate()));
                $endDateTime = mktime(0, 0, 0, date("n", $startDateTime), date("j", $startDateTime) + $project->getDelais(), date("Y", $startDateTime));
                $endDate = new \DateTime(date("Y-m-d", $endDateTime));
                $project->setEndDate($endDate);
            } elseif ($project->getDelais() && $startDate != null && $endDate == null) {
                $startDateTime = strtotime(str_replace('/', '-', $project->getStartDate()));
                $endDateTime = mktime(0, 0, 0, date("n", $startDateTime), date("j", $startDateTime) + $project->getDelais(), date("Y", $startDateTime));
                $endDate = new \DateTime(date("Y-m-d", $endDateTime));
                $project->setEndDate($endDate);
            } elseif ($project->getDelais() && $startDate == null && $endDate != null) {
                $endDateTime = strtotime(str_replace('/', '-', $project->getEndDate()));
                $startDateTime = mktime(0, 0, 0, date("n", $endDateTime), date("j", $endDateTime) - $project->getDelais(), date("Y", $endDateTime));
                $startDate = new \DateTime(date("Y-m-d", $startDateTime));
                $project->setEndDate($endDate);
            }
            $user = $this->getUser();
            $project->setCreatedUser($user);
            $owner = $project->getOwner();
            $common_service->setUserAttributesToContributor($owner);
            $project->setOwner($owner);
            //***************gestion des projectManagers du projet ************************** */
            $projectManagers = $project->getProjectManagers();
            foreach ($projectManagers as $projectManager) {
                $projectManager->setProject($project);
                $common_service->setUserAttributesToContributor($projectManager);
            }

            //***************gestion des titulaire du projet ************************** */
            $holders = $project->getHolders();
            foreach ($holders as $holder) {
                $holder->setProject($project);
                $common_service->setUserAttributesToContributorIfNotExists($holder);
            }

            //***************gestion des prestataire du projet ************************** */
            $servicesProviders = $project->getServiceProviders();
            foreach ($servicesProviders as $servicesProvider) {
                $servicesProvider->setProject($project);
                $common_service->setUserAttributesToContributor($servicesProvider);
            }

            //***************gestion des autres intervenant du projet ************************** */
            $otherContributors = $project->getOtherContributors();
            foreach ($otherContributors as $otherContributor) {
                $otherContributor->setProject($project);
                $common_service->setUserAttributesToContributor($otherContributor);
            }

            $numerMarcheTab = explode("/", $project->getNumeroMarche());
            $anneeBudgetaire = $numerMarcheTab[count($numerMarcheTab) - 1];
            $project->setAnneeBudgetaire($anneeBudgetaire);
//            $decompteTotal = new DecompteTotal();
//            $project->setDecompteTotal($decompteTotal);
            if($project->getAvanceDemarrage() == null){
                $project->setAvanceDemarrage(0);
                $project->setMtAvanceDemarrage(0);
            }
            $project = $repositoryProject->saveProject($project);
            //return $this->redirect($this->generateUrl('project_gen_infos_get', array('id' => $project->getId())));
            $view = View::create(["message" => 'Projet créé avec succès. Vous serez redirigé dans bientôt!', "id_project" => $project->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Delete("/projects/{id}/remove", name="project_delete", options={ "method_prefix" = false, "expose" = true })
     */
    public function removeProjectAction(Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryProject = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Project');
        if ($project) {
            $repositoryProject->deleteProject($project);
            $view = View::create(["message" => "Projet supprimé avec succès !"]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["message" => "Projet introuvable !"], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/projects/{id}/update/start-advance", name="project_update_start_advance", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function updateStartAdvanceProjectAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $decompte_manager = $this->get('app.decompte_manager');
        $form = $this->createForm('OGIVE\ProjectBundle\Form\AvanceDemarrageProjectType', $project, array('method' => 'PUT'));
        $repositoryProject = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Project');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($project->getAvanceDemarrage() == null || $project->getAvanceDemarrage() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vueillez le saisir le montant de l'avance démarrage."], Response::HTTP_BAD_REQUEST);
            }
            if (!is_numeric($project->getAvanceDemarrage())) {
                return new JsonResponse(["success" => false, 'message' => "Vueillez le saisir un montant numerique."], Response::HTTP_BAD_REQUEST);
            }
            $project = $repositoryProject->updateProject($project);
            $view = View::create(["message" => "Montant de l'avance démarrage déclarer avec succès", "id_project" => $project->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/projects/{id}/update/parameters", name="project_parameters_update_post", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function updateProjectParametersAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $decompte_manager = $this->get('app.decompte_manager');
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectParametersType', $project, array('method' => 'PUT'));
        $repositoryProject = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Project');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avanceDemarrageContracted = $request->get('avance_demarrage_contracted');
            $remboursementAvanceOption = intval($request->get('repayment_advance_option'));
            if ($avanceDemarrageContracted == "yes") {
                if ($project->getAvanceDemarrage() == null || $project->getAvanceDemarrage() == "") {
                    return new JsonResponse(["success" => false, 'message' => "Vueillez le saisir le pourcentage de l'avance démarrage."], Response::HTTP_BAD_REQUEST);
                }
                if (!is_numeric($project->getAvanceDemarrage())) {
                    return new JsonResponse(["success" => false, 'message' => "Vueillez le saisir un nombre pour le pourcentage de l'avance démarrage."], Response::HTTP_BAD_REQUEST);
                }
                if (is_numeric($project->getAvanceDemarrage()) && $project->getAvanceDemarrage() <= 0) {
                    return new JsonResponse(["success" => false, 'message' => "Le pourcentage de l'avance démarrage doit être strictement positif."], Response::HTTP_BAD_REQUEST);
                }
            } else {
                $project->setAvanceDemarrage(0);
            }
            $project->setRemboursementAvanceOption($remboursementAvanceOption);
            if ($project->getPercentTVA() == null || $project->getPercentTVA() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vueillez le saisir le pourcentage de la TVA."], Response::HTTP_BAD_REQUEST);
            }
            if (!is_numeric($project->getPercentTVA())) {
                return new JsonResponse(["success" => false, 'message' => "Vueillez le saisir un nombre pour le pourcentage de la TVA"], Response::HTTP_BAD_REQUEST);
            }
            if ($project->getPercentIR() == null || $project->getPercentIR() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vueillez le saisir le pourcentage de l'IR."], Response::HTTP_BAD_REQUEST);
            }
            if (!is_numeric($project->getPercentTVA())) {
                return new JsonResponse(["success" => false, 'message' => "Vueillez le saisir un nombre pour le pourcentage de l'IR."], Response::HTTP_BAD_REQUEST);
            }
            $project->setMtAvanceDemarrage($project->getProjectCost()*$project->getAvanceDemarrage()/100);
            $project = $repositoryProject->updateProject($project);
            $view = View::create(["message" => "Les paramètres du projet on été mis à jour avec succès.", "id_project" => $project->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/projects/{id}/update", name="project_update", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function putProjectAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $this->updateProjectAction($request, $project);
    }

    public function updateProjectAction(Request $request, Project $project) {
        $owner = new Owner();
        $decompte_manager = $this->get('app.decompte_manager');
        $repositoryProject = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Project');
        $repositoryProjectManager = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:ProjectManager');
        $repositoryHolder = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Holder');
        $repositoryServiceProvider = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:ServiceProvider');
        $repositoryOtherContributors = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:OtherContributor');
        $common_service = $this->get('app.common_service');
        $originalProjectManagers = new \Doctrine\Common\Collections\ArrayCollection();
        $originalHolders = new \Doctrine\Common\Collections\ArrayCollection();
        $originalServiceProviders = new \Doctrine\Common\Collections\ArrayCollection();
        $originalOtherContributors = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ($project->getProjectManagers() as $projectManager) {
            $originalProjectManagers->add($projectManager);
        }
        foreach ($project->getHolders() as $holder) {
            $originalHolders->add($holder);
        }
        foreach ($project->getServiceProviders() as $serviceProvider) {
            $originalServiceProviders->add($serviceProvider);
        }
        foreach ($project->getOtherContributors() as $otherContributor) {
            $originalOtherContributors->add($otherContributor);
        }

        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectType', $project, array('method' => 'PUT'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($project->getNumeroMarche() == null || $project->getNumeroMarche() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre projet est sans numero du marché. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($project->getSubject() == null || $project->getSubject() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre projet est sans objet. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($project->getDelais() == null) {
                return new JsonResponse(["success" => false, 'message' => "Veuillez préciser les délais de votre projet."], Response::HTTP_BAD_REQUEST);
            }
            $projectEdit = $repositoryProject->findOneBy(array('numeroMarche' => $project->getNumeroMarche()));
            if (!is_null($projectEdit) && $projectEdit->getId() != $project->getId()) {
                return new JsonResponse(["success" => false, 'message' => 'Un projet avec ce numero du marché existe déjà'], Response::HTTP_BAD_REQUEST);
            }
            if($request->get("priority-order-owner")){
                $project->getOwner()->setOrdrePriorite(intval($request->get("priority-order-owner")));                
            }
            $startDate = $project->getStartDate();
            $endDate = $project->getEndDate();
            if ($project->getDelais() && $startDate == null && $endDate == null) {
                $startDate = new \DateTime('now');
                $project->setStartDate($startDate->format('Y-m-d'));
                $startDateTime = strtotime(str_replace('/', '-', $project->getStartDate()));
                $endDateTime = mktime(0, 0, 0, date("n", $startDateTime), date("j", $startDateTime) + $project->getDelais(), date("Y", $startDateTime));
                $endDate = new \DateTime(date("Y-m-d", $endDateTime));
                $project->setEndDate($endDate);
            } elseif ($project->getDelais() && $startDate != null && $endDate == null) {
                $startDateTime = strtotime(str_replace('/', '-', $project->getStartDate()));
                $endDateTime = mktime(0, 0, 0, date("n", $startDateTime), date("j", $startDateTime) + $project->getDelais(), date("Y", $startDateTime));
                $endDate = new \DateTime(date("Y-m-d", $endDateTime));
                $project->setEndDate($endDate);
            } elseif ($project->getDelais() && $startDate == null && $endDate != null) {
                $endDateTime = strtotime(str_replace('/', '-', $project->getEndDate()));
                $startDateTime = mktime(0, 0, 0, date("n", $endDateTime), date("j", $endDateTime) - $project->getDelais(), date("Y", $endDateTime));
                $startDate = new \DateTime(date("Y-m-d", $startDateTime));
                $project->setEndDate($endDate);
            }
            $owner = $project->getOwner();
            $common_service->setUserAttributesToContributor($owner);
            $project->setOwner($owner);
            //***************gestion des projectManagers du project ************************** */
            // remove the relationship between the project and the projectManagers
            foreach ($originalProjectManagers as $projectManager) {
                if (false === $project->getProjectManagers()->contains($projectManager)) {
                    // remove the project from the projectManagers
                    $project->getProjectManagers()->removeElement($projectManager);
                    // if it was a many-to-one relationship, remove the relationship like this
//                    $projectManager->setProject(null);
//                    $projectManager->setStatus(0);
                    $repositoryProjectManager->deleteProjectManager($projectManager);
                    // if you wanted to delete the Subscriber entirely, you can also do that
                    // $em->remove($domain);
                }
            }
            $projectManagers = $project->getProjectManagers();
            foreach ($projectManagers as $projectManager) {
                if ($projectManager->getProject() == null) {
                    $projectManager->setProject($project);
                }
                $common_service->setUserAttributesToContributor($projectManager);
            }

            //***************gestion des holders du project ************************** */
            // remove the relationship between the project and the holders
            foreach ($originalHolders as $holder) {
                if (false === $project->getHolders()->contains($holder)) {
                    // remove the project from the projectManagers
                    $project->getHolders()->removeElement($holder);
                    // if it was a many-to-one relationship, remove the relationship like this
//                    $holder->setProject(null);
//                    $holder->setStatus(0);
                    $repositoryHolder->deleteHolder($holder);
                    // if you wanted to delete the Subscriber entirely, you can also do that
                    // $em->remove($domain);
                }
            }
            $holders = $project->getHolders();
            foreach ($holders as $holder) {
                if ($holder->getProject() == null) {
                    $holder->setProject($project);
                }
                $common_service->setUserAttributesToContributorIfNotExists($holder);
            }

            //***************gestion des prestataires du project ************************** */
            // remove the relationship between the project and the tasks
            foreach ($originalServiceProviders as $serviceProvider) {
                if (false === $project->getServiceProviders()->contains($serviceProvider)) {
                    // remove the project from the projectManagers
                    $project->getServiceProviders()->removeElement($serviceProvider);
                    // if it was a many-to-one relationship, remove the relationship like this
                    $repositoryServiceProvider->deleteServiceProvider($serviceProvider);
                    // if you wanted to delete the Subscriber entirely, you can also do that
                    // $em->remove($domain);
                }
            }
            $serviceProviders = $project->getServiceProviders();
            foreach ($serviceProviders as $serviceProvider) {
                if ($serviceProvider->getProject() == null) {
                    $serviceProvider->setProject($project);
                }
                $common_service->setUserAttributesToContributor($serviceProvider);
            }

            //***************gestion des prestataires du project ************************** */
            // remove the relationship between the project and the tasks
            foreach ($originalOtherContributors as $otherContributor) {
                if (false === $project->getOtherContributors()->contains($otherContributor)) {
                    // remove the project from the projectManagers
                    $project->getOtherContributors()->removeElement($otherContributor);
                    // if it was a many-to-one relationship, remove the relationship like this
                    $repositoryOtherContributors->deleteOtherContributor($otherContributor);
                    // if you wanted to delete the Subscriber entirely, you can also do that
                    // $em->remove($domain);
                }
            }
            $otherContributors = $project->getOtherContributors();
            foreach ($otherContributors as $otherContributor) {
                if ($otherContributor->getProject() == null) {
                    $otherContributor->setProject($project);
                }
                $common_service->setUserAttributesToContributor($otherContributor);
            }
            $numerMarcheTab = explode("/", $project->getNumeroMarche());
            $anneeBudgetaire = $numerMarcheTab[count($numerMarcheTab) - 1];
            $project->setAnneeBudgetaire($anneeBudgetaire);
            $user = $this->getUser();
            $project->setUpdatedUser($user);
            if($project->getAvanceDemarrage() == null){
                $project->setAvanceDemarrage(0);
                $project->setMtAvanceDemarrage(0);
            }
            $project = $repositoryProject->updateProject($project);
            $view = View::create(["message" => 'Projet modifié avec succès. Vous serez redirigé dans bientôt!', "id_project" => $project->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes'], Response::HTTP_BAD_REQUEST);
        }
    }

}
