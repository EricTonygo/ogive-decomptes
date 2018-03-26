<?php

namespace OGIVE\UserBundle\Controller;

use OGIVE\UserBundle\Entity\User;
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
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * User controller.
 *
 */
class UserController extends Controller {

    public function setUserSessionAttributes(User $user, $new_user_array) {
        $user->setLastname($new_user_array['lastname']);
        $user->setUsername($new_user_array['username']);
        $user->setEmail($new_user_array['email']);
        //$user->setPlainPassword($new_user_array['lastname']);
    }

    /**
     * @Rest\View()
     * @Rest\Get("/register/create-personal-account", name="create_personal_account_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function createPersonalAccountAction(Request $request) {
        $user = new User();
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        if ($session->get('new_user')) {
            $new_user_array = $session->get('new_user');
            $this->setUserSessionAttributes($user, $new_user_array);
        }
        $form = $this->createForm('OGIVE\UserBundle\Form\CreatePersonalAccountType', $user);
        return $this->render('OGIVEUserBundle:user:create-personal-account.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user,
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/register/choose-your-plan", name="choose_your_plan_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function chooseYourPlanAction(Request $request) {
        $user = new User();
        $new_user_array = array();
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        if ($session->get('new_user')) {
            $new_user_array = $session->get('new_user');
            $form = $this->createForm('OGIVE\UserBundle\Form\ChooseYourPlanType', $user);
            return $this->render('OGIVEUserBundle:user:choose-your-plan.html.twig', array(
                        'form' => $form->createView(),
                        'user' => $new_user_array,
            ));
        } else {
            $session->getFlashBag()->add('warning', "You have to create your personal informations before to choose a plan.");
            return $this->redirect($this->generateUrl('create_personal_account_get'));
        }
    }

    /**
     * @Rest\View()
     * @Rest\Get("/register/confirm-informations", name="confirm_informations_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function confirmInformationsAction(Request $request) {
        $user = new User();
        $new_user_array = array();
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        if ($session->get('new_user')) {
            $new_user_array = $session->get('new_user');
            $form = $this->createForm('OGIVE\UserBundle\Form\ConfirmInformationsType', $user);
            return $this->render('OGIVEUserBundle:user:confirm-informations.html.twig', array(
                        'form' => $form->createView(),
                        'user' => $new_user_array
            ));
        } else {
            $session->getFlashBag()->add('warning', "You have to create your personal informations before to confirm your informations.");
            return $this->redirect($this->generateUrl('create_personal_account_get'));
        }
    }

    /**
     * @Rest\View()
     * @Rest\Get("/register/activate/{username}/{hash}", name="activate_user_account_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function activateAccountAction(Request $request, $username, $hash) {
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        $user = $userManager->findUserByUsername($username);
        if ($user) {
            if (!$user->getEnabled()) {
                if ($user->getActivationHash() == $hash) {
                    $user->setEnabled(true);
                    $userManager->updateUser($user);
                    $session->getFlashBag()->add('success', "Votre compte a été activé. Connectez-vous pour utiliser notre service !");
                    return $this->redirect($this->generateUrl('fos_user_security_login'));
                } else {
                    $session->getFlashBag()->add('error', "Le code d'activation utilisé est incorrect !");
                    return $this->redirect($this->generateUrl('fos_user_security_login'));
                }
            } else {
                $session->getFlashBag()->add('warning', "Cet utilisateur est déjà activé. Connectez-vous pour utiliser notre service !");
                return $this->redirect($this->generateUrl('fos_user_security_login'));
            }
        } else {
            $session->getFlashBag()->add('error', "L'utilisateur " . $username . " n'existe pas!");
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/register/create-personal-account", name="create_personal_account_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postCreatePersonalAccountAction(Request $request) {
        $user = new User();
        $form = $this->createForm('OGIVE\UserBundle\Form\CreatePersonalAccountType', $user);
        $form->handleRequest($request);
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        if ($form->isSubmitted() && $form->isValid()) {
            $new_user_array = array("lastname" => $user->getLastname(), 'username' => $user->getUsername(), 'email' => $user->getEmail(), 'plainPassword' => $user->getPlainPassword());
            if ($session->get('new_user')) {
                $session->remove('new_user');
            }
            $session->set('new_user', $new_user_array);
            if ($userManager->findUserByEmail($user->getEmail())) {
                $session->getFlashBag()->add('error', "Un utilisateur avec cette adresse email existe dejà!");
                return $this->redirect($this->generateUrl('create_personal_account_get'));
            }
            if ($userManager->findUserByUsername($user->getUsername())) {
                $session->getFlashBag()->add('error', "Un utilisateur avec ce nom d'utilisateur existe dejà!");
                return $this->redirect($this->generateUrl('create_personal_account_get'));
            }

            return $this->redirect($this->generateUrl('choose_your_plan_get'));
        } else {
            $session->getFlashBag()->add('error', "Le formulaire a été soumis avec des données erronées");
            return $this->redirect($this->generateUrl('create_personal_account_get'));
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/register/choose-your-plan", name="choose_your_plan_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postChooseYourPlanAction(Request $request) {
        $choosePlanUser = new User();
        $form = $this->createForm('OGIVE\UserBundle\Form\ChooseYourPlanType', $choosePlanUser);
        $form->handleRequest($request);
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        if ($form->isSubmitted() && $form->isValid()) {
            $new_user_array = array();
            if ($session->get('new_user')) {
                $new_user_array = $session->get('new_user');
                $session->remove('new_user');
            }
            $new_user_array['choosedPlan'] = intval($request->get('choosed-plan'));
            $session->set('new_user', $new_user_array);
            return $this->redirect($this->generateUrl('confirm_informations_get'));
        } else {
            $session->getFlashBag()->add('error', "Le formulaire a été soumis avec des données erronées");
            return $this->redirect($this->generateUrl('choose_your_plan_get'));
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/register/confirm-informations", name="confirm_informations_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postConfirmInformationsAction(Request $request) {
        $choosePlanUser = new User();
        $form = $this->createForm('OGIVE\UserBundle\Form\ConfirmInformationsType', $choosePlanUser);
        $form->handleRequest($request);
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $mail_service = $this->get('app.user_mail_service');
        if ($form->isSubmitted() && $form->isValid()) {
            $new_user_array = array();
            if ($session->get('new_user')) {
                $new_user_array = $session->get('new_user');
                $session->remove('new_user');
            }
            $new_user_array['confirmedInformations'] = 1;
            $session->set('new_user', $new_user_array);
            $user = $userManager->createUser();

            $hash = sha1(uniqid(mt_rand(), true));
            $user->setActivationHash($hash);
            $user->setLastname($new_user_array['lastname']);
            $user->setUsername($new_user_array['username']);
            $user->setEmail($new_user_array['email']);
            $user->setPlainPassword($new_user_array['plainPassword']);
            $user->addRole("ROLE_ADMIN");
            if ($userManager->findUserByEmail($user->getEmail())) {
                $session->getFlashBag()->add('error', "Un utilisateur avec cette adresse email existe dejà!");
                return $this->redirect($this->generateUrl('create_personal_account_get'));
            }
            if ($userManager->findUserByUsername($user->getUsername())) {
                $session->getFlashBag()->add('error', "Un utilisateur avec ce nom d'utilisateur existe dejà!");
                return $this->redirect($this->generateUrl('create_personal_account_get'));
            }
            $savedUser = $userManager->updateUser($user);
            $session->remove('new_user');
            $mail_service->sendAccountActivationLink($user);
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        } else {
            $session->getFlashBag()->add('error', "Le formulaire a été soumis avec des données erronées");
            return $this->redirect($this->generateUrl('confirm_informations_get'));
        }
    }

}
