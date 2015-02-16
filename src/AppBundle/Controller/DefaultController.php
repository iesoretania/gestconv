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

        $partesPendientesPropios = $em->getRepository('AppBundle:Parte')
            ->countNoNotificadosPorUsuario($usuario);

        $partesPendientesTotales = $em->getRepository('AppBundle:Parte')
            ->countNoNotificadosPorUsuarioOTutoria($usuario);

        $partesTotales = $em->getRepository('AppBundle:Parte')
            ->countPorUsuario($usuario);

        if (true === $this->get('security.authorization_checker')->isGranted('ROLE_REVISOR')) {
            $partesSancionables = $em->getRepository('AppBundle:Parte')
                ->createQueryBuilder('p')
                ->select('COUNT(p.id)')
                ->andWhere('p.sancion IS NULL')
                ->andWhere('p.fechaAviso IS NOT NULL')
                ->andWhere('p.prescrito = false')
                ->getQuery()
                ->getSingleScalarResult();

            $sancionesNotificables = $em->getRepository('AppBundle:Sancion')
                ->createQueryBuilder('s')
                ->select('COUNT(s.id)')
                ->andWhere('s.fechaComunicado IS NULL')
                ->andWhere('s.motivosNoAplicacion IS NULL')
                ->getQuery()
                ->getSingleScalarResult();

            $sancionesTotales = $em->getRepository('AppBundle:Sancion')
                ->createQueryBuilder('s')
                ->select('COUNT(s.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }
        else {
            $partesSancionables = 0;
            $sancionesNotificables = 0;
            $sancionesTotales = 0;
        }

        return $this->render('AppBundle:App:portada.html.twig', [
            'partes_pendientes_totales' => $partesPendientesTotales,
            'partes_totales' => $partesTotales,
            'partes_pendientes_propios' => $partesPendientesPropios,
            'partes_sancionables' => $partesSancionables,
            'sanciones_notificables' => $sancionesNotificables,
            'sanciones_totales' => $sancionesTotales
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
