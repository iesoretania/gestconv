<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Curso;
use AppBundle\Form\Type\CursoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/curso")
 */

class CursoController extends Controller
{
    /**
     * @Route("/modificar/{curso}", name="curso_modificar",methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function modificarAction(Curso $curso, Request $peticion)
    {
        $usuarioActivo = $this->get('security.token_storage')->getToken()->getUser();

        $formulario = $this->createForm(new CursoType(), $curso);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // Guardar el usuario en la base de datos
            $em = $this->getDoctrine()->getManager();

            $em->flush();

            $this->addFlash('success', 'Datos guardados correctamente');

            // redireccionar al listado de grupos
            return new RedirectResponse(
                $this->generateUrl('grupo_listar')
            );
        }

        return $this->render('AppBundle:Curso:modificar.html.twig',
            [
                'formulario' => $formulario->createView()
            ]);
    }
}
