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
use AppBundle\Entity\AvisoSancion;
use AppBundle\Entity\ObservacionSancion;
use AppBundle\Entity\Sancion;
use AppBundle\Form\Type\NuevaObservacionType;
use AppBundle\Form\Type\NuevaSancionType;
use AppBundle\Form\Type\SancionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/sancion")
 */

class SancionController extends Controller
{
    /**
     * @Route("/nueva/{alumno}", name="parte_sancionar",methods={"GET", "POST"})
     * @Security("has_role('ROLE_REVISOR')")
     */
    public function sancionarAction(Alumno $alumno, Request $peticion)
    {
        $sancion = new Sancion();
        $usuario = $this->getUser();

        $sancion->setFechaSancion(new \DateTime())
            ->setUsuario($usuario)
            ->setRegistradoEnSeneca(false);

        $em = $this->getDoctrine()->getManager();

        $partes = $em->getRepository('AppBundle:Parte')->findAllSancionablesPorAlumno($alumno);

        $formulario = $this->createForm(new NuevaSancionType(), $sancion, [
            'alumno' => $alumno
        ]);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            $em = $this->getDoctrine()->getManager();

            foreach($sancion->getPartes() as $parte) {
                $parte->setSancion($sancion);
            }
            $em->persist($sancion);
            $em->flush();

            $this->addFlash('success', 'Se ha registrado la sanción');

            // redireccionar a la portada
            return new RedirectResponse(
                $this->generateUrl('parte_pendiente')
            );
        }

        return $this->render('AppBundle:Parte:sancionar.html.twig',
            [
                'alumno' => $alumno,
                'partes' => $partes,
                'sanciones' => $em->getRepository('AppBundle:Sancion')->findAllPorAlumno($alumno),
                'formulario' => $formulario->createView(),
                'usuario' => $usuario
            ]);
    }

    /**
     * @Route("/notificar", name="sancion_listado_notificar",methods={"GET", "POST"})
     * @Security("has_role('ROLE_REVISOR') or has_role('ROLE_TUTOR') ")
     */
    public function listadoNotificarAction(Request $request)
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        if (($request->getMethod() == 'POST') && (($request->request->get('noNotificada')) || ($request->request->get('notificada')))) {

            $id = $request->request->get(($request->request->get('notificada')) ? 'notificada' : 'noNotificada');

            $sanciones = $em->getRepository('AppBundle:Sancion')
                ->findAllNoNotificadosPorAlumno($id);

            foreach ($sanciones as $sancion) {
                $avisoSancion = new AvisoSancion();
                $avisoSancion
                    ->setSancion($sancion)
                    ->setUsuario($usuario)
                    ->setAnotacion($request->request->get('anotacion'))
                    ->setFecha(new \DateTime())
                    ->setTipo($em->getRepository('AppBundle:CategoriaAviso')->find($request->request->get('tipo')));

                if ($request->request->get('notificada')) {
                    $sancion->setFechaComunicado(new \DateTime());
                }

                $em->persist($avisoSancion);
            }
            $em->flush();
        }

        $alumnos = ($this->isGranted('ROLE_REVISOR'))
            ? $em->getRepository('AppBundle:Alumno')->findAllConSancionesAunNoNotificadas()
            : $em->getRepository('AppBundle:Alumno')->findAllConSancionesAunNoNotificadasPorTutoria($usuario);

        if (count($alumnos) == 0) {
            // redireccionar a la portada
            return new RedirectResponse(
                $this->generateUrl('portada')
            );
        }
        return $this->render('AppBundle:Sancion:notificar.html.twig',
            [
                'usuario' => $usuario,
                'alumnos' => $alumnos,
                'tipos' => $em->getRepository('AppBundle:CategoriaAviso')->findAll()
            ]);
    }


    /**
     * @Route("/listar", name="sancion_listar",methods={"GET"})
     * @Security("has_role('ROLE_REVISOR')")
     */
    public function listarAction()
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $sanciones = $em->getRepository('AppBundle:Sancion')
            ->createQueryBuilder('s')
            ->orderBy('s.fechaSancion', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('AppBundle:Sancion:listar.html.twig',
            [
                'sanciones' => $sanciones,
                'usuario' => $usuario
            ]);
    }

    /**
     * @Route("/detalle/{sancion}", name="sancion_detalle",methods={"GET", "POST"})
     * @Security("has_role('ROLE_REVISOR')")
     */
    public function detalleAction(Sancion $sancion, Request $request)
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $formularioSancion = $this->createForm(new SancionType(), $sancion, [
            'admin' => true,
            'bloqueado' => false
        ]);

        $formularioSancion->get('sinSancion')->setData($sancion->getMotivosNoAplicacion() !== null);
        
        $observacion = new ObservacionSancion();
        $observacion
            ->setSancion($sancion)
            ->setFecha(new \DateTime())
            ->setUsuario($usuario);

        $formularioObservacion = $this->createForm(new NuevaObservacionType(), $observacion, [
            'admin' => $this->isGranted('ROLE_ADMIN')
        ]);

        $formularioObservacion->handleRequest($request);

        if ($formularioObservacion->isSubmitted() && $formularioObservacion->isValid()) {
            $em->persist($observacion);
            $em->flush();
            $this->addFlash('success', 'Observación registrada correctamente');
        }

        $formularioSancion->handleRequest($request);

        if ($formularioSancion->isSubmitted() && $formularioSancion->isValid()) {

            $em->flush();

            $this->addFlash('success', 'Se han registrado correctamente los cambios en la sanción');

            return new RedirectResponse(
                $this->generateUrl('sancion_listar')
            );
        }

        return $this->render('AppBundle:Sancion:detalle.html.twig',
            [
                'sancion' => $sancion,
                'formulario_sancion' => $formularioSancion->createView(),
                'formulario_observacion' => $formularioObservacion->createView(),
                'usuario' => $usuario
            ]);
    }


    /**
     * @Route("/imprimir/{sancion}", name="sancion_detalle_pdf",methods={"GET"})
     * @Security("has_role('ROLE_REVISOR')")
     */
    public function detallePdfAction(Sancion $sancion)
    {
        $usuario = $this->getUser();
        $plantilla = $this->container->getParameter('sancion');
        $logos = $this->container->getParameter('logos');

        $pdf = DefaultController::generarPdf($this, 'Sancion #' . $sancion->getId(), $logos, $plantilla, -15, 'S' . $sancion->getId());

        $html = $this->renderView('AppBundle:Sancion:imprimir.html.twig',
            [
                'sancion' => $sancion,
                'usuario' => $usuario,
                'localidad' => $this->container->getParameter('localidad'),
                'director' => $this->container->getParameter('director')
            ]);

        $pdf->writeHTML($html);

        $response = new Response($pdf->Output('sancion_' . $sancion->getId() . '.pdf'));
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
