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

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NuevaSancionType extends \AppBundle\Form\Type\BaseSancionType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('partes', 'entity', [
                'label' => 'Partes a sancionar*',
                'class' => 'AppBundle\Entity\Parte',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function(EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('p')
                        ->andWhere('p.fechaAviso IS NOT NULL')
                        ->andWhere('p.sancion IS NULL')
                        ->andWhere('p.prescrito = false')
                        ->orderBy('p.fechaSuceso');

                    if ($options['alumno']) {
                        $qb = $qb
                            ->andWhere('p.alumno  = :alumno')
                            ->setParameter('alumno', $options['alumno']);
                    }
                    return $qb;
                },
                'required'  => true
            ]);

        parent::buildForm($builder, $options);

        $builder
            ->add('enviar', 'submit', [
                'label' => 'Grabar sanción',
                'attr' => ['class' => 'btn btn-success']
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Sancion',
            'alumno' => null,
            'validation_groups' => function(FormInterface $form) {
                if ($form->get('sinSancion')->getData() === true)
                    return ['Default', 'sin_sancion'];
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
        return 'appbundle_nuevasancion';
    }
}
