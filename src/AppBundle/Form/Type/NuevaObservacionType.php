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

class NuevaObservacionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['admin']) {
            $builder
                ->add('usuario', 'entity', array(
                    'label' => 'Usuario fuente de la observación*',
                    'class' => 'AppBundle\Entity\Usuario',
                    'required' => true,
                ))
                ->add('fecha', 'datetime', array(
                    'label' => 'Fecha de registro de la observacion*',
                    'required' => true
                ));
        }
        $builder
            ->add('anotacion', 'textarea', array(
                'label' => 'Observación a incorporar*',
                'required' => true,
                'attr' => array('rows' => '8')
            ))
            ->add('enviar', 'submit', array(
                'label' => 'Registrar observación',
                'attr' => array('class' => 'btn btn-success')
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Observacion',
            'admin' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_nuevaobservacion';
    }
}
