<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Archivos
 *
 * @ORM\Table(name="archivos")
 * @ORM\Entity
 */
class Archivos
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
     * @ORM\Column(name="archivo_nombre", type="string", length=100, nullable=false)
     */
    private $archivoNombre;

    /**
     * @var string
     *
     * @ORM\Column(name="archivo_path", type="string", length=100, nullable=false)
     */
    private $archivoPath;

    /**
     * @var string
     *
     * @ORM\Column(name="archivo_size", type="string", length=100, nullable=true)
     */
    private $archivoSize;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;



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
     * Set archivoNombre
     *
     * @param string $archivoNombre
     * @return Archivos
     */
    public function setArchivoNombre($archivoNombre)
    {
        $this->archivoNombre = $archivoNombre;
    
        return $this;
    }

    /**
     * Get archivoNombre
     *
     * @return string 
     */
    public function getArchivoNombre()
    {
        return $this->archivoNombre;
    }

    /**
     * Set archivoPath
     *
     * @param string $archivoPath
     * @return Archivos
     */
    public function setArchivoPath($archivoPath)
    {
        $this->archivoPath = $archivoPath;
    
        return $this;
    }

    /**
     * Get archivoPath
     *
     * @return string 
     */
    public function getArchivoPath()
    {
        return $this->archivoPath;
    }

    /**
     * Set archivoSize
     *
     * @param string $archivoSize
     * @return Archivos
     */
    public function setArchivoSize($archivoSize)
    {
        $this->archivoSize = $archivoSize;
    
        return $this;
    }

    /**
     * Get archivoSize
     *
     * @return string 
     */
    public function getArchivoSize()
    {
        return $this->archivoSize;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Archivos
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
}