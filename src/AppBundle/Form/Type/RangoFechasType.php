<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RangoFechasType extends BaseSancionType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('desde', 'date', [
                'label' => 'Desde',
                'required' => false,
                'widget' => 'single_text'
            ])
            ->add('hasta', 'date', [
                'label' => 'Hasta',
                'required' => false,
                'widget' => 'single_text'
            ])
            ->add('enviar', 'submit', [
                'label' => 'Filtrar partes en el intervalo',
                'attr' => ['class' => 'btn btn-info']
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([

        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_rangofechas';
    }
}
