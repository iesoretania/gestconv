<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AvisoParte;
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
     * @Route("/parte/notificar", name="parte_listado_notificar",methods={"GET", "POST"})
     */
    public function listadoNotificarAction(Request $request)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == 'POST') {
            if (($request->request->get('noNotificada')) || ($request->request->get('notificada'))) {

                $id = $request->request->get(($request->request->get('notificada')) ? 'notificada' : 'noNotificada');

                $partes = $em->getRepository('AppBundle:Parte')
                    ->createQueryBuilder('p')
                    ->innerJoin('p.alumno', 'a')
                    ->where('p.fechaAviso IS NULL')
                    ->andWhere('p.usuario = :usuario')
                    ->andWhere('p.alumno = :alumno')
                    ->setParameter('usuario', $usuario)
                    ->setParameter('alumno', $id)
                    ->getQuery()
                    ->getResult();

                foreach($partes as $parte) {
                    $avisoParte = new AvisoParte();
                    $avisoParte->setUsuario($usuario)
                        ->setAnotacion($request->request->get('anotacion'))
                        ->setFecha(new \DateTime())
                        ->setTipo($em->getRepository('AppBundle:CategoriaAviso')->find($request->request->get('tipo')))
                        ->setParte($parte);
                    $em->persist($avisoParte);

                    if ($request->request->get('notificada')) {
                        $parte->setFechaAviso(new \DateTime());
                    }
                }
                $em->flush();
            }
        }
        $alumnos = $em->getRepository('AppBundle:Alumno')
            ->createQueryBuilder('a')
            ->innerJoin('AppBundle:Parte', 'p')
            ->where('p.fechaAviso IS NULL')
            ->andWhere('p.usuario = :usuario')
            ->andWhere('p.alumno = a')
            ->setParameter('usuario', $usuario)
            ->orderBy('a.apellido1', 'ASC')
            ->addOrderBy('a.apellido2', 'ASC')
            ->addOrderBy('a.nombre', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('AppBundle:Parte:notificar.html.twig',
            [
                'usuario' => $usuario,
                'alumnos' => $alumnos,
                'tipos' => $em->getRepository('AppBundle:CategoriaAviso')->findAll()
            ]);
    }
}
