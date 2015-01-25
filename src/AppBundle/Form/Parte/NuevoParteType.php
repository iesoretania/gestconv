<?php

namespace AppBundle\Form\Parte;

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
            ->add('usuario')
            ->add('fechaCreacion')
            ->add('fechaSuceso')
            ->add('alumnos', null, array(
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.apellido1', 'ASC')
                        ->addOrderBy('u.apellido2', 'ASC')
                        ->addOrderBy('u.nombre', 'ASC');
                }))
            ->add('anotacion')
            ->add('actividades')
            ->add('fechaAviso')
            ->add('prescrito')
            ->add('profesorGuardia')
            ->add('tramo')
            ->add('sancion')
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
