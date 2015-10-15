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

use AppBundle\Utils\RepositoryUtils;
use Doctrine\ORM\EntityRepository;

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
         ->select('u');

        $data = RepositoryUtils::resumenConvivencia($data, $fechas)
         ->leftJoin('AppBundle:Parte', 'p', 'WITH', 'p.usuario = u')
         ->leftJoin('AppBundle:Sancion', 's', 'WITH', 'p.sancion = s');

        $data = $data
            ->addOrderBy('u.apellidos')
            ->addOrderBy('u.nombre')
            ->groupBy('u.id')
            ->getQuery()
            ->getResult();

        return $data;
    }

    public function getRevisores()
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Usuario')
            ->createQueryBuilder('u')
            ->where('u.estaActivo', true)
            ->where('u.esRevisor', true)
            ->getQuery()
            ->getResult();
    }
}
