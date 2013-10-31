<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MensajesArchivos
 *
 * @ORM\Table(name="mensajes_archivos")
 * @ORM\Entity
 */
class MensajesArchivos
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
     * @var \Mensajes
     *
     * @ORM\ManyToOne(targetEntity="Mensajes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mensaje_id", referencedColumnName="id")
     * })
     */
    private $mensaje;

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
     * Set mensaje
     *
     * @param \AT\vocationetBundle\Entity\Mensajes $mensaje
     * @return MensajesArchivos
     */
    public function setMensaje(\AT\vocationetBundle\Entity\Mensajes $mensaje = null)
    {
        $this->mensaje = $mensaje;
    
        return $this;
    }

    /**
     * Get mensaje
     *
     * @return \AT\vocationetBundle\Entity\Mensajes 
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * Set archivo
     *
     * @param \AT\vocationetBundle\Entity\Archivos $archivo
     * @return MensajesArchivos
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