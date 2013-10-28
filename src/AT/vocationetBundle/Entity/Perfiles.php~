<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Perfiles
 *
 * @ORM\Table(name="perfiles")
 * @ORM\Entity
 */
class Perfiles
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
     * @ORM\Column(name="fechanacimiento", type="date", nullable=false)
     */
    private $fechanacimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="genero", type="string", length=45, nullable=false)
     */
    private $genero;

    /**
     * @var integer
     *
     * @ORM\Column(name="curso_actual", type="integer", nullable=true)
     */
    private $cursoActual;

    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", length=155, nullable=true)
     */
    private $imagen;

    /**
     * @var string
     *
     * @ORM\Column(name="tarjeta_profesional", type="string", length=155, nullable=true)
     */
    private $tarjetaProfesional;

    /**
     * @var string
     *
     * @ORM\Column(name="hojavida", type="string", length=155, nullable=true)
     */
    private $hojavida;

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
     * @var \Georeferencias
     *
     * @ORM\ManyToOne(targetEntity="Georeferencias")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="georeferencia_id", referencedColumnName="id")
     * })
     */
    private $georeferencia;

    /**
     * @var \Colegios
     *
     * @ORM\ManyToOne(targetEntity="Colegios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="colegio_id", referencedColumnName="id")
     * })
     */
    private $colegio;



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
     * Set fechanacimiento
     *
     * @param \DateTime $fechanacimiento
     * @return Perfiles
     */
    public function setFechanacimiento($fechanacimiento)
    {
        $this->fechanacimiento = $fechanacimiento;
    
        return $this;
    }

    /**
     * Get fechanacimiento
     *
     * @return \DateTime 
     */
    public function getFechanacimiento()
    {
        return $this->fechanacimiento;
    }

    /**
     * Set genero
     *
     * @param string $genero
     * @return Perfiles
     */
    public function setGenero($genero)
    {
        $this->genero = $genero;
    
        return $this;
    }

    /**
     * Get genero
     *
     * @return string 
     */
    public function getGenero()
    {
        return $this->genero;
    }

    /**
     * Set cursoActual
     *
     * @param integer $cursoActual
     * @return Perfiles
     */
    public function setCursoActual($cursoActual)
    {
        $this->cursoActual = $cursoActual;
    
        return $this;
    }

    /**
     * Get cursoActual
     *
     * @return integer 
     */
    public function getCursoActual()
    {
        return $this->cursoActual;
    }

    /**
     * Set imagen
     *
     * @param string $imagen
     * @return Perfiles
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    
        return $this;
    }

    /**
     * Get imagen
     *
     * @return string 
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set tarjetaProfesional
     *
     * @param string $tarjetaProfesional
     * @return Perfiles
     */
    public function setTarjetaProfesional($tarjetaProfesional)
    {
        $this->tarjetaProfesional = $tarjetaProfesional;
    
        return $this;
    }

    /**
     * Get tarjetaProfesional
     *
     * @return string 
     */
    public function getTarjetaProfesional()
    {
        return $this->tarjetaProfesional;
    }

    /**
     * Set hojavida
     *
     * @param string $hojavida
     * @return Perfiles
     */
    public function setHojavida($hojavida)
    {
        $this->hojavida = $hojavida;
    
        return $this;
    }

    /**
     * Get hojavida
     *
     * @return string 
     */
    public function getHojavida()
    {
        return $this->hojavida;
    }

    /**
     * Set usuario
     *
     * @param \AT\vocationetBundle\Entity\Usuarios $usuario
     * @return Perfiles
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

    /**
     * Set georeferencia
     *
     * @param \AT\vocationetBundle\Entity\Georeferencias $georeferencia
     * @return Perfiles
     */
    public function setGeoreferencia(\AT\vocationetBundle\Entity\Georeferencias $georeferencia = null)
    {
        $this->georeferencia = $georeferencia;
    
        return $this;
    }

    /**
     * Get georeferencia
     *
     * @return \AT\vocationetBundle\Entity\Georeferencias 
     */
    public function getGeoreferencia()
    {
        return $this->georeferencia;
    }

    /**
     * Set colegio
     *
     * @param \AT\vocationetBundle\Entity\Colegios $colegio
     * @return Perfiles
     */
    public function setColegio(\AT\vocationetBundle\Entity\Colegios $colegio = null)
    {
        $this->colegio = $colegio;
    
        return $this;
    }

    /**
     * Get colegio
     *
     * @return \AT\vocationetBundle\Entity\Colegios 
     */
    public function getColegio()
    {
        return $this->colegio;
    }
}