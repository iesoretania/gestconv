<?php
/*
  GESTCONV - Aplicación web para la gestión de la convivencia en centros educativos

  Copyright (C) 2015: Luis Ramón López López

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see [http://www.gnu.org/licenses/].
*/

namespace AppBundle\Controller;

use AppBundle\Entity\Alumno;
use AppBundle\Form\Type\AlumnoType;
use AppBundle\Form\Type\RangoFechasType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/alumno")
 */

class AlumnoController extends Controller
{
    /**
     * @Route("/tutoria", name="alumno_tutoria",methods={"GET", "POST"})
     * @Route("/todo", name="alumno_listar_todo",methods={"GET", "POST"})
     * @Security("has_role('ROLE_DIRECTIVO') or (has_role('ROLE_TUTOR') and request.get('_route') == 'alumno_tutoria')")
     */
    public function listarAction(Request $request)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $fechasPorDefecto = ['desde' => null, 'hasta' => null];

        $form = $this->createForm(new RangoFechasType(), $fechasPorDefecto);
        $form->handleRequest($request);

        $tutor = ($request->get('_route') == 'alumno_tutoria') ? $usuario : null;

        $items = $em->getRepository('AppBundle:Alumno')->findResumenTutorPartesSancionesYExpulsionesEnFecha($tutor, $form->isValid() ? $form->getData() : $fechasPorDefecto);

        return $this->render(($request->get('_route') == 'alumno_tutoria')
                ? 'AppBundle:Alumno:tutoria.html.twig'
                : 'AppBundle:Alumno:listar.html.twig',
            [
                'formulario_fechas' => $form->createView(),
                'items' => $items,
                'usuario' => $usuario
            ]);
    }

    /**
     * @Route("/detalle/tutoria/{alumno}", name="alumno_tutoria_detalle", methods={"GET", "POST"})
     * @Route("/detalle/{alumno}", name="alumno_detalle", methods={"GET", "POST"})
     * @Security("has_role('ROLE_DIRECTIVO') or (has_role('ROLE_TUTOR') and request.get('_route') == 'alumno_tutoria_detalle')")
     */
    public function detalleAction(Alumno $alumno, Request $request)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $formularioAlumno = $this->createForm(new AlumnoType(), $alumno, [
            'admin' => $this->get('security.authorization_checker')->isGranted('ROLE_DIRECTIVO'),
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
