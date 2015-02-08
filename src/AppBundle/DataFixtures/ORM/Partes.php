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

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Grupo;
use AppBundle\Entity\Parte;
use AppBundle\Entity\TramoParte;
use AppBundle\Entity\Usuario;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class Partes extends AbstractFixture implements OrderedFixtureInterface
{
    protected $faker;

    static $conductas;
    static $frecuenciaCuenta = [1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 4];

    /**
     * Devuelve una cuenta al azar donde 1 y 2 son muy probables mientras que 3 y 4 poco
     *
     * @return int
     */
    static function getCuenta()
    {
        return self::$frecuenciaCuenta[rand(0, count(self::$frecuenciaCuenta)-1)];
    }
    /**
     * Crea un parte a uno o varios alumnos por parte de un profesor
     *
     * @param $manager EntityManager de Doctrine
     * @param $profesor Usuario al que se asigna el parte
     */
    function createParte(ObjectManager $manager, Usuario $profesor, TramoParte $tramo)
    {
        $grupos = $manager->getRepository('AppBundle:Grupo')->findAll();
        shuffle($grupos);
        $grupo = next($grupos);

        $alumnos = $manager->getRepository('AppBundle:Alumno')
            ->createQueryBuilder('a')
            ->where('a.grupo = :grupo')
            ->setParameter('grupo', $grupo)
            ->getQuery()
            ->getResult();

        shuffle($alumnos);

        shuffle(self::$conductas);
        reset(self::$conductas);

        $cuentaConductas = self::getCuenta();
        $cuentaAlumnos = (rand(1, 10) > 8) ? rand(2, 3) : 1;

        $parte = new Parte();
        $parte->setAnotacion($this->faker->text());
        $parte->setUsuario($profesor);
        $fecha = new \DateTime('-' . rand(2, 120) . ' days');
        $parte->setFechaSuceso($fecha);
        $fechaCreacion = clone $fecha;
        $fechaCreacion->modify('+' . rand(1, 96) . ' hours');
        $parte->setFechaCreacion($fechaCreacion);
        $parte->setPrescrito(false);
        $parte->setHayExpulsion(false);
        $parte->setTramo($tramo);
        for ($i = 0; $i < $cuentaConductas; $i++) {
            $parte->addConducta(next(self::$conductas));
        }
        for ($i = 0; $i < $cuentaAlumnos; $i++) {
            $nuevoParte = clone $parte;
            $nuevoParte->setAlumno(next($alumnos));
            $manager->persist($nuevoParte);
        }
    }

    public function getOrder()
    {
        return 300;
    }

    public function load(ObjectManager $manager)
    {
        $this->faker = \Faker\Factory::create();
        self::$conductas = $manager->getRepository('AppBundle:TipoConducta')->findAll();
        $tramo = $manager->getRepository('AppBundle:TramoParte')->findOneByDescripcion('En clase');

        $usuarios = $manager->getRepository('AppBundle:Usuario')->findAll();
        shuffle($usuarios);

        for ($i = 1; $i < count($usuarios); $i++) {
            $usuario = next($usuarios);
            $cuenta = self::getCuenta();
            for ($j = 0; $j < $cuenta; $j++) {
                self::createParte($manager, $usuario, $tramo);
            }
        }

        $manager->flush();
    }
}