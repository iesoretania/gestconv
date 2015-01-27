<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Parte;
use AppBundle\Form\Type\NuevoParteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ParteController extends Controller
{
    /**
     * @Route("/parte/nuevo", name="nuevo_parte",methods={"GET", "POST"})
     */
    public function nuevoAction(Request $peticion)
    {
        $parte = new Parte();
        $usuario = $this->get('security.token_storage')->getToken()->getUser();

        $parte->setFechaCreacion(new \DateTime());
        $parte->setFechaSuceso(new \DateTime());
        $parte->setUsuario($usuario);
        $formulario = $this->createForm(new NuevoParteType(), $parte);

        $formulario->handleRequest($peticion);

        $vista = $formulario->createView();

        return $this->render('AppBundle:Parte:nuevo.html.twig',
            [
                'parte' => $parte,
                'formulario' => $vista
            ]);
    }
}
