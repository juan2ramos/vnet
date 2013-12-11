<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Formularios
 *
 * @ORM\Table(name="formularios")
 * @ORM\Entity
 */
class Formularios
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
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     */
    private $nombre;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="integer", nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="encabezado", type="text", nullable=true)
     */
    private $encabezado;

    /**
     * @var \Formularios
     *
     * @ORM\ManyToOne(targetEntity="Formularios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="formulario_id", referencedColumnName="id")
     * })
     */
    private $formulario;



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
     * Set nombre
     *
     * @param string $nombre
     * @return Formularios
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     * @return Formularios
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    
        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Formularios
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set encabezado
     *
     * @param string $encabezado
     * @return Formularios
     */
    public function setEncabezado($encabezado)
    {
        $this->encabezado = $encabezado;
    
        return $this;
    }

    /**
     * Get encabezado
     *
     * @return string 
     */
    public function getEncabezado()
    {
        return $this->encabezado;
    }

    /**
     * Set formulario
     *
     * @param \AT\vocationetBundle\Entity\Formularios $formulario
     * @return Formularios
     */
    public function setFormulario(\AT\vocationetBundle\Entity\Formularios $formulario = null)
    {
        $this->formulario = $formulario;
    
        return $this;
    }

    /**
     * Get formulario
     *
     * @return \AT\vocationetBundle\Entity\Formularios 
     */
    public function getFormulario()
    {
        return $this->formulario;
    }
}