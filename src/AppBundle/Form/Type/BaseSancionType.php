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

abstract class BaseSancionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('medidas', null, array(
                'label' => 'Medidas tomadas*',
                'required' => true,
                'expanded' => true
            ))
            ->add('anotacion', 'textarea', array(
                'label' => 'Detalle de la sanción impuesta*',
                'required' => true,
                'attr' => array('rows' => '8')
            ))
            ->add('fechaInicioSancion', 'date', array(
                'label' => 'Sanción efectiva desde',
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('fechaFinSancion', 'date', array(
                'label' => 'Sanción efectiva hasta',
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('sinSancion', 'checkbox', array(
                'label' => 'No se aplica corrección/medida disciplinaria',
                'mapped' => false,
                'required' => false
            ))
            ->add('motivosNoAplicacion', 'textarea', array(
                'label' => 'Motivos de la no aplicación de sanción',
                'attr' => array('rows' => '8'),
                'required' => false
            ));
    }
}
