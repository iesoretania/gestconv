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
use Doctrine\ORM\QueryBuilder;

/**
 * SancionRepository
 *
 * Clase repositorio para añadir métodos adicionales
 */
class SancionRepository extends EntityRepository
{
    protected function count(QueryBuilder $expresion)
    {
        return $expresion
            ->select('COUNT(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

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

    public function findAlumnosNoNotificados()
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Sancion')
            ->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->innerJoin('AppBundle:Parte', 'p', 'WITH', 'p.sancion = s')
            ->join('p.alumno', 'a')
            ->andWhere('s.fechaComunicado IS NULL')
            ->andWhere('s.motivosNoAplicacion IS NULL')
            ->groupBy('a.id')
            ->orderBy('a.id')
            ->getQuery()
            ->getResult();
    }

    public function findNoNotificadosPorTutoria($usuario)
    {
        return $this->findNoNotificados()
            ->join('a.grupo', 'g')
            ->andWhere('a.grupo = :grupo')
            ->setParameter('grupo', $usuario->getTutoria());
    }

    public function findAllNoNotificadosPorTutoria($usuario)
    {
        return $this->findNoNotificadosPorTutoria($usuario)
            ->getQuery()
            ->getResult();
    }

    public function countNoNotificadosPorTutoria($usuario)
    {
        return $this->count($this->findNoNotificadosPorTutoria($usuario));
    }

    public function countNoNotificados()
    {
        return $this->count($this->findNoNotificados());

    }

    public function countAlumnosNoNotificados()
    {
        return count($this->findAlumnosNoNotificados());
    }


    public function findTodos()
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Sancion')
            ->createQueryBuilder('s');
    }

    public function findAllPorAlumno($alumno)
    {
        return $this->findTodos()
            ->distinct()
            ->innerJoin('s.partes', 'p')
            ->where('p.alumno = :alumno')
            ->setParameter('alumno', $alumno)
            ->orderBy('s.fechaSancion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countAll()
    {
        return $this->count($this->findTodos());
    }
}
