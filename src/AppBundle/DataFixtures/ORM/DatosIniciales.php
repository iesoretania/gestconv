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

    public function generateTiposConducta(ObjectManager $manager)
    {
        $conductas = [
            'Conductas contrarias [Decreto 327/2010 (art.34) o Decreto 328/2010 (art.33)]' =>
                [
                    "Perturbación del normal desarrollo de las actividades de clase",
                    "Falta de colaboración sistemática en la realización de las actividades",
                    "Impedir o dificultar el estudio a sus compañeros",
                    "Faltas injustificadas de puntualidad",
                    "Faltas injustificadas de asistencia a clase",
                    "Actuaciones incorrectas hacia algún miembro de la comunidad educativa",
                    "Daños en instalaciones o docum. del Centro o en pertenencias de un miembro"
                ],
            'Conductas graves [Decreto 327/2010 (art.37) o Decreto 328/2010 (art.36)]' =>
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
            'Otras conductas contrarias no incluidas en el Decreto 327/2010 o en el Decreto 328/2010' =>
                [
                    'Describirlas con detalle'
                ]
        ];

        foreach($conductas as $descripcionCategoria => $tipos) {
            $categoria = new CategoriaConducta();
            $categoria->setDescripcion($descripcionCategoria);
            $manager->persist($categoria);

            foreach($tipos as $descripcionTipo) {
                $tipo = new TipoConducta();
                $tipo->setDescripcion($descripcionTipo);
                $tipo->setCategoria($categoria);
                $manager->persist($tipo);
            }
        }
    }

    public function generateTiposMedida(ObjectManager $manager)
    {
        $medidas = [
            'Correciones a las conductas contrarias [Decreto 327/2010 (art.35) o Decreto 328/2010 (art.34)]' =>
                [
                    "Amonestación oral",
                    "Apercibimiento por escrito",
                    "Realizar tareas dentro y fuera del horario lectivo en el Centro",
                    "Suspender el derecho de asistencia a determinadas clases entre 1 y 3 días",
                    "Suspender el derecho de asistencia al centro entre 1 y 3 días",

                ],
            'Medidas disciplinarias por conductas gravemente perjudiciales [Decreto 327/2010 (art.38) o Decreto 328/2010 (art.37)]' =>
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

        foreach($medidas as $descripcionCategoria => $tipos) {
            $categoria = new CategoriaMedida();
            $categoria->setDescripcion($descripcionCategoria);
            $manager->persist($categoria);

            foreach($tipos as $descripcionTipo) {
                $tipo = new TipoMedida();
                $tipo->setDescripcion($descripcionTipo);
                $tipo->setCategoria($categoria);
                $manager->persist($tipo);
            }
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

        foreach($categorias as $descripcion) {
            $aviso = new CategoriaAviso();
            $aviso->setDescripcion($descripcion);
            $manager->persist($aviso);
        }
    }

    public function generateActitudFamilia(ObjectManager $manager)
    {
        $actitudes = [
            "Colabora",
            "No colabora",
            "Impide la corrección"
        ];

        foreach($actitudes as $descripcion) {
            $actitud = new ActitudFamiliaSancion();
            $actitud->setDescripcion($descripcion);
            $manager->persist($actitud);
        }
    }

    public function generateEstadoSancion(ObjectManager $manager)
    {
        $estados = [
            "Se aplica correción/medida disciplinaria",
            "No se aplica correción/medida disciplinaria",
            "Pendiente de sanción"
        ];

        foreach($estados as $descripcion) {
            $estado = new EstadoSancion();
            $estado->setDescripcion($descripcion);
            $manager->persist($estado);
        }
    }

    public function getOrder()
    {
        return 10;
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