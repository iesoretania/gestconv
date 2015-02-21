<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Grupo;
use AppBundle\Form\Type\GrupoType;
use AppBundle\Form\Type\RangoFechasType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/grupo")
 */

class GrupoController extends Controller
{
    /**
     * @Route("/listar", name="grupo_listar",methods={"GET", "POST"})
     * @Security("has_role('ROLE_DIRECTIVO')")
     */
    public function listarAction(Request $request)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $fechasPorDefecto = ['desde' => null, 'hasta' => null];

        $form = $this->createForm(new RangoFechasType(), $fechasPorDefecto);
        $form->handleRequest($request);

        $grupos = $em->getRepository('AppBundle:Alumno')
            ->createQueryBuilder('a')
            ->leftJoin('AppBundle:Grupo', 'g', 'WITH', 'a.grupo = g')
            ->leftJoin('AppBundle:Parte', 'p', 'WITH', 'p.alumno = a')
            ->leftJoin('AppBundle:Sancion', 's', 'WITH', 'p.sancion = s')
            ->select('g')
            ->addSelect('count(p)')
            ->addSelect('count(p.fechaAviso)')
            ->addSelect('count(p.sancion)')
            ->addSelect('count(s.fechaComunicado)')
            ->addSelect('count(s.motivosNoAplicacion)')
            ->addSelect('count(s.fechaInicioSancion)')
            ->addSelect('sum(p.prescrito)')
            ->addSelect('max(p.fechaSuceso)')
            ->addSelect('max(s.fechaSancion)');

        $cursos = $em->getRepository('AppBundle:Alumno')
            ->createQueryBuilder('a')
            ->leftJoin('AppBundle:Grupo', 'g', 'WITH', 'a.grupo = g')
            ->leftJoin('AppBundle:Curso', 'c', 'WITH', 'g.curso = c')
            ->leftJoin('AppBundle:Parte', 'p', 'WITH', 'p.alumno = a')
            ->leftJoin('AppBundle:Sancion', 's', 'WITH', 'p.sancion = s')
            ->select('c')
            ->addSelect('count(p)')
            ->addSelect('count(p.fechaAviso)')
            ->addSelect('count(p.sancion)')
            ->addSelect('count(s.fechaComunicado)')
            ->addSelect('count(s.motivosNoAplicacion)')
            ->addSelect('count(s.fechaInicioSancion)')
            ->addSelect('sum(p.prescrito)')
            ->addSelect('max(p.fechaSuceso)')
            ->addSelect('max(s.fechaSancion)');

        if ($form->isValid()) {
            // aplicar filtro de fechas
            $data = $form->getData();
            if ($data['desde']) {
                $grupos = $grupos
                    ->andWhere('p.fechaSuceso >= :desde')
                    ->setParameter('desde', $data['desde']);
                $cursos = $cursos
                    ->andWhere('p.fechaSuceso >= :desde')
                    ->setParameter('desde', $data['desde']);
            }
            if ($data['hasta']) {
                $grupos = $grupos
                    ->andWhere('p.fechaSuceso <= :hasta')
                    ->setParameter('hasta', $data['hasta']);
                $cursos = $cursos
                    ->andWhere('p.fechaSuceso <= :hasta')
                    ->setParameter('hasta', $data['hasta']);
            }
        }

        $grupos = $grupos
            ->addOrderBy('g.descripcion')
            ->groupBy('g.id')
            ->getQuery()
            ->getResult();

        $cursos = $cursos
            ->addOrderBy('c.descripcion')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();

        return $this->render('AppBundle:Grupo:listar.html.twig',
            [
                'formulario_fechas' => $form->createView(),
                'items' => $grupos,
                'items2' => $cursos,
                'usuario' => $usuario
            ]);
    }

    /**
     * @Route("/modificar/{grupo}", name="grupo_modificar",methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function modificarAction(Grupo $grupo, Request $peticion)
    {
        $usuarioActivo = $this->get('security.token_storage')->getToken()->getUser();

        $formulario = $this->createForm(new GrupoType(), $grupo);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // Guardar el usuario en la base de datos
            $em = $this->getDoctrine()->getManager();

            $em->flush();

            $this->addFlash('success', 'Datos guardados correctamente');

            // redireccionar al listado de grupos
            return new RedirectResponse(
                $this->generateUrl('grupo_listar')
            );
        }

        return $this->render('AppBundle:Grupo:modificar.html.twig',
            [
                'formulario' => $formulario->createView()
            ]);
    }
}
