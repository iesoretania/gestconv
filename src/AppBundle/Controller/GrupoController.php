<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\RangoFechasType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/grupo")
 */

class GrupoController extends Controller
{
    /**
     * @Route("/listar", name="grupo_listar",methods={"GET", "POST"})
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
            ->addSelect('max(p.fechaSuceso)')
            ->addSelect('max(s.fechaSancion)')
            ->addSelect('sum(p.prescrito)');

        if ($form->isValid()) {
            // aplicar filtro de fechas
            $data = $form->getData();
            if ($data['desde']) {
                $grupos = $grupos
                    ->andWhere('p.fechaSuceso >= :desde')
                    ->setParameter('desde', $data['desde']);
            }
            if ($data['hasta']) {
                $grupos = $grupos
                    ->andWhere('p.fechaSuceso <= :hasta')
                    ->setParameter('hasta', $data['hasta']);
            }
        }

        $grupos = $grupos
            ->addOrderBy('g.descripcion')
            ->groupBy('g.id')
            ->getQuery()
            ->getResult();

        return $this->render('AppBundle:Grupo:listar.html.twig',
            [
                'formulario_fechas' => $form->createView(),
                'items' => $grupos,
                'usuario' => $usuario
            ]);
    }
}
