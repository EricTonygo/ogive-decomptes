<?php

namespace OGIVE\ProjectBundle\Controller;

use OGIVE\ProjectBundle\Entity\Project;
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
        $project = new Project();
        $page = 1;
        $maxResults = 6;
        $route_param_page = array();
        $route_param_search_query = array();
        $search_query = null;
        $start_date = null;
        $end_date = null;
        $owner = null;
        $domain = null;
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
        if ($request->get('domain')) {
            $domain = htmlspecialchars(trim($request->get('domain')));
            $route_param_search_query['domain'] = $domain;
        }
        $start_from = ($page - 1) * $maxResults >= 0 ? ($page - 1) * $maxResults : 0;
        $total_procedures = count($em->getRepository('OGIVEProjectBundle:Project')->getAllByQueriedParameters($search_query, $start_date, $end_date, $owner, $domain));
        $total_pages = ceil($total_procedures / $maxResults);
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectType', $project);
        $projects = $em->getRepository('OGIVEProjectBundle:Project')->getAll($start_from, $maxResults, $search_query, $start_date, $end_date, $owner, $domain);
        $owners = $em->getRepository('OGIVEProjectBundle:Owner')->findBy(array("state" => 1, "status" => 1));
        $domains = $em->getRepository('OGIVEProjectBundle:Domain')->findBy(array("state" => 1, "status" => 1));
        if ($start_date && $end_date) {
            //$this->get('common_service')->getStatisticsOfProceduresByOwner($start_date, $end_date);
            $this->get('common_service')->getStatisticsOfProceduresByMonth($start_date, $end_date);
        }
        return $this->render('OGIVEProjectBundle:project:index.html.twig', array(
                    'projects' => $projects,
                    'total_procedures' => $total_procedures,
                    'total_pages' => $total_pages,
                    'page' => $page,
                    'form' => $form->createView(),
                    'route_param_page' => $route_param_page,
                    'route_param_search_query' => $route_param_search_query,
                    'search_query' => $search_query,
                    'placeholder' => $placeholder,
                    'owners' => $owners,
                    'domains' => $domains,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'queried_owner' => $owner,
                    'queried_domain' => $domain,
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
     * @Rest\Get("/projects/{id}/general-informations" , name="project_gen_infos_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getProjectGenInfosByIdAction(Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $projects = $em->getRepository('OGIVEProjectBundle:Project')->getAll(0, 8, null, $user->getId());
        $projectManager = $project->getProjectManagers()[0];
        $holder = $project->getHolders()[0];
        return $this->render('OGIVEProjectBundle:project:general_informations_project.html.twig', array(
            'project' => $project,
            'projects' => $projects,
            'projectManager' => $projectManager,
            'holder' => $holder
        ));
        
    }
    
    /**
     * @Rest\View()
     * @Rest\Get("/projects/{id}/lots" , name="project_lots_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getProjectLotsByIdAction(Request $request, Project $project) {
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
        $placeholder = "Rechercher un lot...";
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
        $total_lots = count($em->getRepository('OGIVEProjectBundle:Project')->getAll(null, null, $search_query, $project->getId()));
        $total_pages = ceil($total_lots / $maxResults);        
        $lots = $em->getRepository('OGIVEProjectBundle:Lot')->getAll($start_from, $maxResults, $search_query, $project->getId());
        $projects = $em->getRepository('OGIVEProjectBundle:Project')->getAll(0, 8, null, $user->getId());
        return $this->render('OGIVEProjectBundle:project:project-lots.html.twig', array(
            'project' => $project,
            'projects' => $projects,
            'lots' => $lots,
            'total_pages' => $total_pages,
            'total_lots' => $total_lots,
            'placeholder' => $placeholder
        ));
        
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/projects", name="project_add_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postProjectsAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $project = new Project();
        $repositoryProject = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Project');

        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectType', $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $project->setCreatedUser($user);
            //***************gestion des projectManagers du projet ************************** */
            $projectManagers = $project->getProjectManagers();
            foreach ($projectManagers as $projectManager) {
                $projectManager->setProject($project);
            }
            
            //***************gestion des titulaire du projet ************************** */
            $holders = $project->getHolders();
            foreach ($holders as $holder) {
                $holder->setProject($project);
            }
            
            //***************gestion des lots du projet ************************** */
            $lots = $project->getLots();
            foreach ($lots as $lot) {
                $lot->setProject($project);
            }
            $project = $repositoryProject->saveProject($project);
            return $this->redirect($this->generateUrl('ogive_project_homepage'));
        } else {
            return $this->render('OGIVEProjectBundle:project:add.html.twig', array(
                        'form' => $form->createView()
            ));
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Delete("/projects/{id}", name="project_delete", options={ "method_prefix" = false, "expose" = true })
     */
    public function removeProjectAction(Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryProject = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Project');
        if ($project) {
            $repositoryProject->deleteProject($project);
            $view = View::create(["message" => "Appel d'offre supprimé avec succès !"]);
            $view->setFormat('json');
            return $view;
//            return new JsonResponse(["message" => "Appel d'offre supprimé avec succès !"], Response::HTTP_OK);
        } else {
            return new JsonResponse(["message" => "Appel d'offre introuvable"], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/projects/{id}", name="project_update", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function putProjectAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $this->updateProjectAction($request, $project);
    }

    public function updateProjectAction(Request $request, Project $project) {

        $repositoryProject = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Project');
        $repositoryProjectManager = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:ProjectManager');
        $repositoryHolder = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Holder');
        $repositoryLot = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Lot');

        $originalProjectManagers = new \Doctrine\Common\Collections\ArrayCollection();
        $originalHolders = new \Doctrine\Common\Collections\ArrayCollection();
        $originalLots = new \Doctrine\Common\Collections\ArrayCollection();
        
        foreach ($project->getProjectManagers() as $projectManager) {
            $originalProjectManagers->add($projectManager);
        }
        foreach ($project->getHolders() as $holder) {
            $originalHolders->add($holder);
        }
        foreach ($project->getLots() as $lot) {
            $originalLots->add($lot);
        }
    
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectType', $project, array('method' => 'PUT'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            //***************gestion des projectManagers du project ************************** */
            // remove the relationship between the project and the projectManagers
            foreach ($originalProjectManagers as $projectManager) {
                if (false === $project->getProjectManagers()->contains($projectManager)) {
                    // remove the project from the projectManagers
                    $project->getProjectManagers()->removeElement($projectManager);
                    // if it was a many-to-one relationship, remove the relationship like this
                    $projectManager->setProject(null);
                    $projectManager->setStatus(0);
                    $repositoryProjectManager->updateProjectManager($projectManager);
                    // if you wanted to delete the Subscriber entirely, you can also do that
                    // $em->remove($domain);
                }
            }
            $projectManagers = $project->getProjectManagers();
            foreach ($projectManagers as $projectManager) {
                if($projectManager->getProject() == null){
                    $projectManager->setProject($project);
                }
            }
            
            //***************gestion des holders du project ************************** */
            // remove the relationship between the project and the holders
            foreach ($originalHolders as $holder) {
                if (false === $project->getHolders()->contains($holder)) {
                    // remove the project from the projectManagers
                    $project->getHolders()->removeElement($holder);
                    // if it was a many-to-one relationship, remove the relationship like this
                    $holder->setProject(null);
                    $holder->setStatus(0);
                    $repositoryHolder->updateHolder($holder);
                    // if you wanted to delete the Subscriber entirely, you can also do that
                    // $em->remove($domain);
                }
            }
            $holders = $project->getHolders();
            foreach ($holders as $holder) {
                if($holder->getProject() == null){
                    $holder->setProject($project);
                }
            }
            
            //***************gestion des lots du project ************************** */
            // remove the relationship between the project and the lots
            foreach ($originalLots as $lot) {
                if (false === $project->getLots()->contains($lot)) {
                    // remove the project from the projectManagers
                    $project->getLots()->removeElement($lot);
                    // if it was a many-to-one relationship, remove the relationship like this
                    $lot->setProject(null);
                    $lot->setStatus(0);
                    $repositoryLot->updateLot($lot);
                    // if you wanted to delete the Subscriber entirely, you can also do that
                    // $em->remove($domain);
                }
            }
            $lots = $project->getLots();
            foreach ($lots as $lot) {
                if($lot->getProject() == null){
                    $lot->setProject($project);
                }
            }
            
            $user = $this->getUser();
            $project->setUpdatedUser($user);
            $project = $repositoryProject->updateProject($project);
            
            return $this->redirect($this->generateUrl('ogive_project_homepage'));
        }  else {
            return $this->render('OGIVEProjectBundle:project:edit.html.twig', array('form' => $form->createView(), 'project' => $project));
            
        }
    }

}
