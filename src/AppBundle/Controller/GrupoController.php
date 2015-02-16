<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/grupo")
 */

class GrupoController extends Controller
{
    /**
     * @Route("/listar", name="grupo_listar",methods={"GET"})
     */
    public function listarAction()
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $alumnos = $em->getRepository('AppBundle:Alumno')
            ->createQueryBuilder('a')
            ->join('AppBundle:Grupo', 'g', 'WITH', 'a.grupo = g')
            ->where('g.tutor = :usuario')
            ->setParameter('usuario', $usuario)
            ->orderBy('g.descripcion')
            ->addOrderBy('a.apellido1')
            ->addOrderBy('a.apellido2')
            ->addOrderBy('a.nombre')
            ->getQuery()
            ->getResult();

        return $this->render('AppBundle:Grupo:listar.html.twig',
            [
                'alumnos' => $alumnos,
                'usuario' => $usuario
            ]);
    }
}
