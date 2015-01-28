<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;

class ModificarUsuarioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreUsuario', null, [
                'label' => 'Nombre de usuario',
                'disabled' => !$options['admin']
            ])
            ->add('nombre', null, [
                'label' => 'Nombre*',
                'required'  => true
            ])
            ->add('apellido1', null, [
                'label' => 'Primer apellido*',
                'required'  => true
            ])
            ->add('apellido2', null, [
                'label' => 'Segundo apellido',
                'required'  => false
            ])
            ->add('email', 'email', [
                'label' => 'Correo electrónico',
                'required' => false

            ]);

        if ($options['admin']) {
            $builder
                ->add('esAdministrador', null, [
                    'label' => 'Es administrador',
                    'required' => false
                ])
                ->add('esRevisor', null, [
                    'label' => 'Pertenece a la comisión de convivencia',
                    'required' => false
                ]);
        }

        $builder
            ->add('enviar', 'submit', [
                'label' => 'Guardar cambios',
                'attr' => ['class' => 'btn btn-success']
            ]);

        if (!$options['admin']) {
            $builder
                ->add('oldPassword', null, [
                    'label' => 'Contraseña antigua',
                    'required' => false,
                    'mapped' => false,
                    'constraints' => new UserPassword([
                        'message' => 'password.bad',
                        'groups' => 'password'
                    ])
                ]);
        }

        $builder
            ->add('newPassword', 'repeated', [
                'first_options' => ['label' => 'Contraseña nueva'],
                'second_options' => ['label' => 'Repita contraseña nueva'],
                'mapped' => false,
                'required'  => false,
                'validation_groups' => 'password',
                'invalid_message' => 'password.no_match',
                'constraints' => new Length([
                    'min' => 7,
                    'minMessage' => 'password.min_length',
                    'groups' => 'password'
                ])
            ])
            ->add('cambiar_password', 'submit', [
                'label' => 'Guardar los cambios y cambiar la contraseña',
                'attr' => ['class' => 'btn btn-success']
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Usuario',
            'admin' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_modificarusuario';
    }
}
