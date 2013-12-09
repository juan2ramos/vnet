<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Informacion
 *
 * @ORM\Table(name="informacion")
 * @ORM\Entity
 */
class Informacion
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
     * @ORM\Column(name="informacion_imagen", type="string", length=100, nullable=true)
     */
    private $informacionImagen;

    /**
     * @var string
     *
     * @ORM\Column(name="informacion_link", type="string", length=100, nullable=true)
     */
    private $informacionLink;

    /**
     * @var boolean
     *
     * @ORM\Column(name="informacion_estado", type="boolean", nullable=true)
     */
    private $informacionEstado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified;



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
     * Set informacionImagen
     *
     * @param string $informacionImagen
     * @return Informacion
     */
    public function setInformacionImagen($informacionImagen)
    {
        $this->informacionImagen = $informacionImagen;
    
        return $this;
    }

    /**
     * Get informacionImagen
     *
     * @return string 
     */
    public function getInformacionImagen()
    {
        return $this->informacionImagen;
    }

    /**
     * Set informacionLink
     *
     * @param string $informacionLink
     * @return Informacion
     */
    public function setInformacionLink($informacionLink)
    {
        $this->informacionLink = $informacionLink;
    
        return $this;
    }

    /**
     * Get informacionLink
     *
     * @return string 
     */
    public function getInformacionLink()
    {
        return $this->informacionLink;
    }

    /**
     * Set informacionEstado
     *
     * @param boolean $informacionEstado
     * @return Informacion
     */
    public function setInformacionEstado($informacionEstado)
    {
        $this->informacionEstado = $informacionEstado;
    
        return $this;
    }

    /**
     * Get informacionEstado
     *
     * @return boolean 
     */
    public function getInformacionEstado()
    {
        return $this->informacionEstado;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Informacion
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Informacion
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    
        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime 
     */
    public function getModified()
    {
        return $this->modified;
    }
}