<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Estudios
 *
 * @ORM\Table(name="estudios")
 * @ORM\Entity
 */
class Estudios
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
     * @ORM\Column(name="campo", type="string", length=155, nullable=true)
     */
    private $campo;

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
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=155, nullable=true)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="actividad", type="text", nullable=true)
     */
    private $actividad;

    /**
     * @var string
     *
     * @ORM\Column(name="notas", type="text", nullable=true)
     */
    private $notas;

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
     * @var \Instituciones
     *
     * @ORM\ManyToOne(targetEntity="Instituciones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="institucion_id", referencedColumnName="id")
     * })
     */
    private $institucion;



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
     * Set campo
     *
     * @param string $campo
     * @return Estudios
     */
    public function setCampo($campo)
    {
        $this->campo = $campo;
    
        return $this;
    }

    /**
     * Get campo
     *
     * @return string 
     */
    public function getCampo()
    {
        return $this->campo;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return Estudios
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
     * @return Estudios
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
     * Set titulo
     *
     * @param string $titulo
     * @return Estudios
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    
        return $this;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set actividad
     *
     * @param string $actividad
     * @return Estudios
     */
    public function setActividad($actividad)
    {
        $this->actividad = $actividad;
    
        return $this;
    }

    /**
     * Get actividad
     *
     * @return string 
     */
    public function getActividad()
    {
        return $this->actividad;
    }

    /**
     * Set notas
     *
     * @param string $notas
     * @return Estudios
     */
    public function setNotas($notas)
    {
        $this->notas = $notas;
    
        return $this;
    }

    /**
     * Get notas
     *
     * @return string 
     */
    public function getNotas()
    {
        return $this->notas;
    }

    /**
     * Set perfil
     *
     * @param \AT\vocationetBundle\Entity\Perfiles $perfil
     * @return Estudios
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
     * Set institucion
     *
     * @param \AT\vocationetBundle\Entity\Instituciones $institucion
     * @return Estudios
     */
    public function setInstitucion(\AT\vocationetBundle\Entity\Instituciones $institucion = null)
    {
        $this->institucion = $institucion;
    
        return $this;
    }

    /**
     * Get institucion
     *
     * @return \AT\vocationetBundle\Entity\Instituciones 
     */
    public function getInstitucion()
    {
        return $this->institucion;
    }
}