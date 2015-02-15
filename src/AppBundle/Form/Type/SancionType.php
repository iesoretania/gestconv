<?php

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
