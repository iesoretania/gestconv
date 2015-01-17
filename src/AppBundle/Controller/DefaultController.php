<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="portada",methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
}
