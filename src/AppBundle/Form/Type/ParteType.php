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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $desactivado = !$options['admin'] && $options['bloqueado'];

        $builder
            ->add('usuario', null, array(
                'label' => 'Docente u ordenanza*',
                'disabled' => (!$options['admin'])
            ))
            ->add('fechaSuceso', null, array(
                'label' => 'Fecha y hora del suceso*',
                'required'  => true,
                'disabled' => $desactivado
            ))
            ->add('fechaCreacion', null, array(
                'label' => 'Fecha y hora del registro*',
                'required'  => true,
                'disabled' => (!$options['admin'])
            ))
            ->add('tramo', null, array(
                'label' => 'Dónde ha sucedido*',
                'required'  => true,
                'disabled' => ($desactivado)
            ))
            ->add('alumno', 'entity', array(
                'label' => 'Alumnado implicado*',
                'class' => 'AppBundle\Entity\Alumno',
                'required'  => false,
                'disabled' => true
            ))
            ->add('conductas', null, array(
                'label' => 'Conductas que provocan el parte*',
                'required' => true,
                'expanded' => true,
                'disabled' => ($desactivado)
            ))
            ->add('anotacion', 'textarea', array(
                'label' => 'Detalle de lo acontecido*',
                'required' => true,
                'attr' => array('rows' => '8'),
                'disabled' => ($desactivado)
            ))
            ->add('prioritario', null, array(
                'label' => 'El parte es prioritario',
                'required' => false
            ))
            ->add('prescrito', null, array(
                'label' => 'El parte ha prescrito',
                'required' => false,
                'disabled' => (!$options['admin'])
            ))
            ->add('hayExpulsion', null, array(
                'label' => 'Marcar si se expulsó al alumnado implicado del aula',
                'required' => false,
                'disabled' => ($desactivado)
            ))
            ->add('actividades', 'textarea', array(
                'label' => 'Actividades a realizar por el alumnado expulsado del aula',
                'required' => false,
                'attr' => array('rows' => '5'),
                'disabled' => ($desactivado)
            ))
            ->add('actividadesRealizadas', 'choice', array(
                'label' => '¿Se realizaron las actividades durante la expulsión?',
                'choices' => array(null => 'No se sabe', true => 'Sí', false => 'No'),
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ));

        if (false === $desactivado) {
            $builder
                ->add('enviar', 'submit', array(
                    'label' => 'Modificar parte',
                    'attr' => array('class' => 'btn btn-success')
                ));
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Parte',
            'admin' => false,
            'bloqueado' => false,
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getData();
                if ($data->getHayExpulsion() === true)
                    return array('Default', 'expulsion');
                else
                    return array('Default');
            }
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_parte';
    }
}
