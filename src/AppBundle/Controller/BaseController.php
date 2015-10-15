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
use TCPDF;

abstract class BaseController extends Controller
{

    /**
     * Genera un objeto documento PDF
     *
     * @param $titulo
     * @param $logos
     * @param $plantilla
     * @param $margen
     * @param $codigo
     * @return TCPDF
     */
    protected function generarPdf($titulo, $logos, $plantilla, $margen = 0, $codigo = null)
    {
        $pdf = $this->get('white_october.tcpdf')->create();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Gestconv');
        $pdf->SetTitle($titulo);
        $pdf->SetKeywords('');
        $pdf->SetExtendedHeaderData(
            array(
                $logos['centro'],
                $logos['organizacion'],
                $logos['sello']
            ),
            array(
                $this->container->getParameter('centro') . ' - ' . $this->container->getParameter('localidad'),
                $plantilla['proceso'],
                $plantilla['descripcion'],
                $plantilla['modelo'],
                $plantilla['revision']
            )
        );
        if ($codigo) {
            $pdf->setBarcode($codigo);
        }
        $pdf->setFooterData(array(0, 0, 128), array(0, 64, 128));
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER + $plantilla['margen']);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // mostrar cabecera
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM - $margen);

        $pdf->SetFont('helvetica', '', 10, '', true);

        $pdf->AddPage();

        return $pdf;
    }

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

                    $pdf = $this->generarPdf('Parte #' . $parte->getId(), $logos, $plantilla, 0, 'P' . $parte->getId());

                    $html = $this->renderView('AppBundle:Parte:imprimir.html.twig',
                        array(
                            'parte' => $parte,
                            'usuario' => $usuario,
                            'localidad' => $this->container->getParameter('localidad')
                        ));

                    $pdf->writeHTML($html);

                    $adjunto = Swift_Attachment::newInstance($pdf->getPDFData(), 'P' . $parte->getId() . '.pdf', 'application/pdf')->setDisposition('inline');
                }

                $mensaje = $mailer->createMessage()
                    ->setSubject(
                        $this->container->getParameter('prefijo_notificacion') . ' Nuevo parte notificado de ' . $parte->getAlumno() .
                        ($parte->getPrioritario() ? " (PRIORITARIO)" : "")
                    )
                    ->setFrom($this->container->getParameter('remite_notificacion'))
                    ->setTo(array($usuario->getEmail() => $usuario->__toString()))
                    ->setBody('La familia del estudiante ' . $parte->getAlumno() . ' ha sido notificada del parte que se incluye adjunto en el mensaje.')
                    ->attach($adjunto);

                $enviados++;

                $mailer->send($mensaje);
            }
        }

        return $enviados;
    }
}
