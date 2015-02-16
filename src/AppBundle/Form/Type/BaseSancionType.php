<?php

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
            ->add('medidas', null, [
                'label' => 'Medidas tomadas*',
                'required' => true,
                'expanded' => true
            ])
            ->add('anotacion', 'textarea', [
                'label' => 'Detalle de la sanción impuesta*',
                'required' => true,
                'attr' => ['rows' => '8']
            ])
            ->add('fechaInicioSancion', 'date', [
                'label' => 'Sanción efectiva desde',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('fechaFinSancion', 'date', [
                'label' => 'Sanción efectiva hasta',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('sinSancion', 'checkbox', [
                'label' => 'No se aplica corrección/medida disciplinaria',
                'mapped' => false,
                'required' => false
            ])
            ->add('motivosNoAplicacion', 'textarea', [
                'label' => 'Motivos de la no aplicación de sanción',
                'attr' => ['rows' => '8'],
                'required' => false
            ]);
    }
}
