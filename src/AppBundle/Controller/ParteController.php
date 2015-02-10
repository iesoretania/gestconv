<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AvisoParte;
use AppBundle\Entity\Parte;
use AppBundle\Form\Type\NuevoParteType;
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
            'admin' => $usuario->getEsAdministrador()
        ]);

        $formulario->handleRequest($peticion);
        if (!$usuario->getEsAdministrador()) {
            $parte->setUsuario($usuario);
        }

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

        return $this->render('AppBundle:Parte:notificar.html.twig',
            [
                'usuario' => $usuario,
                'alumnos' => $em->getRepository('AppBundle:Alumno')->findAllConPartesAunNoNotificadosPorUsuario($usuario),
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
}
