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

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AlumnoType extends BaseSancionType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nie', 'integer', array(
                'label' => 'NIE*',
                'required' => true,
                'disabled' => !$options['admin']
            ))
            ->add('nombre', null, array(
                'label' => 'Nombre*',
                'required' => true,
                'disabled' => !$options['admin']
            ))
            ->add('apellido1', null, array(
                'label' => 'Primer apellido*',
                'required' => true,
                'disabled' => !$options['admin']
            ))
            ->add('apellido2', null, array(
                'label' => 'Segundo apellido*',
                'required' => false,
                'disabled' => !$options['admin']
            ))
            ->add('grupo', null, array(
                'label' => 'Grupo al que pertenece*',
                'required' => true,
                'disabled' => !$options['admin']
            ))
            ->add('fechaNacimiento', 'date', array(
                'label' => 'Fecha de nacimiento*',
                'widget' => 'single_text',
                'required' => false,
                'disabled' => !$options['admin']
            ))
            ->add('tutor1', null, array(
                'label' => 'Nombre completo del primer tutor',
                'required' => false,
                'disabled' => $options['bloqueado']
            ))
            ->add('tutor2', null, array(
                'label' => 'Nombre completo del segundo tutor',
                'required' => false,
                'disabled' => $options['bloqueado']
            ))
            ->add('telefono1', null, array(
                'label' => 'Teléfono de contacto 1',
                'required' => false
            ))
            ->add('notaTelefono1', null, array(
                'label' => 'Notas del teléfono de contacto 1',
                'required' => false
            ))
            ->add('telefono2', null, array(
                'label' => 'Teléfono de contacto 2',
                'required' => false
            ))
            ->add('notaTelefono2', null, array(
                'label' => 'Notas del teléfono de contacto 2',
                'required' => false
            ))
            ->add('enviar', 'submit', array(
                'label' => 'Grabar cambios en los datos',
                'attr' => array('class' => 'btn btn-success')
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Alumno',
            'admin' => false,
            'bloqueado' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_alumno';
    }
}
