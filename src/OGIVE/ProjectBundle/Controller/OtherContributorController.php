<?php

namespace OGIVE\ProjectBundle\Controller;

use OGIVE\ProjectBundle\Entity\OtherContributor;
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
 * OtherContributor controller.
 *
 */
class OtherContributorController extends Controller {

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/projects/{id}/other-contributors/new", name="other_contributor_add_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function addOtherContributorAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $otherContributor = new OtherContributor();
        $form = $this->createForm('OGIVE\ProjectBundle\Form\OtherContributorType', $otherContributor);
        return $this->render('OGIVEProjectBundle:other-contributor:add.html.twig', array(
                    'form' => $form->createView(),
                    'tab' => 4,
                    'project' => $project
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{idProject}/other-contributors/{id}/update" , name="other_contributor_update_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getUpdateOtherContributorByIdAction(OtherContributor $otherContributor) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $form = $this->createForm('OGIVE\ProjectBundle\Form\OtherContributorType', $otherContributor, array('method' => 'PUT'));
        return $this->render('OGIVEProjectBundle:other-contributor:update.html.twig', array(
                    'otherContributor' => $otherContributor,
                    'project' => $otherContributor->getProject(),
                    'tab' => 4,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/projects/{id}/other-contributors/new", name="other_contributor_add_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postOtherContributorAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $otherContributor = new OtherContributor();
        $repositoryOtherContributor = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:OtherContributor');
        $common_service = $this->get('app.common_service');

        $form = $this->createForm('OGIVE\ProjectBundle\Form\OtherContributorType', $otherContributor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $common_service->setUserAttributesToContributorIfNotExists($otherContributor);
            if ($otherContributor->getNom() == null || $otherContributor->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le nom du prestataire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($otherContributor->getEmail() == null || $otherContributor->getEmail() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé l'adresse email du prestataire. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($otherContributor->getPhone() == null || $otherContributor->getPhone() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le téléphone du prestataire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryOtherContributor->findOneBy(array('email' => $otherContributor->getEmail(), "project" => $project))) {
                return new JsonResponse(["success" => false, 'message' => "Un intervenant avec cette adresse email existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryOtherContributor->findOneBy(array('phone' => $otherContributor->getPhone(), "project" => $project))) {
                return new JsonResponse(["success" => false, 'message' => "Un intervenant avec ce numero téléphone existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $otherContributor->setProject($project);

            $user = $this->getUser();
            $otherContributor->setCreatedUser($user);
            $otherContributor = $repositoryOtherContributor->saveOtherContributor($otherContributor);
            $mail_service = $this->get('app.user_mail_service');
            $mail_service->sendNotificationToOtherContributorRegistration($otherContributor);
            //return $this->redirect($this->generateUrl('project_contributors_get', array('id' => $otherContributor->getProject()->getId())));
            $view = View::create(["message" => "L'intervenant a été ajouté avec succès. Vous serez redirigé dans bientôt !", 'id_project' => $project->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes !'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Delete("/projects/{idProject}/other-contributors/{id}/remove", name="other_contributor_delete", options={ "method_prefix" = false, "expose" = true })
     */
    public function removeOtherContributorAction(OtherContributor $otherContributor) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryOtherContributor = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:OtherContributor');
        if ($otherContributor) {
            $repositoryOtherContributor->deleteOtherContributor($otherContributor);
            $view = View::create(["message" => "Intervant supprimé avec succès !"]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["message" => "Intervenant introuvable !"], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/projects/{idProject}/other-contributors/{id}/update", name="other_contributor_update_post", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function putOtherContributorAction(Request $request, OtherContributor $otherContributor) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $this->updateOtherContributorAction($request, $otherContributor);
    }

    public function updateOtherContributorAction(Request $request, OtherContributor $otherContributor) {
        $repositoryOtherContributor = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:OtherContributor');

        $common_service = $this->get('app.common_service');
        $form = $this->createForm('OGIVE\ProjectBundle\Form\OtherContributorType', $otherContributor, array('method' => 'PUT'));
        $olderOtherContributor = $otherContributor;
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $common_service->setUserAttributesToContributorIfNotExists($otherContributor);
            if ($otherContributor->getNom() == null || $otherContributor->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le nom du prestataire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($otherContributor->getEmail() == null || $otherContributor->getEmail() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé l'adresse email du prestataire. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($otherContributor->getPhone() == null || $otherContributor->getPhone() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le téléphone du prestataire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            $otherContributorEdit = $repositoryOtherContributor->findOneBy(array('email' => $otherContributor->getEmail(), "project" => $otherContributor->getProject()));
            if (!is_null($otherContributorEdit) && $otherContributorEdit->getId() != $otherContributor->getId()) {
                return new JsonResponse(["success" => false, 'message' => "Un titulaire avec cette adresse email existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $otherContributorEditByPhone = $repositoryOtherContributor->findOneBy(array('phone' => $otherContributor->getEmail(), "project" => $otherContributor->getProject()));
            if (!is_null($otherContributorEditByPhone) && $otherContributorEditByPhone->getId() != $otherContributor->getId()) {
                return new JsonResponse(["success" => false, 'message' => "Un titulaire avec ce numero de téléphone existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $user = $this->getUser();
            $otherContributor->setUpdatedUser($user);

            $otherContributor = $repositoryOtherContributor->updateOtherContributor($otherContributor);
            if($otherContributor->getUser()->getId() != $olderOtherContributor->getUser()->getId()){
                $mail_service = $this->get('app.user_mail_service');
                $mail_service->sendNotificationToOtherContributorRegistration($otherContributor);
            }
            //return $this->redirect($this->generateUrl('project_contributors_get', array('id' => $otherContributor->getProject()->getId())));
            $view = View::create(["message" => "L'intervenant a été modifié avec succès. Vous serez redirigé dans bientôt !", 'id_project' => $otherContributor->getProject()->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes !'], Response::HTTP_BAD_REQUEST);
        }
    }

}
