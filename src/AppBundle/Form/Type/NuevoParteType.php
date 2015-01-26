<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NuevoParteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usuario', null, [
                'label' => 'Docente'
            ])
            ->add('fechaCreacion', null, [
                'label' => 'Parte creado'
            ])
            ->add('fechaSuceso', null, [
                'label' => 'Fecha y hora del suceso',
                'required'  => true
            ])

            ->add('tramo', null, [
                'label' => 'Dónde ha sucedido',
                'required'  => true
            ])
            ->add('alumnos', null, [
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.apellido1', 'ASC')
                        ->addOrderBy('u.apellido2', 'ASC')
                        ->addOrderBy('u.nombre', 'ASC');
                },
                'label' => 'Alumnado implicado',
                'required'  => true
            ])
            ->add('anotacion', 'textarea', [
                'label' => 'Detalle de lo acontecido',
                'required' => false,
                'attr' => ['rows' => '8']

            ])
            ->add('actividades', 'textarea', [
                'label' => 'Actividades a realizar',
                'required' => false,
                'attr' => ['rows' => '5']
            ])
            ->add('fechaAviso', null, [
                'label' => 'Fecha de aviso a las familias'
            ])
            ->add('prescrito', null, [
                'required' => false,
                'label' => '¿Ha prescrito?'
            ])
            ->add('profesorGuardia', null, [
                'label' => 'Profesor de guardia'
            ])
            ->add('enviar', 'submit', [
                'label' => 'Crear parte'
            ]);
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Parte',
            'validate_groups' => ['nuevo']
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_nuevoparte';
    }
}
