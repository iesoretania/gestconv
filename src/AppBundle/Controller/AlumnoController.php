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

namespace AppBundle\Controller;

use AppBundle\Entity\Alumno;
use AppBundle\Entity\Curso;
use AppBundle\Entity\Grupo;
use AppBundle\Form\Model\Importar;
use AppBundle\Form\Type\AlumnoType;
use AppBundle\Form\Type\ImportarType;
use AppBundle\Form\Type\RangoFechasType;
use AppBundle\Utils\CsvImporter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/alumno")
 */

class AlumnoController extends Controller
{
    /**
     * @Route("/tutoria", name="alumno_tutoria",methods={"GET"})
     * @Route("/todo", name="alumno_listar_todo",methods={"GET"})
     * @Security("has_role('ROLE_DIRECTIVO') or (has_role('ROLE_TUTOR') and request.get('_route') == 'alumno_tutoria')")
     */
    public function listarAction(Request $request)
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $fechasPorDefecto = ['desde' => null, 'hasta' => null];

        $form = $this->createForm(new RangoFechasType(), $fechasPorDefecto);
        $form->handleRequest($request);

        $tutor = ($request->get('_route') == 'alumno_tutoria') ? $usuario : null;

        $alumnado = $em->getRepository('AppBundle:Alumno')->getResumenConvivencia($tutor, $form->isValid() ? $form->getData() : $fechasPorDefecto);

        return $this->render(($request->get('_route') == 'alumno_tutoria')
                ? 'AppBundle:Alumno:tutoria.html.twig'
                : 'AppBundle:Alumno:listar.html.twig',
            [
                'formulario_fechas' => $form->createView(),
                'items' => $alumnado,
                'usuario' => $usuario
            ]);
    }

    /**
     * @Route("/detalle/tutoria/{alumno}", name="alumno_tutoria_detalle", methods={"GET", "POST"})
     * @Route("/detalle/{alumno}", name="alumno_detalle", methods={"GET", "POST"})
     * @Security("has_role('ROLE_DIRECTIVO') or (has_role('ROLE_TUTOR') and request.get('_route') == 'alumno_tutoria_detalle')")
     */
    public function detalleAction(Alumno $alumno, Request $request)
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $formularioAlumno = $this->createForm(new AlumnoType(), $alumno, [
            'admin' => $this->isGranted('ROLE_DIRECTIVO'),
            'bloqueado' => ($alumno->getGrupo() != $usuario->getTutoria())
        ]);

        $formularioAlumno->handleRequest($request);

        $vuelta = ($request->get('_route') == 'alumno_tutoria_detalle')
            ? [
                'ruta' => 'alumno_tutoria',
                'descripcion' => 'Gestionar tutoría',
                'boton' => 'Volver al listado del grupo'
            ]
            : [
                'ruta' => 'alumno_listar_todo',
                'descripcion' => 'Gestionar alumnado',
                'boton' => 'Volver al listado de alumnado'
            ];

        if ($formularioAlumno->isSubmitted() && $formularioAlumno->isValid()) {
            $em->persist($alumno);
            $em->flush();
            $this->addFlash('success', 'Los cambios han sido registrados correctamente');
            return new RedirectResponse(
                $this->generateUrl($vuelta['ruta'])
            );
        }

        return $this->render('AppBundle:Alumno:detalle.html.twig',
            [
                'vuelta' => $vuelta,
                'alumno' => $alumno,
                'formulario_alumno' => $formularioAlumno->createView(),
                'usuario' => $usuario
            ]);
    }

    protected function importarAlumnadoDesdeCsv($fichero)
    {
        $em = $this->getDoctrine()->getManager();

        $importer = new CsvImporter($fichero, true);
        $curso = $em->getRepository('AppBundle:Curso')->createQueryBuilder('c')
            ->select('c')->setMaxResults(1)->getQuery()
            ->getOneOrNullResult();

        if (!$curso) {
            $curso = new Curso();
            $curso->setDescripcion('Importado');
            $em->persist($curso);
        }

        $grupos = [];

        while($data = $importer->get(100)) {
            foreach($data as $alumnoData) {

                if ($alumnoData['Unidad']) {
                    $alumno = $em->getRepository('AppBundle:Alumno')
                        ->findOneByNie($alumnoData['Nº Id. Escolar']);
                    if (!$alumno) {
                        $alumno = new Alumno();
                    }

                    if (!isset($grupos[$alumnoData['Unidad']])) {
                        $grupo = $em->getRepository('AppBundle:Grupo')
                            ->findOneByDescripcion($alumnoData['Unidad']);

                        if (!$grupo) {
                            $grupo = new Grupo;
                            $grupo->setDescripcion($alumnoData['Unidad'])->setCurso($curso);
                            $em->persist($grupo);
                        }

                        $grupos[$alumnoData['Unidad']] = $grupo;
                    }
                    else {
                        $grupo = $grupos[$alumnoData['Unidad']];
                    }

                    $alumno->setNie($alumnoData['Nº Id. Escolar'])
                        ->setApellido1($alumnoData['Primer apellido'])
                        ->setApellido2($alumnoData['Segundo apellido'])
                        ->setNombre($alumnoData['Nombre'])
                        ->setFechaNacimiento(\DateTime::createFromFormat('d/m/Y', $alumnoData['Fecha de nacimiento']))
                        ->setTutor1($alumnoData['Nombre Primer tutor'] . ' ' . $alumnoData['Primer apellido Primer tutor'] . ' ' . $alumnoData['Segundo apellido Primer tutor'])
                        ->setTutor2($alumnoData['Nombre Segundo tutor'] . ' ' . $alumnoData['Primer apellido Segundo tutor'] . ' ' . $alumnoData['Segundo apellido Segundo tutor'])
                        ->setTelefono1($alumnoData['Teléfono'])
                        ->setTelefono2($alumnoData['Teléfono de urgencia'])
                        ->setGrupo($grupo);

                    $em->persist($alumno);
                }
            }
        }
        $em->flush();
        return true;
    }

    /**
     * @Route("/importar", name="alumno_importar",methods={"GET", "POST"})
     * @Security("has_role('ROLE_DIRECTIVO')")
     */
    public function importarAction(Request $request)
    {
        $datos = new Importar();
        $form = $this->createForm(new ImportarType(), $datos);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->importarAlumnadoDesdeCsv($datos->getFichero()->getPathname())) {
                $this->addFlash('success', 'Los datos se han importado correctamente');
            }
            else {
                $this->addFlash('error', 'Ha ocurrido un error en la importación');
            }

            return new RedirectResponse(
                $this->generateUrl('alumno_listar_todo')
            );
        }

        return $this->render('AppBundle:Alumno:importar.html.twig',
            [
                'formulario' => $form->createView()
            ]);
    }
}
