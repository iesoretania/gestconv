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

use AppBundle\Entity\ActitudFamiliaSancion;
use AppBundle\Entity\CategoriaAviso;
use AppBundle\Entity\CategoriaConducta;
use AppBundle\Entity\CategoriaMedida;
use AppBundle\Entity\EstadoSancion;
use AppBundle\Entity\TipoConducta;
use AppBundle\Entity\TipoMedida;
use AppBundle\Entity\TramoParte;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class DatosIniciales extends AbstractFixture implements OrderedFixtureInterface
{
    public function generateTramoParte(ObjectManager $manager)
    {
        $tramos = [
            "En clase",
            "En el intercambio de clases",
            "Entrada/Salida",
            "Recreo",
            "Fuera del centro",
            "En el comedor",
            "En el aula matinal",
            "Durante las actividades extraescolares",
            "Otros"
        ];

        foreach($tramos as $descripcion) {
            $tramo = new TramoParte();
            $tramo->setDescripcion($descripcion);
            $manager->persist($tramo);
        }
    }

    /**
     * @param ObjectManager $manager
     * @param $items Array de categorías. Cada categoría es un array de ítems
     * @param $niveles Niveles opcionales a asignar a cada categoría
     * @param $claseCategoria Clase correspondiente a la categoría
     * @param $claseItems Clase correspondiente a los ítems
     */
    public function createItemsCategoria(ObjectManager $manager, $items, $niveles, $claseCategoria, $claseItems)
    {
        $refCategoria = '\\AppBundle\\Entity\\' . $claseCategoria;
        $refItems = '\\AppBundle\\Entity\\' . $claseItems;

        $num = 0;
        foreach($items as $descripcionCategoria => $tipos) {
            $categoria = new $refCategoria;
            $categoria->setDescripcion($descripcionCategoria);
            $categoria->setNivel(is_null($niveles) ? null : $niveles[$num++]);
            $manager->persist($categoria);

            foreach($tipos as $descripcionTipo) {
                $tipo = new $refItems;
                $tipo->setDescripcion($descripcionTipo);
                $tipo->setCategoria($categoria);
                $manager->persist($tipo);
            }
        }
    }

    public function generateTiposConducta(ObjectManager $manager)
    {
        $niveles = [1, 2, 0];
        $conductas = [
            'Conductas contrarias' =>
                [
                    "Perturbación del normal desarrollo de las actividades de clase",
                    "Falta de colaboración sistemática en la realización de las actividades",
                    "Impedir o dificultar el estudio a sus compañeros",
                    "Faltas injustificadas de puntualidad",
                    "Faltas injustificadas de asistencia a clase",
                    "Actuaciones incorrectas hacia algún miembro de la comunidad educativa",
                    "Daños en instalaciones o docum. del Centro o en pertenencias de un miembro"
                ],
            'Conductas graves' =>
                [
                    "Agresión física a un miembro de la comunidad educativa",
                    "Injurias y ofensas contra un miembro de la comunidad educativa",
                    "Acoso escolar",
                    "Actuaciones perjudiciales para la salud y la integridad, o incitación a ellas",
                    "Vejaciones o humillaciones contra un miembro de la comunidad educativa",
                    "Amenazas o coacciones a un miembro de la comunidad educativa",
                    "Suplantación de la personalidad, y falsificación o sustracción de documentos",
                    "Deterioro grave de instalac. o docum. del Centro, o pertenencias de un miembro",
                    "Reiteración en un mismo curso de conductas contrarias a normas de convivencia",
                    "Impedir el normal desarrollo de las actividades del centro",
                    "Incumplimiento de las correcciones impuestas"
                ],
            'Otras conductas contrarias' =>
                [
                    'Las descritas con detalle más abajo'
                ]
        ];

        self::createItemsCategoria($manager, $conductas, $niveles, 'CategoriaConducta', 'TipoConducta');
    }

    public function generateTiposMedida(ObjectManager $manager)
    {
        $niveles = [1, 2, 0];
        $medidas = [
            'Correciones a las conductas contrarias' =>
                [
                    "Amonestación oral",
                    "Apercibimiento por escrito",
                    "Realizar tareas dentro y fuera del horario lectivo en el Centro",
                    "Suspender el derecho de asistencia a determinadas clases entre 1 y 3 días",
                    "Suspender el derecho de asistencia al centro entre 1 y 3 días",

                ],
            'Medidas disciplinarias por conductas gravemente perjudiciales' =>
                [
                    "Realizar tareas fuera del horario lectivo en el Centro",
                    "Suspender el derecho de participación en actividades extraescolares del Centro",
                    "Suspender el derecho de asistencia a determinadas clases entre 4 y 14 días",
                    "Suspender el derecho de asistencia al centro entre 4 y 30 días",
                    "Cambio de centro docente",
                ],
            'Otras medidas' =>
                [
                    "Mediación escolar",
                    "Aula de convivencia",
                    "Tutoría compartida",
                    "Compromiso de convivencia",
                    "Otras incluidas en el plan de convivencia"
                ]
        ];

        self::createItemsCategoria($manager, $medidas, $niveles, 'CategoriaMedida', 'TipoMedida');
    }

    /**
     * @param ObjectManager $manager
     * @param Array $items
     * @param string $clase
     */
    public function createItems(ObjectManager $manager, $items, $clase)
    {
        $ref = '\\AppBundle\\Entity\\' . $clase;
        foreach($items as $descripcion) {
            $estado = new $ref;
            $estado->setDescripcion($descripcion);
            $manager->persist($estado);
        }
    }

    public function generateCategoriasAviso(ObjectManager $manager)
    {
        $categorias = [
            "Llamada telefónica",
            "Mensaje en el contestador",
            "SMS",
            "Correo electrónico",
            "Otro (detallar)"
        ];

        self::createItems($manager, $categorias, 'CategoriaAviso');
    }

    public function generateActitudFamilia(ObjectManager $manager)
    {
        $actitudes = [
            "Colabora",
            "No colabora",
            "Impide la corrección"
        ];

        self::createItems($manager, $actitudes, 'ActitudFamiliaSancion');
    }

    public function generateEstadoSancion(ObjectManager $manager)
    {
        $estados = [
            "Se aplica correción/medida disciplinaria",
            "No se aplica correción/medida disciplinaria",
            "Pendiente de sanción"
        ];

        self::createItems($manager, $estados, 'EstadoSancion');
    }

    public function getOrder()
    {
        return 0;
    }

    public function load(ObjectManager $manager)
    {
        self::generateTramoParte($manager);
        self::generateActitudFamilia($manager);
        self::generateEstadoSancion($manager);
        self::generateCategoriasAviso($manager);
        self::generateTiposConducta($manager);
        self::generateTiposMedida($manager);

        $manager->flush();
    }
}