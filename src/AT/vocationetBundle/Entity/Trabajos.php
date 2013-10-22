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
     * @var \Perfiles
     *
     * @ORM\ManyToOne(targetEntity="Perfiles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="perfil_id", referencedColumnName="id")
     * })
     */
    private $perfil;

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
     * Set perfil
     *
     * @param \AT\vocationetBundle\Entity\Perfiles $perfil
     * @return Trabajos
     */
    public function setPerfil(\AT\vocationetBundle\Entity\Perfiles $perfil = null)
    {
        $this->perfil = $perfil;
    
        return $this;
    }

    /**
     * Get perfil
     *
     * @return \AT\vocationetBundle\Entity\Perfiles 
     */
    public function getPerfil()
    {
        return $this->perfil;
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
}