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

use AppBundle\Entity\AvisoParte;
use AppBundle\Entity\ObservacionParte;
use AppBundle\Entity\Parte;
use AppBundle\Form\Type\NuevaObservacionType;
use AppBundle\Form\Type\NuevoParteType;
use AppBundle\Form\Type\ParteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/parte")
 */

class ParteController extends BaseController
{
    /**
     * @Route("/nuevo", name="parte_nuevo",methods={"GET", "POST"})
     */
    public function nuevoAction(Request $peticion)
    {
        $parte = new Parte();
        $usuario = $this->getUser();

        $parte->setFechaCreacion(new \DateTime())
            ->setFechaSuceso(new \DateTime())
            ->setUsuario($usuario)
            ->setPrescrito(false);

        $formulario = $this->createForm(new NuevoParteType(), $parte, [
            'admin' => $this->isGranted('ROLE_REVISOR')
        ]);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // crear un parte por alumno y guardarlo en la base de datos
            $em = $this->getDoctrine()->getManager();
            $alumnos = $formulario->get('alumnos')->getData();
            foreach ($alumnos as $alumno) {
                $nuevoParte = clone $parte;
                $nuevoParte->setAlumno($alumno);
                $em->persist($nuevoParte);
            }

            $em->flush();

            $this->addFlash('success', (count($alumnos) == 1)
                ? 'Se ha creado un parte con éxito'
                : 'Se han creado ' . count($alumnos) . ' partes con éxito');

            // redireccionar a la portada
            return new RedirectResponse(
                $this->generateUrl('portada')
            );
        }

        return $this->render('AppBundle:Parte:nuevo.html.twig',
            [
                'parte' => $parte,
                'formulario' => $formulario->createView()
            ]);
    }

    /**
     * @Route("/notificar", name="parte_listado_notificar",methods={"GET", "POST"})
     */
    public function listadoNotificarAction(Request $request)
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        if (($request->getMethod() === 'POST') && (($request->request->get('noNotificada')) || ($request->request->get('notificada')))) {

            $id = $request->request->get(($request->request->get('notificada')) ? 'notificada' : 'noNotificada');

            $partes = ($this->isGranted('ROLE_REVISOR'))
            ? $em->getRepository('AppBundle:Parte')->findAllNoNotificadosPorAlumno($id)
            : $em->getRepository('AppBundle:Parte')->findAllNoNotificadosPorAlumnoYUsuario($id, $usuario);

            foreach ($partes as $parte) {
                $avisoParte = new AvisoParte();
                $avisoParte
                    ->setParte($parte)
                    ->setUsuario($usuario)
                    ->setAnotacion($request->request->get('anotacion'))
                    ->setFecha(new \DateTime())
                    ->setTipo($em->getRepository('AppBundle:CategoriaAviso')->find($request->request->get('tipo')));

                if ($request->request->get('notificada')) {
                    $parte->setFechaAviso(new \DateTime());
                }

                $em->persist($avisoParte);

                $this->notificarParte($parte->getAlumno()->getGrupo()->getTutores(), $parte);
            }
            $em->flush();
        }

        $alumnos = ($this->isGranted('ROLE_REVISOR'))
            ? $em->getRepository('AppBundle:Alumno')->findAllConPartesAunNoNotificados()
            : $em->getRepository('AppBundle:Alumno')->findAllConPartesAunNoNotificadosPorUsuario($usuario);

        if (count($alumnos) == 0) {
            // redireccionar a la portada
            return new RedirectResponse(
                $this->generateUrl('portada')
            );
        }
        return $this->render('AppBundle:Parte:notificar.html.twig',
            [
                'usuario' => $usuario,
                'alumnos' => $alumnos,
                'tipos' => $em->getRepository('AppBundle:CategoriaAviso')->findAll()
            ]);
    }

    /**
     * @Route("/pendiente", name="parte_pendiente",methods={"GET"})
     * @Security("has_role('ROLE_REVISOR')")
     */
    public function pendienteAction()
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        return $this->render('AppBundle:Parte:pendiente.html.twig',
            [
                'alumnos' => $em->getRepository('AppBundle:Alumno')->findAllConPartesPendientesSancion(),
                'usuario' => $usuario
            ]);
    }

    /**
     * @Route("/listar", name="parte_listar",methods={"GET"})
     */
    public function listarAction()
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $partes = $em->getRepository('AppBundle:Parte')
            ->findAllPorUsuarioOTutoria($usuario);

        return $this->render('AppBundle:Parte:listar.html.twig',
            [
                'partes' => $partes,
                'usuario' => $usuario
            ]);
    }

    /**
     * @Route("/detalle/{parte}", name="parte_detalle",methods={"GET", "POST"})
     */
    public function detalleAction(Parte $parte, Request $request)
    {
        $usuario = $this->getUser();
        
        $esRevisor = $this->isGranted('ROLE_REVISOR');

        if (!$esRevisor && !($parte->getAlumno()->getGrupo() != $usuario->getTutoria()) && $parte->getUsuario() != $usuario) {
            throw $this->createAccessDeniedException();
        }

        $formularioParte = $this->createForm(new ParteType(), $parte, [
            'admin' => $esRevisor,
            'bloqueado' => (false === is_null($parte->getSancion()))
        ]);

        $observacion = (new ObservacionParte())
            ->setParte($parte)
            ->setFecha(new \DateTime())
            ->setUsuario($usuario);

        $formularioObservacion = $this->createForm(new NuevaObservacionType(), $observacion, [
            'admin' => $esRevisor
        ]);

        $formularioObservacion->handleRequest($request);

        if ($formularioObservacion->isSubmitted() && $formularioObservacion->isValid()) {
            $this->getDoctrine()->getManager()->persist($observacion);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Observación registrada correctamente');
        }

        $formularioParte->handleRequest($request);

        if ($formularioParte->isSubmitted() && $formularioParte->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Se han registrado correctamente los cambios en el parte');

            return new RedirectResponse(
                $this->generateUrl('parte_listar')
            );
        }

        return $this->render('AppBundle:Parte:detalle.html.twig',
            [
                'parte' => $parte,
                'formulario_parte' => $formularioParte->createView(),
                'formulario_observacion' => $formularioObservacion->createView(),
                'usuario' => $usuario
            ]);
    }

    /**
     * @Route("/imprimir/{parte}", name="parte_detalle_pdf",methods={"GET"})
     */
    public function detallePdfAction(Parte $parte)
    {
        $usuario = $this->getUser();
        $plantilla = $this->container->getParameter('parte');
        $logos = $this->container->getParameter('logos');

        $esRevisor = $this->isGranted('ROLE_REVISOR');
        $esTutor = ($parte->getAlumno()->getGrupo() != $usuario->getTutoria());

        if (!$esRevisor && !$esTutor && $parte->getUsuario() != $usuario) {
            throw $this->createAccessDeniedException();
        }

        $pdf = $this->generarPdf('Parte #' . $parte->getId(), $logos, $plantilla, 0, 'P' . $parte->getId());

        $html = $this->renderView('AppBundle:Parte:imprimir.html.twig',
            [
                'parte' => $parte,
                'usuario' => $usuario,
                'localidad' => $this->container->getParameter('localidad')
            ]);

        $pdf->writeHTML($html);

        $response = new Response($pdf->Output('parte_' . $parte->getId() . '.pdf'));
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
