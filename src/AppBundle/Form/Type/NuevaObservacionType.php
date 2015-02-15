<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;

class NuevaObservacionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['admin']) {
            $builder
                ->add('usuario', 'entity', [
                    'label' => 'Usuario fuente de la observación*',
                    'class' => 'AppBundle\Entity\Usuario',
                    'required' => true,
                ])
                ->add('fecha', 'datetime', [
                    'label' => 'Fecha de registro de la observacion*',
                    'required' => true
                ]);
        }
        $builder
            ->add('anotacion', 'textarea', [
                'label' => 'Observación a incorporar*',
                'required' => true,
                'attr' => ['rows' => '8']
            ])
            ->add('enviar', 'submit', [
                'label' => 'Registrar observación',
                'attr' => ['class' => 'btn btn-success']
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Observacion',
            'admin' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_nuevaobservacion';
    }
}
