<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trabajos
 *
 * @ORM\Table(name="trabajos")
 * @ORM\Entity
 */
class Trabajos
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
     * @ORM\Column(name="cargo", type="string", length=150, nullable=false)
     */
    private $cargo;

    /**
     * @var string
     *
     * @ORM\Column(name="resumen", type="text", nullable=true)
     */
    private $resumen;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="date", nullable=true)
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_final", type="date", nullable=true)
     */
    private $fechaFinal;

    /**
     * @var integer
     *
     * @ORM\Column(name="es_actual", type="integer", nullable=false)
     */
    private $esActual;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_linkedin", type="integer", nullable=true)
     */
    private $idLinkedin;

    /**
     * @var \Empresas
     *
     * @ORM\ManyToOne(targetEntity="Empresas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="empresa_id", referencedColumnName="id")
     * })
     */
    private $empresa;

    /**
     * @var \Usuarios
     *
     * @ORM\ManyToOne(targetEntity="Usuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
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
     * Set cargo
     *
     * @param string $cargo
     * @return Trabajos
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;
    
        return $this;
    }

    /**
     * Get cargo
     *
     * @return string 
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Set resumen
     *
     * @param string $resumen
     * @return Trabajos
     */
    public function setResumen($resumen)
    {
        $this->resumen = $resumen;
    
        return $this;
    }

    /**
     * Get resumen
     *
     * @return string 
     */
    public function getResumen()
    {
        return $this->resumen;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return Trabajos
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    
        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime 
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFinal
     *
     * @param \DateTime $fechaFinal
     * @return Trabajos
     */
    public function setFechaFinal($fechaFinal)
    {
        $this->fechaFinal = $fechaFinal;
    
        return $this;
    }

    /**
     * Get fechaFinal
     *
     * @return \DateTime 
     */
    public function getFechaFinal()
    {
        return $this->fechaFinal;
    }

    /**
     * Set esActual
     *
     * @param integer $esActual
     * @return Trabajos
     */
    public function setEsActual($esActual)
    {
        $this->esActual = $esActual;
    
        return $this;
    }

    /**
     * Get esActual
     *
     * @return integer 
     */
    public function getEsActual()
    {
        return $this->esActual;
    }

    /**
     * Set idLinkedin
     *
     * @param integer $idLinkedin
     * @return Trabajos
     */
    public function setIdLinkedin($idLinkedin)
    {
        $this->idLinkedin = $idLinkedin;
    
        return $this;
    }

    /**
     * Get idLinkedin
     *
     * @return integer 
     */
    public function getIdLinkedin()
    {
        return $this->idLinkedin;
    }

    /**
     * Set empresa
     *
     * @param \AT\vocationetBundle\Entity\Empresas $empresa
     * @return Trabajos
     */
    public function setEmpresa(\AT\vocationetBundle\Entity\Empresas $empresa = null)
    {
        $this->empresa = $empresa;
    
        return $this;
    }

    /**
     * Get empresa
     *
     * @return \AT\vocationetBundle\Entity\Empresas 
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set usuario
     *
     * @param \AT\vocationetBundle\Entity\Usuarios $usuario
     * @return Trabajos
     */
    public function setUsuario(\AT\vocationetBundle\Entity\Usuarios $usuario = null)
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