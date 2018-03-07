<?php

namespace OGIVE\ProjectBundle\Controller;

use OGIVE\ProjectBundle\Entity\Holder;
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
 * Holder controller.
 *
 */
class HolderController extends Controller {

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/projects/{id}/holders/new", name="holder_add_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function addHolderAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $holder = new Holder();
        $form = $this->createForm('OGIVE\ProjectBundle\Form\HolderType', $holder);
        return $this->render('OGIVEProjectBundle:holder:add.html.twig', array(
                    'form' => $form->createView(),
                    'tab' => 4,
                    'project' => $project
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{idProject}/holders/{id}/update" , name="holder_update_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getUpdateHolderByIdAction(Holder $holder) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $form = $this->createForm('OGIVE\ProjectBundle\Form\HolderType', $holder, array('method' => 'PUT'));
        return $this->render('OGIVEProjectBundle:holder:update.html.twig', array(
                    'holder' => $holder,
                    'project' => $holder->getProject(),
                    'tab' => 4,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/projects/{id}/holders/new", name="holder_add_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postHolderAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $holder = new Holder();
        $repositoryHolder = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Holder');

        $form = $this->createForm('OGIVE\ProjectBundle\Form\HolderType', $holder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($holder->getNom() == null || $holder->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le nom du titulaire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($holder->getEmail() == null || $holder->getEmail() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé l'adresse email du titulaire. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($holder->getPhone() == null || $holder->getPhone() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le téléphone du titulaire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryHolder->findOneBy(array('email' => $holder->getEmail(), "project" => $project))) {
                return new JsonResponse(["success" => false, 'message' => "Un titulaire avec cette adresse email existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryHolder->findOneBy(array('phone' => $holder->getPhone(), "project" => $project))) {
                return new JsonResponse(["success" => false, 'message' => "Un titulaire avec ce numero de téléphone existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $holder->setProject($project);

            $user = $this->getUser();
            $holder->setCreatedUser($user);
            $holder = $repositoryHolder->saveHolder($holder);
            //return $this->redirect($this->generateUrl('project_contributors_get', array('id' => $holder->getProject()->getId())));
            $view = View::create(["message" => "Le titulaire a été ajouté avec succès. Vous serez redirigé dans bientôt !", 'id_project' => $project->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes !'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Delete("/projects/{idProject}/holders/{id}/remove", name="holder_delete", options={ "method_prefix" = false, "expose" = true })
     */
    public function removeHolderAction(Holder $holder) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryHolder = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Holder');
        if ($holder) {
            $repositoryHolder->deleteHolder($holder);
            $view = View::create(["message" => "Titulaire supprimé avec succès !"]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["message" => "Titulaire introuvable !"], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/projects/{idProject}/holders/{id}/update", name="holder_update_post", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function putHolderAction(Request $request, Holder $holder) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $this->updateHolderAction($request, $holder);
    }

    public function updateHolderAction(Request $request, Holder $holder) {
        $repositoryHolder = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Holder');


        $form = $this->createForm('OGIVE\ProjectBundle\Form\HolderType', $holder, array('method' => 'PUT'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($holder->getNom() == null || $holder->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le nom du titulaire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($holder->getEmail() == null || $holder->getEmail() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé l'adresse email du titulaire. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($holder->getPhone() == null || $holder->getPhone() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le téléphone du titulaire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            $holderEdit = $repositoryHolder->findOneBy(array('email' => $holder->getEmail(), "project" => $holder->getProject()));
            if (!is_null($holderEdit) && $holderEdit->getId() != $holder->getId()) {
                return new JsonResponse(["success" => false, 'message' => "Un titulaire avec cette adresse email existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $holderEditByPhone = $repositoryHolder->findOneBy(array('phone' => $holder->getEmail(), "project" => $holder->getProject()));
            if (!is_null($holderEditByPhone) && $holderEditByPhone->getId() != $holder->getId()) {
                return new JsonResponse(["success" => false, 'message' => "Un titulaire avec ce numero de téléphone existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $user = $this->getUser();
            $holder->setUpdatedUser($user);

            $holder = $repositoryHolder->updateHolder($holder);
            //return $this->redirect($this->generateUrl('project_contributors_get', array('id' => $holder->getProject()->getId())));
            $view = View::create(["message" => "Le titulaire a été modifié avec succès. Vous serez redirigé dans bientôt !", 'id_project' => $holder->getProject()->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes !'], Response::HTTP_BAD_REQUEST);
        }
    }

}
