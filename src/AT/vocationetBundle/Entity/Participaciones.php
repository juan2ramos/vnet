<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Participaciones
 *
 * @ORM\Table(name="participaciones")
 * @ORM\Entity
 */
class Participaciones
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="correo_invitacion", type="string", length=100, nullable=true)
     */
    private $correoInvitacion;

    /**
     * @var string
     *
     * @ORM\Column(name="archivo_reporte", type="string", length=100, nullable=true)
     */
    private $archivoReporte;

    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="integer", nullable=false)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_participa_id", type="integer", nullable=true)
     */
    private $usuarioParticipa;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_evaluado_id", type="integer", nullable=true)
     */
    private $usuarioEvaluado;

    /**
     * @var integer
     *
     * @ORM\Column(name="formulario_id", type="integer", nullable=false)
     */
    private $formulario;

    /**
     * @var \Carreras
     *
     * @ORM\Column(name="carrera_id", type="integer", nullable=true)
     */
    private $carrera;



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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Participaciones
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set correoInvitacion
     *
     * @param string $correoInvitacion
     * @return Participaciones
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
     * Set archivoReporte
     *
     * @param string $archivoReporte
     * @return Participaciones
     */
    public function setArchivoReporte($archivoReporte)
    {
        $this->archivoReporte = $archivoReporte;
    
        return $this;
    }

    /**
     * Get archivoReporte
     *
     * @return string 
     */
    public function getArchivoReporte()
    {
        return $this->archivoReporte;
    }

    /**
     * Set estado
     *
     * @param integer $estado
     * @return Participaciones
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
        return $this;
    }

    /**
     * Get estado
     *
     * @return integer 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set usuarioParticipa
     *
     * @param integer $usuarioParticipa
     * @return Participaciones
     */
    public function setUsuarioParticipa($usuarioParticipa = null)
    {
        $this->usuarioParticipa = $usuarioParticipa;
    
        return $this;
    }

    /**
     * Get usuarioParticipa
     *
     * @return integer
     */
    public function getUsuarioParticipa()
    {
        return $this->usuarioParticipa;
    }

    /**
     * Set usuarioEvaluado
     *
     * @param integer $usuarioEvaluado
     * @return Participaciones
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
     * @return Participaciones
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
     * Set carrera
     *
     * @param integer $carrera
     * @return Participaciones
     */
    public function setCarrera($carrera = null)
    {
        $this->carrera = $carrera;
    
        return $this;
    }

    /**
     * Get carrera
     *
     * @return integer
     */
    public function getCarrera()
    {
        return $this->carrera;
    }
}