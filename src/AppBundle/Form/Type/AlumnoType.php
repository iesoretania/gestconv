<?php

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
            ->add('nie', 'integer', [
                'label' => 'NIE*',
                'required' => true,
                'disabled' => !$options['admin']
            ])
            ->add('nombre', null, [
                'label' => 'Nombre*',
                'required' => true,
                'disabled' => !$options['admin']
            ])
            ->add('apellido1', null, [
                'label' => 'Primer apellido*',
                'required' => true,
                'disabled' => !$options['admin']
            ])
            ->add('apellido2', null, [
                'label' => 'Segundo apellido*',
                'required' => false,
                'disabled' => !$options['admin']
            ])
            ->add('grupo', null, [
                'label' => 'Grupo al que pertenece*',
                'required' => true,
                'disabled' => !$options['admin']
            ])
            ->add('fechaNacimiento', 'date', [
                'label' => 'Fecha de nacimiento*',
                'widget' => 'single_text',
                'required' => true,
                'disabled' => !$options['admin']
            ])
            ->add('tutor1', null, [
                'label' => 'Nombre completo del primer tutor',
                'required' => false,
                'disabled' => $options['bloqueado']
            ])
            ->add('tutor2', null, [
                'label' => 'Nombre completo del segundo tutor',
                'required' => false,
                'disabled' => $options['bloqueado']
            ])
            ->add('telefono1', null, [
                'label' => 'Teléfono de contacto 1',
                'required' => false
            ])
            ->add('notaTelefono1', null, [
                'label' => 'Notas del teléfono de contacto 1',
                'required' => false
            ])
            ->add('telefono2', null, [
                'label' => 'Teléfono de contacto 2',
                'required' => false
            ])
            ->add('notaTelefono2', null, [
                'label' => 'Notas del teléfono de contacto 2',
                'required' => false
            ])
            ->add('enviar', 'submit', [
                'label' => 'Grabar cambios en los datos',
                'attr' => ['class' => 'btn btn-success']
            ]);
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
