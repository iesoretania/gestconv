<?php

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
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Sancion',
            'alumno' => null,
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
        return 'appbundle_nuevasancion';
    }
}
