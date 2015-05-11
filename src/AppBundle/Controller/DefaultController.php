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

class DefaultController extends Controller
{
    /**
     * @Route("/portada", name="portada",methods={"GET"})
     */
    public function indexAction()
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();


        $partesPendientes = $em->getRepository('AppBundle:Parte')
            ->countNoNotificados();

        $partesPendientesPropios = $em->getRepository('AppBundle:Parte')
            ->countNoNotificadosPorUsuario($usuario);

        $partesPendientesPropiosYTutoria = $em->getRepository('AppBundle:Parte')
            ->countNoNotificadosPorUsuarioOTutoria($usuario);

        if (true === $this->isGranted('ROLE_REVISOR')) {

            $partesTotales = $em->getRepository('AppBundle:Parte')->countAll();

            $partesSancionables = $em->getRepository('AppBundle:Parte')
                ->countSancionables();

            $sancionesNotificables = $em->getRepository('AppBundle:Sancion')
                ->countNoNotificados();

            $sancionesTotales = $em->getRepository('AppBundle:Sancion')
                ->countAll();
        }
        else {
            $partesTotales = $em->getRepository('AppBundle:Parte')
                ->countPorUsuario($usuario);

            $partesSancionables = 0;
            $sancionesNotificables = $em->getRepository('AppBundle:Sancion')
                ->countNoNotificadosPorTutoria($usuario);
            $sancionesTotales = 0;
        }

        return $this->render('AppBundle:App:portada.html.twig', array(
            'cuenta' => array(
                'partes_pendientes' => $partesPendientes,
                'partes_pendientes_propios_y_tutoria' => $partesPendientesPropiosYTutoria,
                'partes_totales' => $partesTotales,
                'partes_pendientes_propios' => $partesPendientesPropios,
                'partes_sancionables' => $partesSancionables
            ),
            'sanciones_notificables' => $sancionesNotificables,
            'sanciones_totales' => $sancionesTotales
        ));
    }

    /**
     * @Route("/entrar", name="usuario_entrar",methods={"GET"})
     */
    public function entrarAction()
    {
        $helper = $this->get('security.authentication_utils');

        return $this->render('AppBundle:App:entrada.html.twig',
            array(
                'last_username' => $helper->getLastUsername(),
                'error'         => $helper->getLastAuthenticationError()
            ));
    }

}
