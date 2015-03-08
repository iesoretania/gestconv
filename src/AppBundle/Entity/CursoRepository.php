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
 * CursoRepository
 *
 * Clase repositorio para añadir métodos adicionales
 */
class CursoRepository extends EntityRepository
{
    public function getResumenPorFecha($fechas)
    {
        $cursos = $this->getEntityManager()
            ->getRepository('AppBundle:Alumno')
            ->createQueryBuilder('a')
            ->leftJoin('AppBundle:Grupo', 'g', 'WITH', 'a.grupo = g')
            ->leftJoin('AppBundle:Curso', 'c', 'WITH', 'g.curso = c')
            ->leftJoin('AppBundle:Parte', 'p', 'WITH', 'p.alumno = a')
            ->leftJoin('AppBundle:Sancion', 's', 'WITH', 'p.sancion = s')
            ->select('c')
            ->addSelect('count(p.id)')
            ->addSelect('count(p.fechaAviso)')
            ->addSelect('count(p.sancion)')
            ->addSelect('count(s.fechaComunicado)')
            ->addSelect('count(s.motivosNoAplicacion)')
            ->addSelect('count(s.fechaInicioSancion)')
            ->addSelect('sum(p.prescrito)')
            ->addSelect('max(p.fechaSuceso)')
            ->addSelect('max(s.fechaSancion)');

        if ($fechas['desde']) {
            $cursos = $cursos
                ->andWhere('p.fechaSuceso >= :desde')
                ->setParameter('desde', $fechas['desde']);
        }
        if ($fechas['hasta']) {
            $cursos = $cursos
                ->andWhere('p.fechaSuceso <= :hasta')
                ->setParameter('hasta', $fechas['hasta']);
        }

        return $cursos
            ->addOrderBy('c.descripcion')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }
}
