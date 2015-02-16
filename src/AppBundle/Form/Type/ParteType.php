<?php

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
        $resolver->setDefaults(array(
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
