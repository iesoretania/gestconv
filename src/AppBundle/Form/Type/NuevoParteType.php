<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

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
                'attr' => ['rows' => '8']

            ])
            ->add('actividades', 'textarea', [
                'label' => 'Actividades a realizar',
                'attr' => ['rows' => '5']
            ])
            ->add('fechaAviso', null, [
                'label' => 'Fecha de aviso a las familias'
            ])
            ->add('prescrito', null, [
                'label' => '¿Ha prescrito?'
            ])
            ->add('profesorGuardia', null, [
                'label' => 'Profesor de guardia'
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Parte',
            'admin' => false
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
