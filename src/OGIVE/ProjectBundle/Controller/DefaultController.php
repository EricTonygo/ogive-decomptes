<?php

namespace OGIVE\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OGIVE\UserBundle\Entity\User;

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

}
