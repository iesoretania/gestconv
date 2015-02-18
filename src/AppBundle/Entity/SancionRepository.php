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
 * SancionRepository
 *
 * Clase repositorio para añadir métodos adicionales
 */
class SancionRepository extends EntityRepository
{
    public function findNoNotificados()
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Sancion')
            ->createQueryBuilder('s')
            ->leftJoin('s.partes', 'p')
            ->leftJoin('p.alumno', 'a')
            ->andWhere('s.fechaComunicado IS NULL')
            ->andWhere('s.motivosNoAplicacion IS NULL');
    }

    public function findAllNoNotificadosPorAlumno($alumno)
    {
        return $this->findNoNotificados()
            ->andWhere('p.alumno = :alumno')
            ->setParameter('alumno', $alumno)
            ->getQuery()
            ->getResult();
    }

    public function findNoNotificadosPorTutoria($usuario)
    {
        return $this->findNoNotificados()
            ->join('a.grupo', 'g')
            ->andWhere('g.tutor = :usuario')
            ->setParameter('usuario', $usuario);
    }

    public function findAllNoNotificadosPorTutoria($usuario)
    {
        return $this->findNoNotificadosPorTutoria($usuario)
            ->getQuery()
            ->getResult();
    }

    public function countNoNotificadosPorTutoria($usuario)
    {
        return $this->findNoNotificadosPorTutoria($usuario)
            ->select('COUNT(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

}
