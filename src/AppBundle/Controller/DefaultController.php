<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class DefaultController extends Controller
{
    /**
     * @Route("/portada", name="portada",methods={"GET"})
     */
    public function indexAction()
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $partesPendientes = $em->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.fechaAviso IS NULL')
            ->andWhere('p.usuario = :usuario')
            ->setParameter('usuario', $usuario)
            ->getQuery()
            ->getSingleScalarResult();

        $partesTotales = $em->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.usuario = :usuario')
            ->setParameter('usuario', $usuario)
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render('AppBundle:App:portada.html.twig', [
            'partes_pendientes' => $partesPendientes,
            'partes_totales' => $partesTotales
        ]);
    }

    /**
     * @Route("/entrar", name="usuario_entrar",methods={"GET", "POST"})
     */
    public function entrarAction(Request $peticion)
    {
        $sesion = $peticion->getSession();

        if ($peticion->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $peticion->attributes->get(Security::AUTHENTICATION_ERROR);
        }
        else {
            $error = $sesion->get(Security::AUTHENTICATION_ERROR);
            $sesion->remove(Security::AUTHENTICATION_ERROR);
        }

        return $this->render('AppBundle:App:entrada.html.twig',
            [
                'last_username' => $sesion->get(Security::LAST_USERNAME),
                'error' => $error
            ]);
    }
}
