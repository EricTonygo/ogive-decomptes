<?php

namespace OGIVE\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $projects = $em->getRepository('OGIVEProjectBundle:Project')->getAll(1, 8, null, $user);
        return $this->render('OGIVEProjectBundle::index.html.twig', array(
            'projects' => $projects
        ));
    }
}
