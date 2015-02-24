<?php
/*
  GESTCONV - Aplicación web para la gestión de la convivencia en centros educativos

  Copyright (C) 2015: Luis Ramón López López

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see [http://www.gnu.org/licenses/].
*/

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
            ->countNoNotificados();

        $partesPendientesPropios = $em->getRepository('AppBundle:Parte')
            ->countNoNotificadosPorUsuario($usuario);

        $partesPendientesPropiosYTutoria = $em->getRepository('AppBundle:Parte')
            ->countNoNotificadosPorUsuarioOTutoria($usuario);

        $partesTotales = $em->getRepository('AppBundle:Parte')
            ->countPorUsuario($usuario);

        if (true === $this->get('security.authorization_checker')->isGranted('ROLE_REVISOR')) {
            $partesSancionables = $em->getRepository('AppBundle:Parte')
                ->countSancionables();

            $sancionesNotificables = $em->getRepository('AppBundle:Sancion')
                ->countNoNotificados();

            $sancionesTotales = $em->getRepository('AppBundle:Sancion')
                ->countAll();
        }
        else {
            $partesSancionables = 0;
            $sancionesNotificables = $em->getRepository('AppBundle:Sancion')
                ->countNoNotificadosPorTutoria($usuario);
            $sancionesTotales = 0;
        }

        return $this->render('AppBundle:App:portada.html.twig', [
            'cuenta' => [
                'partes_pendientes' => $partesPendientes,
                'partes_pendientes_propios_y_tutoria' => $partesPendientesPropiosYTutoria,
                'partes_totales' => $partesTotales,
                'partes_pendientes_propios' => $partesPendientesPropios,
                'partes_sancionables' => $partesSancionables
            ],
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
