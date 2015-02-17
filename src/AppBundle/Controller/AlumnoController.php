<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Alumno;
use AppBundle\Entity\AvisoSancion;
use AppBundle\Entity\ObservacionSancion;
use AppBundle\Entity\Sancion;
use AppBundle\Form\Type\AlumnoType;
use AppBundle\Form\Type\NuevaObservacionType;
use AppBundle\Form\Type\NuevaSancionType;
use AppBundle\Form\Type\SancionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/alumno")
 */

class AlumnoController extends Controller
{
    /**
     * @Route("/tutoria", name="alumno_tutoria",methods={"GET"})
     * @Route("/todo", name="alumno_listar_todo",methods={"GET"})
     */
    public function listarTodoAction(Request $request)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $alumnos = $em->getRepository('AppBundle:Alumno')
            ->createQueryBuilder('a');

        if ($request->get('_route') == 'alumno_tutoria') {
            $alumnos = $alumnos
                ->join('AppBundle:Grupo', 'g', 'WITH', 'a.grupo = g')
                ->where('g.tutor = :usuario')
                ->setParameter('usuario', $usuario)
                ->addOrderBy('g.descripcion');
        }
        $alumnos = $alumnos
            ->addOrderBy('a.apellido1')
            ->addOrderBy('a.apellido2')
            ->addOrderBy('a.nombre')
            ->getQuery()
            ->getResult();

        return $this->render(($request->get('_route') == 'alumno_tutoria')
                ? 'AppBundle:Grupo:tutoria.html.twig'
                : 'AppBundle:Grupo:listar.html.twig',
            [
                'alumnos' => $alumnos,
                'usuario' => $usuario
            ]);
    }

    /**
     * @Route("/detalle/tutoria/{alumno}", name="alumno_tutoria_detalle",methods={"GET", "POST"})
     * @Route("/detalle/{alumno}", name="alumno_detalle",methods={"GET", "POST"})
     */
    public function detalleAction(Alumno $alumno, Request $request)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $formularioAlumno = $this->createForm(new AlumnoType(), $alumno, [
            'admin' => $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'),
            'bloqueado' => ($alumno->getGrupo()->getTutor() != $usuario)
        ]);

        $formularioAlumno->handleRequest($request);

        $vuelta = ($request->get('_route') == 'alumno_tutoria_detalle')
            ? [
                'ruta' => 'alumno_tutoria',
                'descripcion' => 'Gestionar tutoría',
                'boton' => 'Volver al listado del grupo'
            ]
            : [
                'ruta' => 'alumno_listar_todo',
                'descripcion' => 'Gestionar alumnado',
                'boton' => 'Volver al listado de alumnado'
            ];

        if ($formularioAlumno->isSubmitted() && $formularioAlumno->isValid()) {
            $em->persist($alumno);
            $em->flush();
            $this->addFlash('success', 'Los cambios han sido registrados correctamente');

            // redireccionar al listado
            return new RedirectResponse(
                $this->generateUrl($vuelta['ruta'])
            );
        }

        return $this->render('AppBundle:Alumno:detalle.html.twig',
            [
                'vuelta' => $vuelta,
                'alumno' => $alumno,
                'formulario_alumno' => $formularioAlumno->createView(),
                'usuario' => $usuario
            ]);
    }
}