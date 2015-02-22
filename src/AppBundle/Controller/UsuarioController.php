<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Usuario;
use AppBundle\Form\Type\RangoFechasType;
use AppBundle\Form\Type\UsuarioType;
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
    public function modificarPropioAction(Request $peticion)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        return $this->forward('AppBundle:Usuario:modificar', ['usuario' => $usuario->getId()]);
    }

    /**
     * @Route("/modificar/{usuario}", name="usuario_modificar_otro",methods={"GET", "POST"})
     */
    public function modificarAction(Usuario $usuario, Request $peticion)
    {
        $usuarioActivo = $this->get('security.token_storage')->getToken()->getUser();
        if ($usuario->getId() != $usuarioActivo->getId() && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->createAccessDeniedException();
        }
        $formulario = $this->createForm(new UsuarioType(), $usuario, [
            'admin' => $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'),
            'propio' => ($usuarioActivo->getId() == $usuario->getId())
        ]);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            // Guardar el usuario en la base de datos

            // Si es solicitado, cambiar la contraseña
            $passwordSubmit = $formulario->get('cambiarPassword');
            if (($passwordSubmit instanceof SubmitButton) && $passwordSubmit->isClicked()) {
                $encoder = $this->container->get('security.password_encoder');
                $password = $encoder->encodePassword($usuario, $formulario->get('newPassword')->get('first')->getData());
                $usuario->setPassword($password);
                $this->addFlash('success', 'Datos guardados correctamente y contraseña cambiada');
            }
            else {
                $this->addFlash('success', 'Datos guardados correctamente');
            }
            $this->getDoctrine()->getManager()->flush();

            // redireccionar a la portada
            return new RedirectResponse(
                $this->generateUrl($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') ? 'usuario_listar' : 'portada')
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
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $fechasPorDefecto = ['desde' => null, 'hasta' => null];

        $form = $this->createForm(new RangoFechasType(), $fechasPorDefecto);
        $form->handleRequest($request);

        $usuarios = $em->getRepository('AppBundle:Usuario')
            ->createQueryBuilder('u')
            ->leftJoin('AppBundle:Parte', 'p', 'WITH', 'p.usuario = u')
            ->leftJoin('AppBundle:Sancion', 's', 'WITH', 'p.sancion = s')
            ->select('u')
            ->addSelect('count(p.id)')
            ->addSelect('count(p.fechaAviso)')
            ->addSelect('count(p.sancion)')
            ->addSelect('count(s.fechaComunicado)')
            ->addSelect('count(s.motivosNoAplicacion)')
            ->addSelect('count(s.fechaInicioSancion)')
            ->addSelect('sum(p.prescrito)')
            ->addSelect('max(p.fechaSuceso)')
            ->addSelect('max(s.fechaSancion)');

        if ($form->isValid()) {
            // aplicar filtro de fechas
            $data = $form->getData();
            if ($data['desde']) {
                $usuarios = $usuarios
                    ->andWhere('p.fechaSuceso >= :desde')
                    ->setParameter('desde', $data['desde']);
            }
            if ($data['hasta']) {
                $usuarios = $usuarios
                    ->andWhere('p.fechaSuceso <= :hasta')
                    ->setParameter('hasta', $data['hasta']);
            }
        }

        $usuarios = $usuarios
            ->addOrderBy('u.apellido1')
            ->addOrderBy('u.apellido2')
            ->addOrderBy('u.nombre')
            ->groupBy('u.id')
            ->getQuery()
            ->getResult();

        return $this->render('AppBundle:Usuario:listar.html.twig',
            [
                'formulario_fechas' => $form->createView(),
                'items' => $usuarios,
                'usuario' => $usuario
            ]);
    }
}
