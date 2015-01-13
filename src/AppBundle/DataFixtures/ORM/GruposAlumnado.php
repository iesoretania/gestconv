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

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Grupo;
use AppBundle\Entity\Curso;
use AppBundle\Entity\Alumno;
use AppBundle\DataFixtures\Utils\Nombres;

class GruposAlumnado extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Rellena los datos de un alumno aleatoriamente
     *
     * @param int $anio Año de nacimiento, null si aleatorio
     * @return Alumno
     */
    static function generateAlumno($anio = null)
    {
        $alumno = new Alumno();
        // Generar tipo de alumno (0=H, 1=M)
        $tipo = rand(0, 1);

        $estudiante = Nombres::generateNombreCompleto($tipo);
        $padre = Nombres::GenerateNombreCompleto(0);
        $padre['apellido1'] = $estudiante['apellido1'];
        $madre = Nombres::GenerateNombreCompleto(1);
        $madre['apellido1'] = $estudiante['apellido2'];

        // Generar otros campos al azar
        $nie = rand(1000, 10000000);

        // Fecha nacimiento
        if ($anio == null) {
            $anio = rand(1990, 2002);
        }
        else {
            // simular que puede ser repetidor
            if (rand(0,8) > 4) {
                $anio = $anio - rand(0, 2);
            }
        }
        $mes = rand(1, 12);
        $dia = rand(1, 31);

        $alumno->setNombre($estudiante['nombre']);
        $alumno->setApellido1($estudiante['apellido1']);
        // A veces no generar un segundo apellido
        if (rand(0,100) > 0) {
            $alumno->setApellido2($estudiante['apellido2']);
        }
        $alumno->setNie($nie);
        $alumno->setFechaNacimiento(new \DateTime($anio . '-' . $mes . '-' . $dia));
        $alumno->setTelefono1(Nombres::generateTelefono((rand(1, 10) < 7)));
        // A veces generar un segundo teléfono móvil
        if (rand(0, 10) > 3) {
            $alumno->setTelefono2(Nombres::generateTelefono(true));
        }
        if (rand(0, 30) > 0) {
            // poner dos tutores
            if (rand(0,1) == 0) {
                $alumno->setTutor1(Nombres::composeNombre($padre));
                $alumno->setTutor2(Nombres::composeNombre($madre));
            }
            else {
                $alumno->setTutor1(Nombres::composeNombre($madre));
                $alumno->setTutor2(Nombres::composeNombre($padre));
            }
        }
        else {
            // poner un tutor
            if (rand(0, 1) == 0) {
                $alumno->setTutor1(Nombres::composeNombre($padre));
            }
            else {
                $alumno->setTutor1(Nombres::composeNombre($madre));
            }
        }

        return $alumno;
    }

    public function getOrder()
    {
        return 50;
    }

    public function load(ObjectManager $manager)
    {
        $cursos = ['1ºESO' => 2, '2ºESO' => 2, '3ºESO' => 2, '4ºESO' => 1, '1ºBACH' => 2, '2ºBACH' => 1];

        $anio = 2002;
        foreach ($cursos as $nivel => $grupos) {
            $curso = new Curso();
            $curso->setDescripcion($nivel);
            $manager->persist($curso);

            if ($grupos > 1) {
                for ($i = 0; $i < $grupos; $i++) {
                    $grupo = new Grupo();
                    $grupo->setCurso($curso);
                    $grupo->setDescripcion($nivel . '-' . chr(65 + $i));
                    $manager->persist($grupo);

                    for ($k = 0; $k < rand(15, 20); $k++) {
                        $alumno = self::generateAlumno($anio);
                        $alumno->setGrupo($grupo);
                        $manager->persist($alumno);
                    }
                }
            }
            else {
                $grupo = new Grupo();
                $grupo->setCurso($curso);
                $grupo->setDescripcion($nivel);
                $manager->persist($grupo);

                for ($k = 0; $k < rand(22, 30); $k++) {
                    $alumno = self::generateAlumno($anio);
                    $alumno->setGrupo($grupo);
                    $manager->persist($alumno);
                }
            }
            $anio--;
        }
        $manager->flush();
    }
}