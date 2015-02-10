<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Alumno;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/sancion")
 */

class SancionController extends Controller
{
    /**
     * @Route("/nueva/{alumno}", name="parte_sancionar",methods={"GET", "POST"})
     * @Security("has_role('ROLE_REVISOR')")
     */
    public function sancionarAction(Alumno $alumno)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $partes = $em->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->where('p.alumno  = :alumno')
            ->andWhere('p.fechaAviso IS NOT NULL')
            ->andWhere('p.sancion IS NULL')
            ->orderBy('p.fechaSuceso')
            ->setParameter('alumno', $alumno)
            ->getQuery()
            ->getResult();

        return $this->render('AppBundle:Parte:sancionar.html.twig',
            [
                'alumno' => $alumno,
                'partes' => $partes,
                'usuario' => $usuario
            ]);
    }
}
