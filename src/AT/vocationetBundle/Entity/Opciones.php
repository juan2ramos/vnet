<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Opciones
 *
 * @ORM\Table(name="opciones")
 * @ORM\Entity
 */
class Opciones
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
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var integer
     *
     * @ORM\Column(name="peso", type="integer", nullable=true)
     */
    private $peso;

    /**
     * @var integer
     *
     * @ORM\Column(name="factor", type="integer", nullable=true)
     */
    private $factor;

    /**
     * @var \Preguntas
     *
     * @ORM\ManyToOne(targetEntity="Preguntas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pregunta_id", referencedColumnName="id")
     * })
     */
    private $pregunta;



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
     * @return Opciones
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
     * Set peso
     *
     * @param integer $peso
     * @return Opciones
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;
    
        return $this;
    }

    /**
     * Get peso
     *
     * @return integer 
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set factor
     *
     * @param integer $factor
     * @return Opciones
     */
    public function setFactor($factor)
    {
        $this->factor = $factor;
    
        return $this;
    }

    /**
     * Get factor
     *
     * @return integer 
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * Set pregunta
     *
     * @param \AT\vocationetBundle\Entity\Preguntas $pregunta
     * @return Opciones
     */
    public function setPregunta(\AT\vocationetBundle\Entity\Preguntas $pregunta = null)
    {
        $this->pregunta = $pregunta;
    
        return $this;
    }

    /**
     * Get pregunta
     *
     * @return \AT\vocationetBundle\Entity\Preguntas 
     */
    public function getPregunta()
    {
        return $this->pregunta;
    }
}