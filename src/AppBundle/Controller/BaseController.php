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

use AppBundle\Entity\Parte;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseController extends Controller
{
    protected function notificarParte($usuarios, Parte $parte)
    {
        $enviados = 0;
        $mailer = $this->get('mailer');
        $adjunto = null;

        foreach($usuarios as $usuario) {
            if ($usuario->getNotificaciones() && $usuario->getEmail()) {

                if (is_null($adjunto)) {
                    $plantilla = $this->container->getParameter('parte');
                    $logos = $this->container->getParameter('logos');

                    $pdf = DefaultController::generarPdf($this, 'Parte #' . $parte->getId(), $logos, $plantilla, 0, 'P' . $parte->getId());

                    $html = $this->renderView('AppBundle:Parte:imprimir.html.twig',
                        [
                            'parte' => $parte,
                            'usuario' => $usuario,
                            'localidad' => $this->container->getParameter('localidad')
                        ]);

                    $pdf->writeHTML($html);

                    $adjunto = Swift_Attachment::newInstance($pdf->getPDFData(), 'P' . $parte->getId() . '.pdf', 'application/pdf')->setDisposition('inline');
                }

                $mensaje = $mailer->createMessage()
                    ->setSubject($this->container->getParameter('prefijo_notificacion') . ' Nuevo parte notificado de ' . $parte->getAlumno())
                    ->setFrom($this->container->getParameter('remite_notificacion'))
                    ->setTo([$usuario->getEmail() => $usuario->__toString()])
                    ->setBody('La familia del estudiante ' . $parte->getAlumno() . ' ha sido notificada del parte que se incluye adjunto en el mensaje.')
                    ->attach($adjunto);

                $enviados++;

                $mailer->send($mensaje);
            }
        }

        return $enviados;
    }
}