<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ModificarUsuarioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UsuarioController extends Controller
{
    /**
     * @Route("/usuario/modificar", name="usuario_modificar",methods={"GET", "POST"})
     */
    public function modificarAction(Request $peticion)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();

        $formulario = $this->createForm(new ModificarUsuarioType(), $usuario, [
            'es_admin' => $usuario->getEsAdministrador(),
            'es_propio' => true
        ]);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            $mensaje = 'Datos guardados correctamente';
            // Guardar el usuario en la base de datos
            $em = $this->getDoctrine()->getManager();

            // Si es solicitado, cambiar la contraseña
            $passwordSubmit = $formulario->get('cambiarPassword');
            if (($passwordSubmit instanceof SubmitButton) && $passwordSubmit->isClicked()) {
                $encoder = $this->container->get('security.password_encoder');
                $password = $encoder->encodePassword($usuario, $formulario->get('newPassword')->get('first')->getData());
                $usuario->setPassword($password);
                $mensaje = 'Datos guardados correctamente y contraseña cambiada';
            }
            $em->flush();

            $this->addFlash('success', $mensaje);

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

}
