<?php

namespace AppBundle\Controller;

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
    public function modificarAction(Request $peticion)
    {
        $usuario = $this->get('security.token_storage')->getToken()->getUser();

        $formulario = $this->createForm(new UsuarioType(), $usuario, [
            'admin' => $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'),
            'propio' => true
        ]);

        $formulario->handleRequest($peticion);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            $mensaje = 'Datos guardados correctamente';
            // Guardar el usuario en la base de datos
            $em = $this->getDoctrine()->getManager();

            // Si es solicitado, cambiar la contraseña
            $passwordSubmit = $formulario->get('cambiarPassword');
            if (($passwordSubmit instanceof SubmitButton) && $passwordSubmit->isClicked()) {
                $encoder = $this->container->get('security.password_encoder');
                $password = $encoder->encodePassword($usuario, $formulario->get('newPassword')->get('first')->getData());
                $usuario->setPassword($password);
                $mensaje = 'Datos guardados correctamente y contraseña cambiada';
            }
            $em->flush();

            $this->addFlash('success', $mensaje);

            // redireccionar a la portada
            return new RedirectResponse(
                $this->generateUrl('portada')
            );
        }

        return $this->render('AppBundle:Usuario:modificar.html.twig',
            [
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
            ->addSelect('count(p)')
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
