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

use AppBundle\Entity\Curso;
use AppBundle\Form\Type\CursoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/curso")
 */

class CursoController extends Controller
{
    /**
     * @Route("/modificar/{curso}", name="curso_modificar",methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function modificarAction(Curso $curso, Request $peticion)
    {
        $formulario = $this->createForm(new CursoType(), $curso);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // Guardar el usuario en la base de datos
            $em = $this->getDoctrine()->getManager();

            $em->flush();

            $this->addFlash('success', 'Datos guardados correctamente');

            return new RedirectResponse(
                $this->generateUrl('grupo_listar')
            );
        }

        return $this->render('AppBundle:Curso:modificar.html.twig',
            [
                'formulario' => $formulario->createView(),
                'curso' => $curso
            ]);
    }

    /**
     * @Route("/nuevo", name="curso_nuevo",methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function nuevoAction(Request $peticion)
    {
        $curso = new Curso();

        $formulario = $this->createForm(new CursoType(), $curso);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // Guardar el usuario en la base de datos
            $em = $this->getDoctrine()->getManager();
            $em->persist($curso);
            $em->flush();

            $this->addFlash('success', 'Curso creado correctamente');

            return new RedirectResponse(
                $this->generateUrl('grupo_listar')
            );
        }

        return $this->render('AppBundle:Curso:modificar.html.twig',
            [
                'formulario' => $formulario->createView(),
                'curso' => $curso
            ]);
    }
}
