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
    protected $faker;

    /**
     * Rellena los datos de un usuario aleatoriamente
     *
     * @return Usuario
     */
    function generateUsuario()
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

        $username = $nombre['nombre'][0] . mb_substr($nombre['apellido1'], 0, 3, 'UTF-8') .
            mb_substr($nombre['apellido2'], 0, 3, 'UTF-8');
        $username = mb_strtolower($username, 'UTF-8');
        $username .= rand(0, 9) . rand(0, 9) . rand(0, 9);

        $busqueda = ['á', 'é', 'í', 'ó', 'ú', 'ñ'];
        $cambiarPor = ['a', 'e', 'i', 'o', 'u', 'n'];
        $username = str_replace($busqueda, $cambiarPor, $username);

        $usuario->setNombreUsuario($username);
        if (rand(0, 4)>0) {
            $usuario->setEmail($this->faker->email);
        }

        $usuario->setPassword(password_hash('12345', PASSWORD_DEFAULT));

        return $usuario;
    }

    public function getOrder()
    {
        return 100;
    }

    public function load(ObjectManager $manager)
    {
        $this->faker = \Faker\Factory::create('es_ES');

        $usuario = new Usuario();
        $usuario->setNombreUsuario('admin');
        $usuario->setPassword(password_hash('admin', PASSWORD_DEFAULT));
        $usuario->setNombre('Administrador');
        $usuario->setApellido1('Admin');
        $usuario->setEsAdministrador(true);
        $usuario->setEsRevisor(false);
        $usuario->setEstaActivo(true);
        $usuario->setEstaBloqueado(false);

        $manager->persist($usuario);

        $usuario = new Usuario();
        $usuario->setNombreUsuario('comisionario');
        $usuario->setPassword(password_hash('comisionario', PASSWORD_DEFAULT));
        $usuario->setNombre('Comisión');
        $usuario->setApellido1('Convivencia');
        $usuario->setEsAdministrador(false);
        $usuario->setEsRevisor(true);
        $usuario->setEstaActivo(true);
        $usuario->setEstaBloqueado(false);
        $manager->persist($usuario);

        $usuario = new Usuario();
        $usuario->setNombreUsuario('usuario');
        $usuario->setPassword(password_hash('usuario', PASSWORD_DEFAULT));
        $usuario->setNombre('Juan');
        $usuario->setApellido1('Nadie');
        $usuario->setApellido2('Nadie');
        $usuario->setEsAdministrador(false);
        $usuario->setEsRevisor(false);
        $usuario->setEstaActivo(true);
        $usuario->setEstaBloqueado(false);
        $manager->persist($usuario);

        for ($i = 0; $i < rand(30, 45); $i++) {
            $usuario = self::generateUsuario();
            $usuario->setEsAdministrador(false);
            $usuario->setEsRevisor(rand(1, 20) > 19);
            $usuario->setEstaActivo(true);
            $usuario->setEstaBloqueado(false);
            $manager->persist($usuario);
        }

        $manager->flush();
    }
}