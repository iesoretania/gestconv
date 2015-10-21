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

namespace AppBundle\Command;

use AppBundle\Entity\Usuario;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('gestconv:cron')
            ->setDescription('Execute cron tasks')
        ;
    }

    /**
     * @param OutputInterface $output
     * @param Usuario[]|Collection $usuarios
     * @param string $titulo
     * @param string $cuerpo
     * @param $rol
     * @return int
     */
    private function notificar($output, $usuarios, $titulo, $cuerpo, $rol)
    {
        $enviados = 0;
        $mailer = $this->getContainer()->get('mailer');

        foreach($usuarios as $usuario) {
            $output->writeln('  - Notificando a ' . $usuario . ($rol ? ' (' . $rol . ')' : ''));
            if ($usuario->getEstaActivo() && $usuario->getNotificaciones() && $usuario->getEmail()) {

                $mensaje = $mailer->createMessage()
                    ->setSubject($this->getContainer()->getParameter('prefijo_notificacion') . ' ' . $titulo)
                    ->setFrom($this->getContainer()->getParameter('remite_notificacion'))
                    ->setTo(array($usuario->getEmail() => $usuario->__toString()))
                    ->setBody($cuerpo);

                $enviados++;

                $mailer->send($mensaje);
            }
        }

        return $enviados;
    }

    /**
     * @param OutputInterface $output
     * @param $parte
     * @param $titulo
     * @param $mensaje
     * @param $em
     */
    private function notificarSobreParte(OutputInterface $output, $parte, $titulo, $mensaje, $em)
    {
        $output->writeln('* ' . $parte->getId() . ": " . $parte->getAlumno() . ' - ' . $parte->getFechaSuceso()->format('d/m/Y'));
        $this->notificar($output, $parte->getAlumno()->getGrupo()->getTutores(), $titulo, $mensaje, 'tutor/a');
        $this->notificar($output, array($parte->getUsuario()), $titulo, $mensaje, 'autor/a');
        $this->notificar($output, $em->getRepository('AppBundle:Usuario')->getRevisores(), $titulo, $mensaje, 'comisionario/a');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Ejecutando tareas de cron</info>');
        $em = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln('<info>Localizando partes prescritos</info>');

        $partes = $em->getRepository('AppBundle:Parte')->findPrescritos($this->getContainer()->getParameter('dias_prescripcion'));

        foreach($partes as $parte) {
            $parte->setPrescrito(true);

            $titulo = 'Parte prescrito de ' . $parte->getAlumno();

            $mensaje = 'El parte #' . $parte->getId() . ' de ' . $parte->getAlumno() . ' con fecha ' . $parte->getFechaSuceso()->format('d/m/Y') .
                ' ha prescrito y no podrá ser sancionado.';

            $this->notificarSobreParte($output, $parte, $titulo, $mensaje, $em);
        }
        $em->flush();

        $output->writeln('<info>Localizando partes a punto de prescribir</info>');

        $partes = $em->getRepository('AppBundle:Parte')->findPrescritos($this->getContainer()->getParameter('dias_aviso_previo'));

        foreach($partes as $parte) {

            $titulo = 'Recordatorio: Parte a punto de prescribir de ' . $parte->getAlumno();

            $mensaje = 'El parte #' . $parte->getId() . ' de ' . $parte->getAlumno() . ' con fecha ' . $parte->getFechaSuceso()->format('d/m/Y') .
                ' está próximo a ser marcado como prescrito y no podrá ser sancionado.';

            $this->notificarSobreParte($output, $parte, $titulo, $mensaje, $em);
        }
    }
}
