<?php

namespace AT\vocationetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InformacionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('informacionTitulo')
			->add('informacionImagen','file', array('required' => false, 'data_class' => null))
            ->add('informacionLink')
			->add('informacionEstado', 'checkbox', array('required' => false))
            //->add('created')
            //->add('modified')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AT\vocationetBundle\Entity\Informacion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'at_vocationetbundle_informacion';
    }
}
