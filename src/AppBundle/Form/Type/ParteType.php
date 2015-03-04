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
            ->add('usuario', null, [
                'label' => 'Docente u ordenanza*',
                'disabled' => (!$options['admin'])
            ])
            ->add('fechaSuceso', null, [
                'label' => 'Fecha y hora del suceso*',
                'required'  => true,
                'disabled' => $desactivado
            ])
            ->add('fechaCreacion', null, [
                'label' => 'Fecha y hora del registro*',
                'required'  => true,
                'disabled' => (!$options['admin'])
            ])
            ->add('tramo', null, [
                'label' => 'Dónde ha sucedido*',
                'required'  => true,
                'disabled' => ($desactivado)
            ])
            ->add('alumno', 'entity', [
                'label' => 'Alumnado implicado*',
                'class' => 'AppBundle\Entity\Alumno',
                'required'  => false,
                'disabled' => true
            ])
            ->add('conductas', null, [
                'label' => 'Conductas que provocan el parte*',
                'required' => true,
                'expanded' => true,
                'disabled' => ($desactivado)
            ])
            ->add('anotacion', 'textarea', [
                'label' => 'Detalle de lo acontecido*',
                'required' => true,
                'attr' => ['rows' => '8'],
                'disabled' => ($desactivado)
            ])
            ->add('prescrito', null, [
                'label' => 'El parte ha prescrito',
                'required' => false,
                'disabled' => (!$options['admin'])
            ])
            ->add('hayExpulsion', null, [
                'label' => 'Marcar si se expulsó al alumnado implicado del aula',
                'required' => false,
                'disabled' => ($desactivado)
            ])
            ->add('actividades', 'textarea', [
                'label' => 'Actividades a realizar por el alumnado expulsado del aula',
                'required' => false,
                'attr' => ['rows' => '5'],
                'disabled' => ($desactivado)
            ])
            ->add('actividadesRealizadas', 'choice', [
                'label' => '¿Se realizaron las actividades durante la expulsión?',
                'choices' => [null => 'No se sabe', true => 'Sí', false => 'No'],
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ]);

        if (false === $desactivado) {
            $builder
                ->add('enviar', 'submit', [
                    'label' => 'Modificar parte',
                    'attr' => ['class' => 'btn btn-success']
                ]);
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Parte',
            'admin' => false,
            'bloqueado' => false,
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getData();
                if ($data->getHayExpulsion() === true)
                    return ['Default', 'expulsion'];
                else
                    return ['Default'];
            }
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_parte';
    }
}
