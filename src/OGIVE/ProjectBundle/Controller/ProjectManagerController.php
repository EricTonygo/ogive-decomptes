<?php

namespace OGIVE\ProjectBundle\Controller;

use OGIVE\ProjectBundle\Entity\ProjectManager;
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
 * ProjectManager controller.
 *
 */
class ProjectManagerController extends Controller {

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/projects/{id}/project-managers/new", name="project_manager_add_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function addProjectManagerAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $project_manager = new ProjectManager();
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectManagerType', $project_manager);
        return $this->render('OGIVEProjectBundle:project-manager:add.html.twig', array(
                    'form' => $form->createView(),
                    'tab' => 4,
                    'project' => $project
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{idProject}/project-managers/{id}/update" , name="project_manager_update_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getUpdateProjectManagerByIdAction(ProjectManager $project_manager) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectManagerType', $project_manager, array('method' => 'PUT'));
        return $this->render('OGIVEProjectBundle:project-manager:update.html.twig', array(
                    'projectManager' => $project_manager,
                    'project' => $project_manager->getProject(),
                    'tab' => 4,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/projects/{id}/project-managers/new", name="project_manager_add_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postProjectManagerAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $project_manager = new ProjectManager();
        $repositoryProjectManager = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:ProjectManager');
        $common_service = $this->get('app.common_service');

        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectManagerType', $project_manager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $common_service->setUserAttributesToContributorIfNotExists($project_manager);
            if ($project_manager->getNom() == null || $project_manager->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le nom du maître d'oeuvre. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($project_manager->getEmail() == null || $project_manager->getEmail() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé l'adresse email du maître d'oeuvre. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($project_manager->getPhone() == null || $project_manager->getPhone() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le téléphone du maître d'oeuvre. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryProjectManager->findOneBy(array('email' => $project_manager->getEmail(), "project" => $project))) {
                return new JsonResponse(["success" => false, 'message' => "Un maitre d'oeuvre avec cette adresse email existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryProjectManager->findOneBy(array('email' => $project_manager->getEmail(), "project" => $project))) {
                return new JsonResponse(["success" => false, 'message' => "Un maitre d'oeuvre avec ce numero téléphone existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            
            $project_manager->setProject($project);
            $user = $this->getUser();
            $project_manager->setCreatedUser($user);
            $project_manager = $repositoryProjectManager->saveProjectManager($project_manager);
            //return $this->redirect($this->generateUrl('project_contributors_get', array('id' => $project_manager->getProject()->getId())));
            $view = View::create(["message" => "Le maître d'oeuvre a été ajouté avec succès. Vous serez redirigé dans bientôt !", 'id_project' => $project->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes !'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Delete("/projects/{idProject}/project-managers/{id}/remove", name="project_manager_delete", options={ "method_prefix" = false, "expose" = true })
     */
    public function removeProjectManagerAction(ProjectManager $project_manager) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryProjectManager = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:ProjectManager');
        if ($project_manager) {
            $repositoryProjectManager->deleteProjectManager($project_manager);
            $view = View::create(["message" => "Maître d'oeuvre supprimé avec succès !"]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["message" => "Maître d'oeuvre introuvable !"], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/projects/{idProject}/project-managers/{id}/update", name="project_manager_update_post", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function putProjectManagerAction(Request $request, ProjectManager $project_manager) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $this->updateProjectManagerAction($request, $project_manager);
    }

    public function updateProjectManagerAction(Request $request, ProjectManager $project_manager) {
        $repositoryProjectManager = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:ProjectManager');
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectManagerType', $project_manager, array('method' => 'PUT'));
        $common_service = $this->get('app.common_service');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $common_service->setUserAttributesToContributorIfNotExists($project_manager);
            if ($project_manager->getNom() == null || $project_manager->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le nom du maître d'oeuvre. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($project_manager->getEmail() == null || $project_manager->getEmail() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé l'adresse email du maître d'oeuvre. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($project_manager->getPhone() == null || $project_manager->getPhone() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le téléphone du maître d'oeuvre. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            $projectManagerEdit = $repositoryProjectManager->findOneBy(array('email' => $project_manager->getEmail(), "project" => $project_manager->getProject()));
            if (!is_null($projectManagerEdit) && $projectManagerEdit->getId() != $project_manager->getId()) {
                return new JsonResponse(["success" => false, 'message' => "Un maître d'oeuvre avec cette adresse email existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $projectManagerEditByPhone = $repositoryProjectManager->findOneBy(array('phone' => $project_manager->getEmail(), "project" => $project_manager->getProject()));
            if (!is_null($projectManagerEditByPhone) && $projectManagerEditByPhone->getId() != $project_manager->getId()) {
                return new JsonResponse(["success" => false, 'message' => "Un maître d'oeuvre avec ce numero de téléphone existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $user = $this->getUser();
            $project_manager->setUpdatedUser($user);

            $project_manager = $repositoryProjectManager->updateProjectManager($project_manager);
//            return $this->redirect($this->generateUrl('project_contributors_get', array('id' => $project_manager->getProject()->getId())));
            $view = View::create(["message" => "Le maître d'oeuvre a été modifié avec succès. Vous serez redirigé dans bientôt !", 'id_project' => $project_manager->getProject()->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes !'], Response::HTTP_BAD_REQUEST);
        }
    }

}
