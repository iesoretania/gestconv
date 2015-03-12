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

use AppBundle\Entity\Usuario;
use AppBundle\Form\Model\Importar;
use AppBundle\Form\Type\ImportarType;
use AppBundle\Form\Type\RangoFechasType;
use AppBundle\Form\Type\UsuarioType;
use AppBundle\Utils\CsvImporter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/usuario")
 */
class UsuarioController extends Controller
{
    /**
     * @Route("/modificar", name="usuario_modificar",methods={"GET", "POST"})
     */
    public function modificarPropioAction()
    {
        $usuario = $this->getUser();
        return $this->forward('AppBundle:Usuario:modificar', ['usuario' => $usuario->getId()]);
    }

    /**
     * @Route("/modificar/{usuario}", name="usuario_modificar_otro",methods={"GET", "POST"})
     */
    public function modificarAction(Usuario $usuario, Request $peticion)
    {
        $usuarioActivo = $this->getUser();
        if ($usuario->getId() !== $usuarioActivo->getId() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->createAccessDeniedException();
        }
        $formulario = $this->createForm(new UsuarioType(), $usuario, [
            'admin' => $this->isGranted('ROLE_ADMIN'),
            'propio' => ($usuarioActivo->getId() === $usuario->getId())
        ]);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // Guardar el usuario en la base de datos

            // Si es solicitado, cambiar la contraseña
            $passwordSubmit = $formulario->get('cambiarPassword');
            if (($passwordSubmit instanceof SubmitButton) && $passwordSubmit->isClicked()) {
                $password = $this->container->get('security.password_encoder')
                    ->encodePassword($usuario, $formulario->get('newPassword')->get('first')->getData());
                $usuario->setPassword($password);
                $this->addFlash('success', 'Datos guardados correctamente y contraseña cambiada');
            }
            else {
                $this->addFlash('success', 'Datos guardados correctamente');
            }
            $this->getDoctrine()->getManager()->flush();

            return new RedirectResponse(
                $this->generateUrl('usuario_listar')
            );
        }

        return $this->render('AppBundle:Usuario:modificar.html.twig',
            [
                'usuario' => $usuario,
                'formulario' => $formulario->createView()
            ]);
    }

    /**
     * @Route("/nuevo", name="usuario_nuevo",methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function nuevoAction(Request $peticion)
    {
        $usuario = new Usuario();
        $usuario
            ->setEstaActivo(true)
            ->setEstaBloqueado(false);

        $formulario = $this->createForm(new UsuarioType(), $usuario, [
            'admin' => true,
            'propio' => false,
            'nuevo' => true
        ]);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // Guardar el usuario en la base de datos
            $em = $this->getDoctrine()->getManager();

            $encoder = $this->container->get('security.password_encoder');
            $password = $encoder->encodePassword($usuario, $formulario->get('newPassword')->get('first')->getData());
            $usuario->setPassword($password);

            $em->persist($usuario);
            $em->flush();

            $this->addFlash('success', 'Usuario creado correctamente');

            // redireccionar a la portada
            return new RedirectResponse(
                $this->generateUrl('usuario_listar')
            );
        }

        return $this->render('AppBundle:Usuario:modificar.html.twig',
            [
                'usuario' => $usuario,
                'formulario' => $formulario->createView()
            ]);
    }

    /**
     * @Route("/listar", name="usuario_listar",methods={"GET", "POST"})
     * @Security("has_role('ROLE_REVISOR')")
     */
    public function listarAction(Request $request)
    {
        $usuario = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $fechasPorDefecto = ['desde' => null, 'hasta' => null];

        $form = $this->createForm(new RangoFechasType(), $fechasPorDefecto);
        $form->handleRequest($request);

        $usuarios = $em->getRepository('AppBundle:Usuario')->getResumenConvivencia($form->isValid() ? $form->getData() : $fechasPorDefecto);

        return $this->render('AppBundle:Usuario:listar.html.twig',
            [
                'formulario_fechas' => $form->createView(),
                'items' => $usuarios,
                'usuario' => $usuario
            ]);
    }

    protected function importarUsuariosDesdeCsv($fichero)
    {
        $em = $this->getDoctrine()->getManager();

        $importer = new CsvImporter($fichero, true);
        $encoder = $this->container->get('security.password_encoder');

        while($data = $importer->get(100)) {
            foreach($data as $usuarioData) {

                $usuario = $em->getRepository('AppBundle:Usuario')
                    ->findOneByNombreUsuario($usuarioData['Usuario IdEA']);
                if (!$usuario) {

                    $usuario = new Usuario();

                    $completo = explode(', ', $usuarioData['Empleado/a']);

                    $usuario->setNombreUsuario($usuarioData['Usuario IdEA'])
                        ->setApellidos($completo[0])
                        ->setNombre($completo[1])
                        ->setPassword($encoder->encodePassword($usuario, $usuarioData['Usuario IdEA']))
                        ->setNotificaciones(false)
                        ->setEsAdministrador(false)
                        ->setEsRevisor(false)
                        ->setEsDirectivo(false)
                        ->setEstaBloqueado(false)
                        ->setEstaActivo(true);

                    $em->persist($usuario);
                }

            }
        }
        $em->flush();
        return true;
    }

    /**
     * @Route("/importar", name="usuario_importar",methods={"GET", "POST"})
     * @Security("has_role('ROLE_DIRECTIVO')")
     */
    public function importarAction(Request $request)
    {
        $datos = new Importar();
        $form = $this->createForm(new ImportarType(), $datos);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->importarUsuariosDesdeCsv($datos->getFichero()->getPathname())) {
                $this->addFlash('success', 'Los datos se han importado correctamente');
            }
            else {
                $this->addFlash('error', 'Ha ocurrido un error en la importación');
            }

            return new RedirectResponse(
                $this->generateUrl('usuario_listar')
            );
        }

        return $this->render('AppBundle:Usuario:importar.html.twig',
            [
                'formulario' => $form->createView()
            ]);
    }
}
