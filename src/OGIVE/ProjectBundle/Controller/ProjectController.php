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
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
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
     * @Rest\Get("/projects/{id}" , name="project_get_one", options={ "method_prefix" = false, "expose" = true })
     */
    public function getProjectByIdAction(Project $project) {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if (empty($project)) {
            return new JsonResponse(['message' => "Appel d'offre introuvable"], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectType', $project, array('method' => 'PUT'));
        $project_details = $this->renderView('OGIVEProjectBundle:project:show.html.twig', array(
            'project' => $project,
            'form' => $form->createView()
        ));
        $view = View::create(['project_details' => $project_details]);
        $view->setFormat('json');
        return $view;
//        return new JsonResponse(["code" => 200, 'project_details' => $project_details], Response::HTTP_OK);
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/projects", name="project_add_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postProjectsAction(Request $request) {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $project = new Project();
        $repositoryProject = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Project');
        $repositoryOwner = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Owner');

        if ($request->get('testunicity') == 'yes' && $request->get('reference')) {
            $reference = $request->get('reference');
            if ($repositoryProject->findOneBy(array('reference' => $reference, 'status' => 1))) {
                return new JsonResponse(["success" => false, 'message' => "Un appel d'offre avec cette référence existe dejà !"], Response::HTTP_BAD_REQUEST);
            } else {
                return new JsonResponse(['message' => "Add Call offer is possible !"], Response::HTTP_OK);
            }
        }

        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectType', $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
                $sendActivate = $request->get('send_activate');
                if ($sendActivate && $sendActivate === 'on') {
                    $project->setState(1);
                }
            }
            $project->setType($request->get('project_type'));
            $project->setAbstract($this->getAbstractOfProject($project));
            $user = $this->getUser();
            $project->setUser($user);
            $project = $repositoryProject->saveProject($project);
            $curl_response = $this->get('curl_service')->sendProjectToWebsite($project);
            $curl_response_array = json_decode($curl_response, true);
            $project->setUrlDetails($curl_response_array['data']['url']);
            $project->setAbstract($this->getAbstractOfProject($project, $project->getUrlDetails()));
            $repositoryProject->updateProject($project);
            $repositoryOwner->saveOwnerForProcedure($project);
            $view = View::createRedirect($this->generateUrl('project_index'));
            $view->setFormat('html');
            return $view;
        } else {
            $view = View::create($form);
            $view->setFormat('json');
            return $view;
//            return new JsonResponse($form, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Delete("/projects/{id}", name="project_delete", options={ "method_prefix" = false, "expose" = true })
     */
    public function removeProjectAction(Project $project) {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
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
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $this->updateProjectAction($request, $project);
    }

    public function updateProjectAction(Request $request, Project $project) {

        $repositoryProject = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Project');
        $repositoryOwner = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Owner');

        if (empty($project)) {
            return new JsonResponse(['message' => "Appel d'offre introuvable"], Response::HTTP_NOT_FOUND);
        }

        if ($request->get('testunicity') == 'yes' && $request->get('reference')) {
            $reference = $request->get('reference');
            $projectUnique = $repositoryProject->findOneBy(array('reference' => $reference, 'status' => 1));
            if ($projectUnique && $projectUnique->getId() != $project->getId()) {
                return new JsonResponse(["success" => false, 'message' => "Un appel d'offre avec cette référence existe dejà"], Response::HTTP_BAD_REQUEST);
            } else {
                return new JsonResponse(['message' => "Update Call offer is possible !"], Response::HTTP_OK);
            }
        }

        if ($request->get('action') == 'enable') {
            $project->setState(1);
//            $curl_response = $this->get('curl_service')->sendProjectToWebsite($project);
//            $curl_response_array = json_decode($curl_response, true);
//            $project->setAbstract($this->getAbstractOfProject($project,  $curl_response_array['data']['url']));
            $project = $repositoryProject->updateProject($project);
            return new JsonResponse(['message' => "Appel d'offre activé avec succcès !"], Response::HTTP_OK);
        }

        if ($request->get('action') == 'disable') {
            $project->setState(0);
//            $curl_response = $this->get('curl_service')->sendProjectToWebsite($project);
//            $curl_response_array = json_decode($curl_response, true);
//            $project->setAbstract($this->getAbstractOfProject($project,  $curl_response_array['data']['url']));
            $project = $repositoryProject->updateProject($project);
            return new JsonResponse(['message' => "Appel d'offre désactivé avec succcès !"], Response::HTTP_OK
            );
        }
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectType', $project, array('method' => 'PUT'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $project->setType($request->get('project_type'));

            if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
                $sendActivate = $request->get('send_activate');
                if ($sendActivate && $sendActivate === 'on') {
                    $project->setState(1);
                } else {
                    $project->setState(0);
                }
            }
            $project->setAbstract($this->getAbstractOfProject($project));
            $user = $this->getUser();
            $project->setUpdatedUser($user);
            $project = $repositoryProject->updateProject($project);
            $curl_response = $this->get('curl_service')->sendProjectToWebsite($project);
            $curl_response_array = json_decode($curl_response, true);
            $project->setUrlDetails($curl_response_array['data']['url']);
            $project->setAbstract($this->getAbstractOfProject($project, $project->getUrlDetails()));
            $repositoryProject->updateProject($project);
            $repositoryOwner->saveOwnerForProcedure($project);
            $view = View::createRedirect($this->generateUrl('project_index'));
            $view->setFormat('html');
            return $view;
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $view = View::create($form);
            $view->setFormat('json');
            return $view;
            //return new JsonResponse($form, Response::HTTP_BAD_REQUEST);
        } else {
            $edit_project_form = $this->renderView('OGIVEProjectBundle:project:edit.html.twig', array('form' => $form->createView(), 'project' => $project));
            $view = View::create(['edit_project_form' => $edit_project_form]);
            $view->setFormat('json');
            return $view;
            //return new JsonResponse(["code" => 200, 'edit_project_form' => $edit_project_form], Response::HTTP_OK);
        }
    }

}
