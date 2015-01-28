<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Parte;
use AppBundle\Form\Model\CambioPassword;
use AppBundle\Form\Type\CambiarPasswordType;
use AppBundle\Form\Type\ModificarUsuarioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UsuarioController extends Controller
{
    /**
     * @Route("/usuario/modificar", name="usuario_modificar",methods={"GET", "POST"})
     */
    public function modificarAction(Request $peticion)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();

        $formulario = $this->createForm(new ModificarUsuarioType(), $usuario, [
            'admin' => $usuario->getEsAdministrador()
        ]);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // Guardar el usuarioo en la base de datos
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Datos guardados correctamente');

            // redireccionar a la portada
            return new RedirectResponse(
                $this->generateUrl('portada')
            );
        }

        return $this->render('AppBundle:Usuario:modificar.html.twig',
            [
                'formulario' => $formulario->createView()
            ]);
    }

    /**
     * @Route("/parte/notificar", name="notificar_parte",methods={"GET"})
     */
    public function notificarAction(Request $peticion)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $partes = $em->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->where('p.fechaAviso IS NULL')
            ->andWhere('p.usuario = :usuario')
            ->setParameter('usuario', $usuario)
            ->orderBy('p.fechaSuceso', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('AppBundle:Parte:notificar.html.twig',
            [
                'usuario' => $usuario,
                'partes' => $partes
            ]);
    }
}
