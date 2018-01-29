<?php

namespace OGIVE\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('OGIVEUserBundle:Default:index.html.twig');
    }
}
