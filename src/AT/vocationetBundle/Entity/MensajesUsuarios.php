<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MensajesUsuarios
 *
 * @ORM\Table(name="mensajes_usuarios")
 * @ORM\Entity
 */
class MensajesUsuarios
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
     * @var integer
     *
     * @ORM\Column(name="tipo", type="integer", nullable=true)
     */
    private $tipo;

    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="integer", nullable=false)
     */
    private $estado;

    /**
     * @var \Mensajes
     *
     * @ORM\ManyToOne(targetEntity="Mensajes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mensaje_id", referencedColumnName="id")
     * })
     */
    private $mensaje;

    /**
     * @var \Usuarios
     *
     * @ORM\Column(name="usuario_id", type="integer", nullable=false)
     */
    private $usuario;



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
     * Set tipo
     *
     * @param integer $tipo
     * @return MensajesUsuarios
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return integer 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set estado
     *
     * @param boolean $estado
     * @return MensajesUsuarios
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
     * Set mensaje
     *
     * @param \AT\vocationetBundle\Entity\Mensajes $mensaje
     * @return MensajesUsuarios
     */
    public function setMensaje(\AT\vocationetBundle\Entity\Mensajes $mensaje = null)
    {
        $this->mensaje = $mensaje;
    
        return $this;
    }

    /**
     * Get mensaje
     *
     * @return \AT\vocationetBundle\Entity\Mensajes 
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * Set usuario
     *
     * @param \AT\vocationetBundle\Entity\Usuarios $usuario
     * @return MensajesUsuarios
     */
    public function setUsuario($usuario = null)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    /**
     * Get usuario
     *
     * @return \AT\vocationetBundle\Entity\Usuarios 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}