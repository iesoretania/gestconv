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

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ParteRepository
 *
 * Clase repositorio para añadir métodos adicionales
 */
class ParteRepository extends EntityRepository
{
    public function findAllNoNotificadosPorAlumnoYUsuario(Alumno $alumno, Usuario $usuario)
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->innerJoin('p.alumno', 'a')
            ->where('p.fechaAviso IS NULL')
            ->andWhere('p.usuario = :usuario')
            ->andWhere('p.alumno = :alumno')
            ->setParameter('usuario', $usuario)
            ->setParameter('alumno', $alumno)
            ->getQuery()
            ->getResult();
    }
}
