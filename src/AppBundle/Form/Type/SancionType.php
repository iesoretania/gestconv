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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SancionType extends BaseSancionType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaSancion', 'datetime', [
                'label' => 'Sanción creada el*',
                'required' => true
            ])
            ->add('fechaComunicado', 'date', [
                'label' => 'Sanción comunicada',
                'widget' => 'single_text',
                'required' => false,
                'disabled' => true
            ])
            ->add('fechaRegistro', 'date', [
                'label' => 'Fecha del registro de salida',
                'widget' => 'single_text',
                'required' => false
            ]);

        parent::buildForm($builder, $options);

        $builder
            ->add('medidasEfectivas', 'checkbox', [
                'label' => 'Las medidas han sido efectivas',
                'required' => false
            ])
            ->add('reclamacion', 'checkbox', [
                'label' => 'La familia ha presentado una reclamación a la sanción',
                'required' => false
            ])
            ->add('actitudFamilia', null, [
                'label' => 'Actitud de la familia ante la sanción',
                'placeholder' => 'Desconocida o no aplicable',
                'required' => false
            ])
            ->add('enviar', 'submit', [
                'label' => 'Grabar cambios de la sanción',
                'attr' => ['class' => 'btn btn-success']
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Sancion',
            'admin' => false,
            'bloqueado' => false,
            'validation_groups' => function(FormInterface $form) {
                if ($form->get('sinSancion')->getData() === true)
                    return ['Default', 'sin_sancion'];
                else
                    return ['Default'];
            }
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_sancion';
    }
}
