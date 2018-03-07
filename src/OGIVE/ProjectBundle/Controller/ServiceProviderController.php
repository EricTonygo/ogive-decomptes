<?php

namespace OGIVE\ProjectBundle\Controller;

use OGIVE\ProjectBundle\Entity\ServiceProvider;
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
 * ServiceProvider controller.
 *
 */
class ServiceProviderController extends Controller {

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/projects/{id}/service-providers/new", name="service_provider_add_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function addServiceProviderAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $service_provider = new ServiceProvider();
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ServiceProviderType', $service_provider);
        return $this->render('OGIVEProjectBundle:service-provider:add.html.twig', array(
                    'form' => $form->createView(),
                    'tab' => 4,
                    'project' => $project
        ));
    }
    

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{idProject}/service-providers/{id}/update" , name="service_provider_update_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getUpdateServiceProviderByIdAction(ServiceProvider $service_provider) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $form = $this->createForm('OGIVE\ProjectBundle\Form\ServiceProviderType', $service_provider, array('method' => 'PUT'));
        return $this->render('OGIVEProjectBundle:service-provider:update.html.twig', array(
                    'service-provider' => $service_provider,
                    'project' => $service_provider->getProject(),
                    'tab' => 4,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/projects/{id}/service-providers/new", name="service_provider_add_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postServiceProviderAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $service_provider = new ServiceProvider();
        $repositoryServiceProvider = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:ServiceProvider');

        $form = $this->createForm('OGIVE\ProjectBundle\Form\ServiceProviderType', $service_provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($service_provider->getNom() == null || $service_provider->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le nom du prestataire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($service_provider->getEmail() == null || $service_provider->getEmail() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé l'adresse email du prestataire. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($service_provider->getPhone() == null || $service_provider->getPhone() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le téléphone du prestataire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryServiceProvider->findOneBy(array('email' => $service_provider->getEmail(), "project" => $project))) {
                return new JsonResponse(["success" => false, 'message' => "Un prestataire avec cette adresse email existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryServiceProvider->findOneBy(array('phone' => $service_provider->getPhone(), "project" => $project))) {
                return new JsonResponse(["success" => false, 'message' => "Un prestataire avec ce numero téléphone existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $service_provider->setProject($project);
            $user = $this->getUser();
            $service_provider->setCreatedUser($user);
            $service_provider = $repositoryServiceProvider->saveServiceProvider($service_provider);
            //return $this->redirect($this->generateUrl('project_contributors_get', array('id' => $service_provider->getProject()->getId())));
            $view = View::create(["message" => "Le prestataire a été ajouté avec succès. Vous serez redirigé dans bientôt !", 'id_project' => $project->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes !'], Response::HTTP_BAD_REQUEST);
        }
    }
    
   
    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Delete("/projects/{idProject}/service-providers/{id}/remove", name="service-provider_delete", options={ "method_prefix" = false, "expose" = true })
     */
    public function removeServiceProviderAction(ServiceProvider $service_provider) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryServiceProvider = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:ServiceProvider');
        if ($service_provider) {
            $repositoryServiceProvider->deleteServiceProvider($service_provider);
            $view = View::create(["message" => "Prestataire supprimé avec succès !"]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["message" => "Prestataire introuvable !"], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/projects/{idProject}/service-providers/{id}/update", name="service_provider_update_post", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function putServiceProviderAction(Request $request, ServiceProvider $service_provider) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $this->updateServiceProviderAction($request, $service_provider);
    }

    public function updateServiceProviderAction(Request $request, ServiceProvider $service_provider) {
        $repositoryServiceProvider = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:ServiceProvider');

        
        $form = $this->createForm('OGIVE\ProjectBundle\Form\ServiceProviderType', $service_provider, array('method' => 'PUT'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($service_provider->getNom() == null || $service_provider->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le nom du prestataire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($service_provider->getEmail() == null || $service_provider->getEmail() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé l'adresse email du prestataire. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($service_provider->getPhone() == null || $service_provider->getPhone() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vous n'avez pas précisé le téléphone du prestataire. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            $serviceProviderEdit = $repositoryServiceProvider->findOneBy(array('email' => $service_provider->getEmail(), "project" => $service_provider->getProject()));
            if (!is_null($serviceProviderEdit) && $serviceProviderEdit->getId() != $service_provider->getId()) {
                return new JsonResponse(["success" => false, 'message' => "Un prestataire avec cette adresse email existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $serviceProviderEditByPhone = $repositoryServiceProvider->findOneBy(array('phone' => $service_provider->getEmail(), "project" => $service_provider->getProject()));
            if (!is_null($serviceProviderEditByPhone) && $serviceProviderEditByPhone->getId() != $service_provider->getId()) {
                return new JsonResponse(["success" => false, 'message' => "Un prestataire avec ce numero de téléphone existe déjà"], Response::HTTP_BAD_REQUEST);
            }
            $user = $this->getUser();
            $service_provider->setUpdatedUser($user);
            
            $service_provider = $repositoryServiceProvider->updateServiceProvider($service_provider);
            //return $this->redirect($this->generateUrl('project_contributors_get', array('id' => $service_provider->getProject()->getId())));
            $view = View::create(["message" => "Le prestataire a été modifié avec succès. Vous serez redirigé dans bientôt !", 'id_project' => $service_provider->getProject()->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes !'], Response::HTTP_BAD_REQUEST);
        }
    }

}
