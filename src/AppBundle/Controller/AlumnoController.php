<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Alumno;
use AppBundle\Form\Type\AlumnoType;
use AppBundle\Form\Type\RangoFechasType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/alumno")
 */

class AlumnoController extends Controller
{
    /**
     * @Route("/tutoria", name="alumno_tutoria",methods={"GET", "POST"})
     * @Route("/todo", name="alumno_listar_todo",methods={"GET", "POST"})
     */
    public function listarAction(Request $request)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $fechasPorDefecto = ['desde' => null, 'hasta' => null];

        $form = $this->createForm(new RangoFechasType(), $fechasPorDefecto);
        $form->handleRequest($request);

        $alumnos = $em->getRepository('AppBundle:Alumno')
            ->createQueryBuilder('a')
            ->leftJoin('AppBundle:Parte', 'p', 'WITH', 'p.alumno = a')
            ->leftJoin('AppBundle:Sancion', 's', 'WITH', 'p.sancion = s')
            ->select('a')
            ->addSelect('count(p)')
            ->addSelect('count(p.fechaAviso)')
            ->addSelect('count(p.sancion)')
            ->addSelect('count(s.fechaComunicado)')
            ->addSelect('count(s.motivosNoAplicacion)')
            ->addSelect('count(s.fechaInicioSancion)')
            ->addSelect('max(p.fechaSuceso)')
            ->addSelect('max(s.fechaSancion)')
            ->addSelect('sum(p.prescrito)');

        if ($form->isValid()) {
            // aplicar filtro de fechas
            $data = $form->getData();
            if ($data['desde']) {
                $alumnos = $alumnos
                    ->andWhere('p.fechaSuceso >= :desde')
                    ->setParameter('desde', $data['desde']);
            }
            if ($data['hasta']) {
                $alumnos = $alumnos
                    ->andWhere('p.fechaSuceso <= :hasta')
                    ->setParameter('hasta', $data['hasta']);
            }
        }

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
            ->groupBy('a.id')
            ->getQuery()
            ->getResult();

        return $this->render(($request->get('_route') == 'alumno_tutoria')
                ? 'AppBundle:Alumno:tutoria.html.twig'
                : 'AppBundle:Alumno:listar.html.twig',
            [
                'formulario_fechas' => $form->createView(),
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
