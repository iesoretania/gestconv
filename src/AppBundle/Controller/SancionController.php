<?php

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
     * @Security("has_role('ROLE_REVISOR') or has_role('ROLE_TUTOR') ")
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

        $alumnos = ($this->get('security.authorization_checker')->isGranted('ROLE_REVISOR'))
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
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
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
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
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
            'admin' => $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')
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

            // redireccionar a la portada
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
        $usuario = $this->get('security.token_storage')->getToken()->getUser();

        $pdf = $this->get('white_october.tcpdf')->create();

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Gestconv');
        $pdf->SetTitle('Sancion #' . $sancion->getId());
        $pdf->SetSubject($sancion->getPartes()->first()->getAlumno());
        $pdf->SetKeywords('');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->SetFont('helvetica', '', 10, '', true);

        $pdf->AddPage();

        $html = $this->renderView('AppBundle:Sancion:imprimir.html.twig',
            [
                'sancion' => $sancion,
                'usuario' => $usuario
            ]);

        $pdf -> writeHTML($html);
        $response = new Response($pdf->Output('sancion_' . $sancion->getId() . '.pdf'));
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
