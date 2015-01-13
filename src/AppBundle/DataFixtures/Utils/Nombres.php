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

namespace AppBundle\DataFixtures\Utils;

class Nombres
{
    static $nombres = [
        0 => ['Juan', 'Pedro', 'Luis', 'José', 'José', 'Valentín', 'Francisco', 'Jerónimo', 'Blas', 'Diego', 'Manuel',
            'Fernando', 'Miguel', 'Rafael', 'Antonio', 'Rubén', 'Jaime', 'Sergio', 'Sebastián', 'Jesús'],
        1 => ['Ana', 'Rocío', 'María', 'María', 'Cristina', 'Dolores', 'Olga', 'Inés', 'Carmen', 'Carmen', 'Eva',
            'Laura', 'Irene', 'Marta', 'Carolina', 'Isabel', 'Elena', 'Olga', 'Almudena', 'Raquel', 'Luisa',
            'Inmaculada', 'Nieves']
    ];

    static $segundos_nombres = [
        0 => ['Juan', 'Pedro', 'Luis', 'José', 'José', 'Francisco', 'Manuel', 'María', 'María', 'Miguel',
            'Antonio', 'Jesús'],
        1 => ['José', 'María', 'María', 'María', 'María', 'María', 'Victoria']
    ];

    static $apellidos = [
        'López', 'López', 'Moreno', 'Heredia', 'Fernández', 'Santiago', 'Muñoz', 'Fajardo', 'Torres', 'Sánchez',
        'Martínez', 'Martínez', 'Díaz', 'Díaz', 'García', 'García', 'García', 'Ojeda', 'Rodríguez', 'Vera', 'León',
        'Romero', 'Serrano', 'González', 'González', 'Aguilar', 'Pérez', 'Pérez', 'Gómez', 'Cuevas', 'Rivero', 'Soler',
        'Vázquez', 'Gallardo', 'Romero', 'Casado', 'Martín', 'Pardo', 'Valverde', 'Merino', 'Velasco', 'Rentero'
    ];

    /**
     * Genera un número de teléfono aleatorio
     *
     * @param boolean $esMovil
     * @return string
     */
    static function generateTelefono($esMovil)
    {
        $prefijos = ['69', '65', '60'];
        if ($esMovil) {
            $numero = (rand(0, 10) > 9) ? '7' : '6';
            $cuantos = 8;
        }
        else {
            $numero = "953";
            $cuantos = 4;
            if (rand(0, 15) > 0) {
                $numero .= $prefijos[rand(0,2)];
            }
            else {
                $numero .= "03";
            }
        }

        for ($i = 0; $i < $cuantos; $i++) {
            $numero .= rand(0, 9);
        }

        return $numero;
    }

    /**
     * @param int $tipo 0 para generar un nombre de hombre y 1 de mujer
     * @return array
     */
    static function generateNombreCompleto($tipo)
    {
        // Obtener un nombre al azar
        $nNombre = rand(0, count(self::$nombres[$tipo])-1);
        $nombre = self::$nombres[$tipo][$nNombre];

        // A veces, obtener un segundo nombre
        $haySegundo = rand(0, 5);
        if ($haySegundo < 2) {
            $nNombre = rand(0, count(self::$segundos_nombres[$tipo])-1);
            $nombre2 = self::$segundos_nombres[$tipo][$nNombre];
            if ($nombre != $nombre2) {
                $nombre .= ' ' . $nombre2;
            }
        }

        // Obtener dos apellidos
        $apellido1 = self::$apellidos[rand(0, count(self::$apellidos)-1)];
        $apellido2 = self::$apellidos[rand(0, count(self::$apellidos)-1)];

        return ['nombre' => $nombre, 'apellido1' => $apellido1, 'apellido2' => $apellido2];
    }

    /**
     * Devuelve una cadena con el nombre completo
     *
     * @param array $nombre
     * @return string
     */
    static function composeNombre($nombre)
    {
        return $nombre['nombre'] . ' ' . $nombre['apellido1'] . ' ' . $nombre['apellido2'];
    }
}