<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Parte;
use AppBundle\Form\Type\NuevoParteType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ParteController extends Controller
{
    /**
     * @Route("/parte/nuevo", name="parte_nuevo",methods={"GET", "POST"})
     */
    public function nuevoAction(Request $peticion)
    {
        $parte = new Parte();
        $usuario = $this->get('security.token_storage')->getToken()->getUser();

        $parte->setFechaCreacion(new \DateTime());
        $parte->setFechaSuceso(new \DateTime());
        $parte->setUsuario($usuario);
        $parte->setPrescrito(false);

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
            foreach($alumnos as $alumno) {
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
     * @Route("/parte/notificar", name="parte_listado_notificar",methods={"GET"})
     */
    public function listadoNotificarAction()
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $partes = $em->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->where('p.fechaAviso IS NULL')
            ->andWhere('p.usuario = :usuario')
            ->setParameter('usuario', $usuario)
            ->orderBy('p.fechaSuceso', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('AppBundle:Parte:listado_notificar.html.twig',
            [
                'usuario' => $usuario,
                'partes' => $partes
            ]);
    }

    /**
     * @Route("/parte/notificar/{id}", name="parte_notificar",methods={"GET", "POST"})
     */
    public function notificarAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $parte = $em->getRepository('AppBundle:Parte')->find($id);

        return $this->render('AppBundle:Parte:notificar.html.twig',
            [
                'parte' => $parte,
                'pendientes' => []
            ]);
    }
}
