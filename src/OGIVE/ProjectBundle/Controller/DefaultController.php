<?php

namespace OGIVE\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OGIVE\UserBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;

class DefaultController extends Controller {

    public function indexAction() {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = new User();
            $form = $this->createForm('OGIVE\UserBundle\Form\CreatePersonalAccountType', $user);
            return $this->render('OGIVEProjectBundle::index.html.twig', array(
                        'projects' => null,
                        'form' => $form->createView()
            ));
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $projects = $em->getRepository('OGIVEProjectBundle:Project')->getAll(0, 8, null, $user->getId());
        return $this->render('OGIVEProjectBundle::index.html.twig', array(
                    'projects' => $projects
        ));
    }
    
    /**
     * @Rest\View()
     * @Rest\Get("/about", name="about_page_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getAboutPageAction() {
        return $this->render('OGIVEProjectBundle:page:about.html.twig');
    }
    
    /**
     * @Rest\View()
     * @Rest\Get("/how-it-works", name="how_it_works_page_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getHowItWorksPageAction() {
        return $this->render('OGIVEProjectBundle:page:how-it-works.html.twig');
    }
    
    /**
     * @Rest\View()
     * @Rest\Get("/help", name="help_page_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getHelpPageAction() {
        return $this->render('OGIVEProjectBundle:page:help.html.twig');
    }
    
    /**
     * @Rest\View()
     * @Rest\Get("/user-guide", name="user_guide_page_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getUserGuidePageAction() {
        return $this->render('OGIVEProjectBundle:page:user-guide.html.twig');
    }

}
