<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Temas
 *
 * @ORM\Table(name="temas")
 * @ORM\Entity
 */
class Temas
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
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var \Carreras
     *
     * @ORM\ManyToOne(targetEntity="Carreras")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="carrera_id", referencedColumnName="id")
     * })
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
     * Set nombre
     *
     * @param string $nombre
     * @return Temas
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
     * Set carrera
     *
     * @param \AT\vocationetBundle\Entity\Carreras $carrera
     * @return Temas
     */
    public function setCarrera(\AT\vocationetBundle\Entity\Carreras $carrera = null)
    {
        $this->carrera = $carrera;
    
        return $this;
    }

    /**
     * Get carrera
     *
     * @return \AT\vocationetBundle\Entity\Carreras 
     */
    public function getCarrera()
    {
        return $this->carrera;
    }
}