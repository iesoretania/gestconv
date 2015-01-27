<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Parte;
use AppBundle\Form\Type\NuevoParteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        $parte->setPrescrito(false);

        $formulario = $this->createForm(new NuevoParteType(), $parte, [
            'admin' => $usuario->getEsAdministrador()
        ]);

        $formulario->handleRequest($peticion);
        if (!$usuario->getEsAdministrador()) {
            $parte->setUsuario($usuario);
        }

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // crear el parte y guardarlo en la base de datos
            $em = $this->getDoctrine()->getManager();
            $em->persist($parte);
            $em->flush();

            // redireccionar a la portada
            return new RedirectResponse(
                $this->generateUrl('portada')
            );
        }

        $vista = $formulario->createView();

        return $this->render('AppBundle:Parte:nuevo.html.twig',
            [
                'parte' => $parte,
                'formulario' => $vista
            ]);
    }
}
