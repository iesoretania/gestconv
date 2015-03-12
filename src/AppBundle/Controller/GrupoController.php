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

use AppBundle\Entity\Grupo;
use AppBundle\Form\Type\GrupoType;
use AppBundle\Form\Type\RangoFechasType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/grupo")
 */

class GrupoController extends Controller
{
    /**
     * @Route("/listar", name="grupo_listar",methods={"GET", "POST"})
     * @Security("has_role('ROLE_DIRECTIVO')")
     */
    public function listarAction(Request $request)
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $fechasPorDefecto = ['desde' => null, 'hasta' => null];

        $form = $this->createForm(new RangoFechasType(), $fechasPorDefecto)->handleRequest($request);

        $fechas = ($form->isValid()) ? $fechasPorDefecto : ['desde' => null, 'hasta' => null];

        $grupos = $em->getRepository('AppBundle:Grupo')->getResumenPorFecha($fechas);

        $cursos = $em->getRepository('AppBundle:Curso')->getResumenPorFecha($fechas);

        return $this->render('AppBundle:Grupo:listar.html.twig',
            [
                'formulario_fechas' => $form->createView(),
                'items' => $grupos,
                'items2' => $cursos,
                'usuario' => $usuario
            ]);
    }

    /**
     * @Route("/modificar/{grupo}", name="grupo_modificar",methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function modificarAction(Grupo $grupo, Request $peticion)
    {
        $formulario = $this->createForm(new GrupoType(), $grupo);

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

        return $this->render('AppBundle:Grupo:modificar.html.twig',
            [
                'formulario' => $formulario->createView(),
                'grupo' => $grupo
            ]);
    }


    /**
     * @Route("/nuevo", name="grupo_nuevo",methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function nuevoAction(Request $peticion)
    {
        $grupo = new Grupo;

        $formulario = $this->createForm(new GrupoType(), $grupo);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // Guardar el grupo en la base de datos
            $em = $this->getDoctrine()->getManager();
            $em->persist($grupo);
            $em->flush();

            $this->addFlash('success', 'Grupo creado correctamente');

            return new RedirectResponse(
                $this->generateUrl('grupo_listar')
            );
        }

        return $this->render('AppBundle:Grupo:modificar.html.twig',
            [
                'formulario' => $formulario->createView(),
                'grupo' => $grupo
            ]);
    }
}
