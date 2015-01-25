<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    /**
     * @Route("/portada", name="portada",methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('AppBundle:App:portada.html.twig');
    }

    /**
     * @Route("/entrar", name="usuario_entrar",methods={"GET", "POST"})
     */
    public function entrarAction(Request $peticion)
    {
        $sesion = $peticion->getSession();

        if ($peticion->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $peticion->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        else {
            $error = $sesion->get(SecurityContext::AUTHENTICATION_ERROR);
            $sesion->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        return $this->render('AppBundle:App:entrada.html.twig',
            [
                'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
                'error' => $error
            ]);
    }
}
