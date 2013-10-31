<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ForosArchivos
 *
 * @ORM\Table(name="foros_archivos")
 * @ORM\Entity
 */
class ForosArchivos
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
     * @var \Foros
     *
     * @ORM\ManyToOne(targetEntity="Foros")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="foro_id", referencedColumnName="id")
     * })
     */
    private $foro;

    /**
     * @var \Archivos
     *
     * @ORM\ManyToOne(targetEntity="Archivos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="archivo_id", referencedColumnName="id")
     * })
     */
    private $archivo;



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
     * Set foro
     *
     * @param \AT\vocationetBundle\Entity\Foros $foro
     * @return ForosArchivos
     */
    public function setForo(\AT\vocationetBundle\Entity\Foros $foro = null)
    {
        $this->foro = $foro;
    
        return $this;
    }

    /**
     * Get foro
     *
     * @return \AT\vocationetBundle\Entity\Foros 
     */
    public function getForo()
    {
        return $this->foro;
    }

    /**
     * Set archivo
     *
     * @param \AT\vocationetBundle\Entity\Archivos $archivo
     * @return ForosArchivos
     */
    public function setArchivo(\AT\vocationetBundle\Entity\Archivos $archivo = null)
    {
        $this->archivo = $archivo;
    
        return $this;
    }

    /**
     * Get archivo
     *
     * @return \AT\vocationetBundle\Entity\Archivos 
     */
    public function getArchivo()
    {
        return $this->archivo;
    }
}