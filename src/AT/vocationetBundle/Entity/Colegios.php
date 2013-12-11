<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Colegios
 *
 * @ORM\Table(name="colegios")
 * @ORM\Entity
 */
class Colegios
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
     * @ORM\Column(name="nombre", type="string", length=45, nullable=false)
	 * @Assert\NotNull()
     * @Assert\Length(min = 3,  max = 45)
     */
    private $nombre;

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
     * @return Colegios
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
     * Set georeferencia
     *
     * @param \AT\vocationetBundle\Entity\Georeferencias $georeferencia
     * @return Colegios
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
}