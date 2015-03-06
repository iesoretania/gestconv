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
 * AlumnoRepository
 *
 * Clase repositorio para añadir métodos adicionales
 */
class AlumnoRepository extends EntityRepository
{
    public function findConPartesAunNoNotificados()
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Alumno')
            ->createQueryBuilder('a')
            ->innerJoin('AppBundle:Parte', 'p', 'WITH', 'p.alumno = a')
            ->where('p.fechaAviso IS NULL')
            ->orderBy('a.apellido1', 'ASC')
            ->addOrderBy('a.apellido2', 'ASC')
            ->addOrderBy('a.nombre', 'ASC');
    }

    public function findConPartesAunNoNotificadosPorUsuario(Usuario $usuario)
    {
        $orX = $this->getEntityManager()->createQueryBuilder()
            ->expr()->orX()
            ->add('p.usuario = :usuario')
            ->add('a.grupo = :grupo');

        return $this->findConPartesAunNoNotificados()
            ->innerJoin('AppBundle:Grupo', 'g', 'WITH', 'a.grupo = g')
            ->andWhere($orX)
            ->setParameter('grupo', $usuario->getTutoria())
            ->setParameter('usuario', $usuario);
    }

    public function findAllConPartesAunNoNotificados()
    {
        return $this->findConPartesAunNoNotificados()
            ->getQuery()
            ->getResult();
    }

    public function findAllConPartesAunNoNotificadosPorUsuario(Usuario $usuario)
    {
        return $this->findConPartesAunNoNotificadosPorUsuario($usuario)
            ->getQuery()
            ->getResult();
    }

    public function findAllConPartesPendientesSancion()
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->select('a')
            ->addSelect('COUNT(p.id)')
            ->addSelect('MIN(p.fechaSuceso)')
            ->addSelect('MAX(p.fechaSuceso)')
            ->innerJoin('AppBundle:Alumno', 'a')
            ->where('p.fechaAviso IS NOT NULL')
            ->andWhere('p.sancion IS NULL')
            ->andWhere('p.alumno = a')
            ->andWhere('p.prescrito = false')
            ->groupBy('a.id')
            ->orderBy('a.apellido1', 'ASC')
            ->addOrderBy('a.apellido2', 'ASC')
            ->addOrderBy('a.nombre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findConSancionesAunNoNotificadas()
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Alumno')
            ->createQueryBuilder('a')
            ->leftJoin('a.partes', 'p')
            ->leftJoin('p.sancion', 's')
            ->where('s.fechaComunicado IS NULL')
            ->andWhere('s.motivosNoAplicacion IS NULL')
            ->andWhere('p.alumno = a')
            ->andWhere('p.sancion = s')
            ->orderBy('a.apellido1', 'ASC')
            ->addOrderBy('a.apellido2', 'ASC')
            ->addOrderBy('a.nombre', 'ASC');
    }

    public function findAllConSancionesAunNoNotificadas()
    {
        return $this->findConSancionesAunNoNotificadas()
            ->getQuery()
            ->getResult();
    }

    public function findAllConSancionesAunNoNotificadasPorTutoria($usuario)
    {
        return $this->findConSancionesAunNoNotificadas()
            ->join('a.grupo', 'g')
            ->andWhere('g = :grupo')
            ->setParameter('grupo', $usuario->getTutoria())
            ->getQuery()
            ->getResult();
    }

    public function getResumenPartesSancionesYExpulsiones()
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Alumno')
            ->createQueryBuilder('a')
            ->leftJoin('AppBundle:Parte', 'p', 'WITH', 'p.alumno = a')
            ->leftJoin('AppBundle:Sancion', 's', 'WITH', 'p.sancion = s')
            ->select('a')
            ->addSelect('count(p.id)')
            ->addSelect('count(p.fechaAviso)')
            ->addSelect('count(p.sancion)')
            ->addSelect('count(s.fechaComunicado)')
            ->addSelect('count(s.motivosNoAplicacion)')
            ->addSelect('count(s.fechaInicioSancion)')
            ->addSelect('sum(p.prescrito)')
            ->addSelect('max(p.fechaSuceso)')
            ->addSelect('max(s.fechaSancion)');
    }

    public function getResumenPartesSancionesYExpulsionesEnFecha($data)
    {
        $alumnos = $this->getResumenPartesSancionesYExpulsiones();

        if ($data['desde']) {
            $alumnos = $alumnos
                ->andWhere('p.fechaSuceso >= :desde')
                ->setParameter('desde', $data['desde']);
        }
        if ($data['hasta']) {
            $alumnos = $alumnos
                ->andWhere('p.fechaSuceso <= :hasta')
                ->setParameter('hasta', $data['hasta']);
        }
        return $alumnos;
    }

    public function findResumenTutorPartesSancionesYExpulsionesEnFecha($tutor, $data)
    {
        $resultado = $this->getResumenPartesSancionesYExpulsionesEnFecha($data);

        if ($tutor) {
            $resultado = $resultado
                ->andWhere('a.grupo = :grupo')
                ->setParameter('grupo', $tutor->getTutoria());
        }
        return $resultado
            ->addOrderBy('a.apellido1')
            ->addOrderBy('a.apellido2')
            ->addOrderBy('a.nombre')
            ->groupBy('a.id')
            ->getQuery()
            ->getResult();
    }
}
