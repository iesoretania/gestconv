<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;

class NuevoParteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['admin']) {
            $builder
                ->add('usuario', null, [
                    'label' => 'Docente u ordenanza*'
                ]);
        }

        $builder
            ->add('fechaSuceso', null, [
                'label' => 'Fecha y hora del suceso*',
                'required'  => true
            ])
            ->add('tramo', null, [
                'label' => 'Dónde ha sucedido*',
                'required'  => true
            ])
            ->add('alumnos', null, [
                'label' => 'Alumnado implicado*',
                'attr' => ['data-placeholder' => 'Escriba parte del nombre o el grupo al que pertenece'],
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.apellido1', 'ASC')
                        ->addOrderBy('u.apellido2', 'ASC')
                        ->addOrderBy('u.nombre', 'ASC');
                },
                'required'  => false
            ])
            ->add('conductas', null, [
                'label' => 'Conductas que provocan el parte*',
                'required' => true,
                'expanded' => true
            ])
            ->add('anotacion', 'textarea', [
                'label' => 'Detalle de lo acontecido*',
                'required' => true,
                'attr' => ['rows' => '8']

            ])
            ->add('hayExpulsion', null, [
                'label' => 'Marcar si se expulsó al alumnado implicado del aula',
                'required' => false
            ])
            ->add('actividades', 'textarea', [
                'label' => 'Actividades a realizar por el alumnado expulsado del aula',
                'required' => false,
                'attr' => ['rows' => '5']
            ])
            ->add('profesorGuardia', null, [
                'label' => 'Profesor de guardia que atendió al alumnado',
                'required' => false,
                'placeholder' => 'No aplicable'
            ])
            ->add('enviar', 'submit', [
                'label' => 'Crear parte',
                'attr' => ['class' => 'btn btn-success']
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Parte',
            'admin' => false,
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
        return 'appbundle_nuevoparte';
    }
}
