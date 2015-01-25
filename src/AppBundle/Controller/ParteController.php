<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Parte;
use AppBundle\Form\Parte\NuevoParteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ParteController extends Controller
{
    /**
     * @Route("/parte/nuevo", name="nuevo-parte",methods={"GET"})
     */
    public function nuevoAction(Request $peticion)
    {
        $parte = new Parte();
        $parte->setFechaCreacion(new \DateTime());
        $parte->setFechaSuceso(new \DateTime());
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
