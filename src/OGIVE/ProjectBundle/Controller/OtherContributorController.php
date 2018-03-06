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
        $other_contributor = new OtherContributor();
        $form = $this->createForm('OGIVE\ProjectBundle\Form\OtherContributorType', $other_contributor);
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
    public function getUpdateOtherContributorByIdAction(OtherContributor $other_contributor) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $form = $this->createForm('OGIVE\ProjectBundle\Form\OtherContributorType', $other_contributor, array('method' => 'PUT'));
        return $this->render('OGIVEProjectBundle:other-contributor:update.html.twig', array(
                    'projectManager' => $other_contributor,
                    'project' => $other_contributor->getProject(),
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
        $other_contributor = new OtherContributor();
        $repositoryOtherContributor = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:OtherContributor');

        $form = $this->createForm('OGIVE\ProjectBundle\Form\OtherContributorType', $other_contributor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($other_contributor->getNom() == null || $other_contributor->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le nom du prestataire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($other_contributor->getEmail() == null || $other_contributor->getEmail() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé l'adresse email du prestataire. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($other_contributor->getPhone() == null || $other_contributor->getPhone() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le téléphone du prestataire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            $other_contributor->setProject($project);

            $user = $this->getUser();
            $other_contributor->setCreatedUser($user);
            $other_contributor = $repositoryOtherContributor->saveOtherContributor($other_contributor);
            //return $this->redirect($this->generateUrl('project_contributors_get', array('id' => $other_contributor->getProject()->getId())));
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
    public function removeOtherContributorAction(OtherContributor $other_contributor) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryOtherContributor = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:OtherContributor');
        if ($other_contributor) {
            $repositoryOtherContributor->deleteOtherContributor($other_contributor);
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
    public function putOtherContributorAction(Request $request, OtherContributor $other_contributor) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $this->updateOtherContributorAction($request, $other_contributor);
    }

    public function updateOtherContributorAction(Request $request, OtherContributor $other_contributor) {
        $repositoryOtherContributor = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:OtherContributor');


        $form = $this->createForm('OGIVE\ProjectBundle\Form\OtherContributorType', $other_contributor, array('method' => 'PUT'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($other_contributor->getNom() == null || $other_contributor->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le nom du prestataire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($other_contributor->getEmail() == null || $other_contributor->getEmail() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé l'adresse email du prestataire. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($other_contributor->getPhone() == null || $other_contributor->getPhone() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le téléphone du prestataire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            $user = $this->getUser();
            $other_contributor->setUpdatedUser($user);

            $other_contributor = $repositoryOtherContributor->updateOtherContributor($other_contributor);
            //return $this->redirect($this->generateUrl('project_contributors_get', array('id' => $other_contributor->getProject()->getId())));
            $view = View::create(["message" => "L'intervenant a été modifié avec succès. Vous serez redirigé dans bientôt !", 'id_project' => $other_contributor->getProject()->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes !'], Response::HTTP_BAD_REQUEST);
        }
    }

}
