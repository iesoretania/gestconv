<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Alumno;
use AppBundle\Entity\AvisoSancion;
use AppBundle\Entity\Sancion;
use AppBundle\Form\Type\NuevaSancionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
        $usuario = $this->get('security.token_storage')->getToken()->getUser();

        $sancion->setFechaSancion(new \DateTime())
            ->setUsuario($usuario);

        $em = $this->getDoctrine()->getManager();

        $partes = $em
            ->createQueryBuilder()
            ->select('p')
            ->from('AppBundle\Entity\Parte', 'p', 'p.id')
            ->andWhere('p.fechaAviso IS NOT NULL')
            ->andWhere('p.sancion IS NULL')
            ->andWhere('p.prescrito = false')
            ->andWhere('p.alumno = :alumno')
            ->orderBy('p.fechaSuceso')
            ->setParameter('alumno', $alumno)
            ->getQuery()
            ->getResult();

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
                'formulario' => $formulario->createView(),
                'usuario' => $usuario
            ]);
    }

    /**
     * @Route("/notificar", name="sancion_listado_notificar",methods={"GET", "POST"})
     */
    public function listadoNotificarAction(Request $request)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        if (($request->getMethod() == 'POST') && (($request->request->get('noNotificada')) || ($request->request->get('notificada')))) {

            $id = $request->request->get(($request->request->get('notificada')) ? 'notificada' : 'noNotificada');

            $sanciones = $em->getRepository('AppBundle:Sancion')
                ->findAllNoNotificadosPorAlumno($id);

            foreach ($sanciones as $sancion) {
                $avisoSancion = new AvisoSancion();
                $avisoSancion->setUsuario($usuario)
                    ->setAnotacion($request->request->get('anotacion'))
                    ->setFecha(new \DateTime())
                    ->setTipo($em->getRepository('AppBundle:CategoriaAviso')->find($request->request->get('tipo')))
                    ->setSancion($sancion);

                if ($request->request->get('notificada')) {
                    $sancion->setFechaComunicado(new \DateTime());
                }

                $em->persist($avisoSancion);
            }
            $em->flush();
        }
        $alumnos = $em->getRepository('AppBundle:Alumno')
            ->findAllConSancionesAunNoNotificadas();

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

}
