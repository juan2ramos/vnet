<?php

namespace AT\vocationetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsuariosType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usuarioNombre')
            ->add('usuarioApellido')
            ->add('usuarioEmail')
            ->add('usuarioPassword')
            ->add('usuarioHash')
            ->add('usuarioEstado')
            ->add('usuarioRolEstado')
            ->add('usuarioFacebookid')
            ->add('usuarioFechaNacimiento')
            ->add('usuarioGenero')
            ->add('usuarioImagen')
            ->add('usuarioTarjetaProfesional')
            ->add('usuarioHojaVida')
            ->add('usuarioProfesion')
            ->add('usuarioPuntos')
            ->add('usuarioPerfilProfesional')
            ->add('usuarioValorMentoria')
            ->add('usuarioCursoActual')
            ->add('usuarioFechaPlaneacion')
            ->add('created')
            ->add('modified')
            ->add('syncLinkedin')
            ->add('rol')
            ->add('colegio')
            ->add('georeferencia')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AT\vocationetBundle\Entity\Usuarios'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'at_vocationetbundle_usuarios';
    }
}
