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
use Doctrine\ORM\Query\Expr;

/**
 * UsuarioRepository
 *
 * Clase repositorio para añadir métodos adicionales
 */
class UsuarioRepository extends EntityRepository
{
    public function getResumenConvivencia($fechas)
    {
        $data = $this->getEntityManager()
         ->getRepository('AppBundle:Usuario')
         ->createQueryBuilder('u')
         ->select('u')
         ->addSelect('COUNT(p.id)')
         ->addSelect('COUNT(p.fechaAviso)')
         ->addSelect('COUNT(DISTINCT s.id)')
         ->addSelect('COUNT(s.fechaComunicado)')
         ->addSelect('COUNT(s.motivosNoAplicacion)')
         ->addSelect('COUNT(DISTINCT s.fechaInicioSancion)')
         ->addSelect('SUM(p.prescrito)')
         ->addSelect('MAX(p.fechaSuceso)')
         ->addSelect('MAX(s.fechaSancion)')
         ->leftJoin('AppBundle:Parte', 'p', 'WITH', 'p.usuario = u')
         ->leftJoin('AppBundle:Sancion', 's', 'WITH', 'p.sancion = s')
         ->groupBy('u.id')
         ->addGroupBy('s.id');

        if ($fechas['desde']) {
            $data = $data
                ->andWhere('p.fechaSuceso >= :desde')
                ->setParameter('desde', $fechas['desde']);
        }

        if ($fechas['hasta']) {
            $data = $data
                ->andWhere('p.fechaSuceso <= :hasta')
                ->setParameter('hasta', $fechas['hasta']);
        }

        $data = $data
            ->addOrderBy('u.apellidos')
            ->addOrderBy('u.nombre')
            ->groupBy('u.id')
            ->getQuery()
            ->getResult();

        return $data;
    }
}
