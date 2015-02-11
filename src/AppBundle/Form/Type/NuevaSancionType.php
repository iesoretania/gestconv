<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;

class NuevaSancionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('partes', 'entity', [
                'label' => 'Partes sancionados*',
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
            ])
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
            ->add('motivosNoAplicacion', 'textarea', [
                'label' => 'Motivos de la no aplicación de sanción',
                'attr' => ['rows' => '8'],
                'required' => false
            ])
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
            'alumno' => null
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
