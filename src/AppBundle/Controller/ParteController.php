<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AvisoParte;
use AppBundle\Entity\ObservacionParte;
use AppBundle\Entity\Parte;
use AppBundle\Form\Type\NuevaObservacionType;
use AppBundle\Form\Type\NuevoParteType;
use AppBundle\Form\Type\ParteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/parte")
 */

class ParteController extends Controller
{
    /**
     * @Route("/nuevo", name="parte_nuevo",methods={"GET", "POST"})
     */
    public function nuevoAction(Request $peticion)
    {
        $parte = new Parte();
        $usuario = $this->get('security.token_storage')->getToken()->getUser();

        $parte->setFechaCreacion(new \DateTime())
            ->setFechaSuceso(new \DateTime())
            ->setUsuario($usuario)
            ->setPrescrito(false);

        $formulario = $this->createForm(new NuevoParteType(), $parte, [
            'admin' => $this->get('security.authorization_checker')->isGranted('ROLE_REVISOR')
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
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        if (($request->getMethod() == 'POST') && (($request->request->get('noNotificada')) || ($request->request->get('notificada')))) {

            $id = $request->request->get(($request->request->get('notificada')) ? 'notificada' : 'noNotificada');

            $partes = $em->getRepository('AppBundle:Parte')
                ->findAllNoNotificadosPorAlumnoYUsuario($id, $usuario);

            foreach ($partes as $parte) {
                $avisoParte = new AvisoParte();
                $avisoParte->setUsuario($usuario)
                    ->setAnotacion($request->request->get('anotacion'))
                    ->setFecha(new \DateTime())
                    ->setTipo($em->getRepository('AppBundle:CategoriaAviso')->find($request->request->get('tipo')))
                    ->setParte($parte);

                if ($request->request->get('notificada')) {
                    $parte->setFechaAviso(new \DateTime());
                }

                $em->persist($avisoParte);
            }
            $em->flush();
        }
        $alumnos = $em->getRepository('AppBundle:Alumno')
            ->findAllConPartesAunNoNotificadosPorUsuario($usuario);

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
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
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
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $partes = $em->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->andWhere('p.usuario = :usuario')
            ->setParameter('usuario', $usuario)
            ->getQuery()
            ->getResult();

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
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $esRevisor = $this->get('security.authorization_checker')->isGranted('ROLE_REVISOR');
        $esTutor = ($parte->getAlumno()->getGrupo()->getTutor() == $usuario);

        if (!$esRevisor && !$esTutor && $parte->getUsuario() != $usuario) {
            throw $this->createAccessDeniedException('No puede acceder al parte indicado');
        }

        $formularioParte = $this->createForm(new ParteType(), $parte, [
            'admin' => $esRevisor,
            'bloqueado' => (false === is_null($parte->getSancion()))
        ]);

        $observacion = new ObservacionParte();
        $observacion->setFecha(new \DateTime())
            ->setParte($parte)
            ->setUsuario($usuario)
            ->setAutomatica(false);

        $formularioObservacion = $this->createForm(new NuevaObservacionType(), $observacion, [
            'admin' => $esRevisor
        ]);

        $formularioObservacion->handleRequest($request);

        if ($formularioObservacion->isSubmitted() && $formularioObservacion->isValid()) {
            $em->persist($observacion);
            $em->flush();
            $this->addFlash('success', 'Observación registrada correctamente');
        }

        $formularioParte->handleRequest($request);

        if ($formularioParte->isSubmitted() && $formularioParte->isValid()) {

            $em->flush();

            $this->addFlash('success', 'Se han registrado correctamente los cambios en el parte');

            // redireccionar a la portada
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
}
