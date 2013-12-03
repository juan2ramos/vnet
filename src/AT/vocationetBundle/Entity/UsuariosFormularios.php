<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsuariosFormularios
 *
 * @ORM\Table(name="usuarios_formularios")
 * @ORM\Entity
 */
class UsuariosFormularios
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="correo_invitacion", type="string", length=100, nullable=true)
     */
    private $correoInvitacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estado", type="boolean", nullable=false)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_evaluado_id", type="integer", nullable=false)
     */
    private $usuarioEvaluado;

    /**
     * @var integer
     *
     * @ORM\Column(name="formulario_id", type="integer", nullable=false)
     */
    private $formulario;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_responde_id", type="integer", nullable=false)
     */
    private $usuarioResponde;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set correoInvitacion
     *
     * @param string $correoInvitacion
     * @return UsuariosFormularios
     */
    public function setCorreoInvitacion($correoInvitacion)
    {
        $this->correoInvitacion = $correoInvitacion;
    
        return $this;
    }

    /**
     * Get correoInvitacion
     *
     * @return string 
     */
    public function getCorreoInvitacion()
    {
        return $this->correoInvitacion;
    }

    /**
     * Set estado
     *
     * @param boolean $estado
     * @return UsuariosFormularios
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
        return $this;
    }

    /**
     * Get estado
     *
     * @return boolean 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set usuarioEvaluado
     *
     * @param integer $usuarioEvaluado
     * @return UsuariosFormularios
     */
    public function setUsuarioEvaluado($usuarioEvaluado = null)
    {
        $this->usuarioEvaluado = $usuarioEvaluado;
    
        return $this;
    }

    /**
     * Get usuarioEvaluado
     *
     * @return integer 
     */
    public function getUsuarioEvaluado()
    {
        return $this->usuarioEvaluado;
    }

    /**
     * Set formulario
     *
     * @param integer $formulario
     * @return UsuariosFormularios
     */
    public function setFormulario($formulario = null)
    {
        $this->formulario = $formulario;
    
        return $this;
    }

    /**
     * Get formulario
     *
     * @return integer
     */
    public function getFormulario()
    {
        return $this->formulario;
    }

    /**
     * Set usuarioResponde
     *
     * @param integer $usuarioResponde
     * @return UsuariosFormularios
     */
    public function setUsuarioResponde($usuarioResponde = null)
    {
        $this->usuarioResponde = $usuarioResponde;
    
        return $this;
    }

    /**
     * Get usuarioResponde
     *
     * @return integer
     */
    public function getUsuarioResponde()
    {
        return $this->usuarioResponde;
    }
}