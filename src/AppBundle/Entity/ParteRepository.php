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
    public function findAllOrdered()
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->orderBy('p.fechaSuceso', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findNotificados()
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->innerJoin('p.alumno', 'a')
            ->where('p.fechaAviso IS NOT NULL');
    }

    public function findNoNotificados()
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->innerJoin('p.alumno', 'a')
            ->where('p.fechaAviso IS NULL')
            ->andWhere('p.prescrito = :prescrito')
            ->setParameter('prescrito', false);
    }

    public function findAllNoNotificadosPorAlumnoYUsuario($alumno, $usuario)
    {
        return $this->findNoNotificadosPorUsuarioOTutoria($usuario)
            ->andWhere('p.alumno = :alumno')
            ->setParameter('usuario', $usuario)
            ->setParameter('alumno', $alumno)
            ->orderBy('p.fechaSuceso', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllNoNotificadosPorAlumno($alumno)
    {
        return $this->findNoNotificados()
            ->andWhere('p.alumno = :alumno')
            ->setParameter('alumno', $alumno)
            ->addOrderBy('p.fechaSuceso', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPorUsuarioOTutoria($usuario)
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.alumno', 'a')
            ->innerJoin('AppBundle:Grupo', 'g', 'WITH', 'a.grupo = g')
            ->andWhere('p.usuario = :usuario OR a.grupo = :grupo')
            ->setParameter('usuario', $usuario)
            ->setParameter('grupo', $usuario->getTutoria());
    }

    public function findPorUsuario($usuario)
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->where('p.usuario = :usuario')
            ->setParameter('usuario', $usuario);
    }

    public function findNoNotificadosPorUsuario($usuario)
    {
        return $this->findPorUsuario($usuario)
            ->andWhere('p.prescrito = :prescrito')
            ->setParameter('prescrito', false)
            ->andWhere('p.fechaAviso IS NULL');
    }

    public function findNoNotificadosPorUsuarioOTutoria($usuario)
    {
        return $this->findNoNotificados()
            ->innerJoin('AppBundle:Grupo', 'g', 'WITH', 'a.grupo = g')
            ->andWhere('p.usuario = :usuario OR a.grupo = :grupo')
            ->setParameter('usuario', $usuario)
            ->setParameter('grupo', $usuario->getTutoria());
    }

    public function findAllNoNotificadosPorUsuario($usuario)
    {
        return $this->findNoNotificadosPorUsuario($usuario)
            ->orderBy('p.fechaSuceso', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllPorUsuarioOTutoria($usuario)
    {
        return $this->findPorUsuarioOTutoria($usuario)
            ->orderBy('p.fechaSuceso', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countAll()
    {
        return $this->getEntityManager()
            ->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countNoNotificados()
    {
        return $this->findNoNotificados()
            ->select('COUNT(p.id)')
            ->andWhere('p.prescrito = :prescrito')
            ->setParameter('prescrito', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countNoNotificadosPorUsuario($usuario)
    {
        return $this->findNoNotificadosPorUsuario($usuario)
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countNoNotificadosPorUsuarioOTutoria($usuario)
    {
        return $this->findNoNotificadosPorUsuarioOTutoria($usuario)
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPorUsuarioOTutoria($usuario)
    {
        return $this->findPorUsuarioOTutoria($usuario)
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function countPorUsuario($usuario)
    {
        return $this->findPorUsuario($usuario)
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countSancionables()
    {
        return $this->findNotificados()
            ->select('COUNT(p.id)')
            ->andWhere('p.prescrito = false')
            ->andWhere('p.sancion IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countSancionablesPrioritarios()
    {
        return $this->findNotificados()
            ->select('COUNT(p.id)')
            ->andWhere('p.prescrito = false')
            ->andWhere('p.sancion IS NULL')
            ->andWhere('p.prioritario = true')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllSancionablesPorAlumno($alumno)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p')
            ->from('AppBundle\Entity\Parte', 'p', 'p.id')
            ->andWhere('p.fechaAviso IS NOT NULL')
            ->andWhere('p.sancion IS NULL')
            ->andWhere('p.prescrito = false')
            ->andWhere('p.alumno = :alumno')
            ->orderBy('p.fechaSuceso')
            ->setParameter('alumno', $alumno)
            ->getQuery()
            ->getResult();
    }

    public function findPrescritos($plazo, $filtrarRecordados = false)
    {
        $fecha = new \DateTime();
        $fecha->sub(new \DateInterval('P' . $plazo . 'D'));

        $data = $this->getEntityManager()
            ->getRepository('AppBundle:Parte')
            ->createQueryBuilder('p')
            ->where('p.sancion IS NULL')
            ->andWhere('p.prescrito = false')
            ->andWhere('p.fechaSuceso < :fechaLimite');

        if ($filtrarRecordados) {
            $data = $data->andWhere('p.fechaRecordatorio IS NULL');
        }

        return $data
            ->setParameter('fechaLimite', $fecha)
            ->getQuery()
            ->getResult();
    }
}
