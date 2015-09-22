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

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class UsuarioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreUsuario', null, array(
                'label' => 'Nombre de usuario*',
                'disabled' => !$options['admin']
            ))
            ->add('nombre', null, array(
                'label' => 'Nombre*',
                'required'  => true
            ))
            ->add('apellidos', null, array(
                'label' => 'Apellidos*',
                'required'  => true
            ))
            ->add('email', 'email', array(
                'label' => 'Correo electrónico',
                'required' => false
            ))
            ->add('notificaciones', null, array(
                'label' => 'Marcar si se desea recibir notificaciones',
                'required' => false
            ));

        if ($options['directivo']) {
            $builder
                ->add('tutoria', null, array(
                    'label' => 'Tutor/a del grupo',
                    'required' => false
                ));
        }

        if ($options['admin']) {
            $builder
                ->add('estaActivo', null, array(
                    'label' => 'El usuario está activo',
                    'required' => false
                ))
                ->add('estaBloqueado', null, array(
                    'label' => 'El usuario está bloqueado',
                    'required' => false
                ))
                ->add('esAdministrador', null, array(
                    'label' => 'Es administrador',
                    'required' => false,
                    'disabled' => $options['propio']
                ))
                ->add('esRevisor', null, array(
                    'label' => 'Pertenece a la comisión de convivencia',
                    'required' => false
                ))
                ->add('esDirectivo', null, array(
                    'label' => 'Pertenece al equipo directivo',
                    'required' => false
                ));
        }

        if (!$options['nuevo']) {
            $builder
                ->add('enviar', 'submit', array(
                    'label' => 'Guardar cambios',
                    'attr' => array('class' => 'btn btn-success')
                ));

            if (!$options['directivo']) {
                $builder
                    ->add('oldPassword', 'password', array(
                        'label' => 'Contraseña antigua',
                        'required' => false,
                        'mapped' => false,
                        'constraints' => new UserPassword(array(
                            'groups' => array('password')
                        ))
                    ));
            }
        }

        $builder
            ->add('newPassword', 'repeated', array(
                'label' => 'Correo electrónico',
                'required' => false,
                'type' => 'password',
                'mapped' => false,
                'invalid_message' => 'password.no_match',
                'first_options' => array(
                    'label' => 'Nueva contraseña',
                    'constraints' => array(
                        new Length(array(
                            'min' => 7,
                            'minMessage' => 'password.min_length',
                            'groups' => array('password')
                        )),
                        new NotNull(array(
                            'groups' => array('password')
                        ))
                    )
                ),
                'second_options' => array(
                    'label' => 'Repita nueva contraseña'
                )
            ))
            ->add('cambiarPassword', 'submit', array(
                'label' => 'Guardar los cambios y cambiar la contraseña',
                'attr' => array('class' => 'btn btn-success'),
                'validation_groups' => array('Default', 'password')
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Usuario',
            'cascade_validation' => true,
            'admin' => false,
            'directivo' => false,
            'propio' => false,
            'nuevo' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_usuario';
    }
}
