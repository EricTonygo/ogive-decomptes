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
        $projectManager = new ProjectManager();
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectManagerType', $projectManager);
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
    public function getUpdateProjectManagerByIdAction(ProjectManager $projectManager) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectManagerType', $projectManager, array('method' => 'PUT'));
        return $this->render('OGIVEProjectBundle:project-manager:update.html.twig', array(
                    'projectManager' => $projectManager,
                    'project' => $projectManager->getProject(),
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
        $projectManager = new ProjectManager();
        $repositoryProjectManager = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:ProjectManager');
        $common_service = $this->get('app.common_service');

        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectManagerType', $projectManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $common_service->setUserAttributesToContributorIfNotExists($projectManager);
            if ($projectManager->getNom() == null || $projectManager->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le nom du maître d'oeuvre. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($projectManager->getEmail() == null || $projectManager->getEmail() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé l'adresse email du maître d'oeuvre. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($projectManager->getPhone() == null || $projectManager->getPhone() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le téléphone du maître d'oeuvre. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryProjectManager->findOneBy(array('email' => $projectManager->getEmail(), "project" => $project))) {
                return new JsonResponse(["success" => false, 'message' => "Un maitre d'oeuvre avec cette adresse email existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryProjectManager->findOneBy(array('email' => $projectManager->getEmail(), "project" => $project))) {
                return new JsonResponse(["success" => false, 'message' => "Un maitre d'oeuvre avec ce numero téléphone existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            
            $projectManager->setProject($project);
            $user = $this->getUser();
            $projectManager->setCreatedUser($user);
            $projectManager = $repositoryProjectManager->saveProjectManager($projectManager);
            $mail_service = $this->get('app.user_mail_service');
            $mail_service->sendNotificationToProjectManagerRegistration($projectManager);
            //return $this->redirect($this->generateUrl('project_contributors_get', array('id' => $projectManager->getProject()->getId())));
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
    public function removeProjectManagerAction(ProjectManager $projectManager) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryProjectManager = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:ProjectManager');
        if ($projectManager) {
            $repositoryProjectManager->deleteProjectManager($projectManager);
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
    public function putProjectManagerAction(Request $request, ProjectManager $projectManager) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $this->updateProjectManagerAction($request, $projectManager);
    }

    public function updateProjectManagerAction(Request $request, ProjectManager $projectManager) {
        $repositoryProjectManager = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:ProjectManager');
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ProjectManagerType', $projectManager, array('method' => 'PUT'));
        $common_service = $this->get('app.common_service');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $common_service->setUserAttributesToContributorIfNotExists($projectManager);
            if ($projectManager->getNom() == null || $projectManager->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le nom du maître d'oeuvre. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($projectManager->getEmail() == null || $projectManager->getEmail() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé l'adresse email du maître d'oeuvre. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($projectManager->getPhone() == null || $projectManager->getPhone() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le téléphone du maître d'oeuvre. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            $projectManagerEdit = $repositoryProjectManager->findOneBy(array('email' => $projectManager->getEmail(), "project" => $projectManager->getProject()));
            if (!is_null($projectManagerEdit) && $projectManagerEdit->getId() != $projectManager->getId()) {
                return new JsonResponse(["success" => false, 'message' => "Un maître d'oeuvre avec cette adresse email existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $projectManagerEditByPhone = $repositoryProjectManager->findOneBy(array('phone' => $projectManager->getEmail(), "project" => $projectManager->getProject()));
            if (!is_null($projectManagerEditByPhone) && $projectManagerEditByPhone->getId() != $projectManager->getId()) {
                return new JsonResponse(["success" => false, 'message' => "Un maître d'oeuvre avec ce numero de téléphone existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $user = $this->getUser();
            $projectManager->setUpdatedUser($user);

            $projectManager = $repositoryProjectManager->updateProjectManager($projectManager);
            if($projectManager->getUser()->getId() != $projectManager->getUser()->getId()){
                $mail_service = $this->get('app.user_mail_service');
                $mail_service->sendNotificationToProjectManagerRegistration($projectManager);
            }
//            return $this->redirect($this->generateUrl('project_contributors_get', array('id' => $projectManager->getProject()->getId())));
            $view = View::create(["message" => "Le maître d'oeuvre a été modifié avec succès. Vous serez redirigé dans bientôt !", 'id_project' => $projectManager->getProject()->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes !'], Response::HTTP_BAD_REQUEST);
        }
    }

}
