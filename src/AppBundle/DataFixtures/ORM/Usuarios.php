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

use AppBundle\DataFixtures\Utils\Nombres;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Usuario;

class Usuarios extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Rellena los datos de un usuario aleatoriamente
     *
     * @return Usuario
     */
    static function generateUsuario()
    {
        $usuario = new Usuario();
        // Generar tipo de alumno (0=H, 1=M)
        $tipo = rand(0, 1);

        $nombre = Nombres::generateNombreCompleto($tipo);

        $usuario->setNombre($nombre['nombre']);
        $usuario->setApellido1($nombre['apellido1']);
        // A veces no generar un segundo apellido
        if (rand(0,100) > 0) {
            $usuario->setApellido2($nombre['apellido2']);
        }

        return $usuario;
    }

    public function getOrder()
    {
        return 100;
    }

    public function load(ObjectManager $manager)
    {
        $usuario = new Usuario();
        $usuario->setNombre('Administrador');
        $usuario->setApellido1('Admin');
        $usuario->setEsAdministrador(true);
        $usuario->setEsRevisor(false);
        $manager->persist($usuario);

        $usuario = new Usuario();
        $usuario->setNombre('Comisión');
        $usuario->setApellido1('Convivencia');
        $usuario->setEsAdministrador(false);
        $usuario->setEsRevisor(true);
        $manager->persist($usuario);


        for ($i = 0; $i < rand(20, 40); $i++) {
            $usuario = self::generateUsuario();
            $usuario->setEsAdministrador(false);
            $usuario->setEsRevisor(rand(1, 20) > 19);

            $manager->persist($usuario);
        }
        $manager->flush();
    }
}