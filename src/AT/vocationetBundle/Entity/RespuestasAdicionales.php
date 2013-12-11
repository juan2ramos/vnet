<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RespuestasAdicionales
 *
 * @ORM\Table(name="respuestas_adicionales")
 * @ORM\Entity
 */
class RespuestasAdicionales
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
     * @ORM\Column(name="respuesta_key", type="string", length=45, nullable=false)
     */
    private $respuestaKey;

    /**
     * @var string
     *
     * @ORM\Column(name="respuesta_json", type="string", length=45, nullable=false)
     */
    private $respuestaJson;

    /**
     * @var \Participaciones
     *
     * @ORM\ManyToOne(targetEntity="Participaciones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="participacion_id", referencedColumnName="id")
     * })
     */
    private $participacion;



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
     * Set respuestaKey
     *
     * @param string $respuestaKey
     * @return RespuestasAdicionales
     */
    public function setRespuestaKey($respuestaKey)
    {
        $this->respuestaKey = $respuestaKey;
    
        return $this;
    }

    /**
     * Get respuestaKey
     *
     * @return string 
     */
    public function getRespuestaKey()
    {
        return $this->respuestaKey;
    }

    /**
     * Set respuestaJson
     *
     * @param string $respuestaJson
     * @return RespuestasAdicionales
     */
    public function setRespuestaJson($respuestaJson)
    {
        $this->respuestaJson = $respuestaJson;
    
        return $this;
    }

    /**
     * Get respuestaJson
     *
     * @return string 
     */
    public function getRespuestaJson()
    {
        return $this->respuestaJson;
    }

    /**
     * Set participacion
     *
     * @param \AT\vocationetBundle\Entity\Participaciones $participacion
     * @return RespuestasAdicionales
     */
    public function setParticipacion(\AT\vocationetBundle\Entity\Participaciones $participacion = null)
    {
        $this->participacion = $participacion;
    
        return $this;
    }

    /**
     * Get participacion
     *
     * @return \AT\vocationetBundle\Entity\Participaciones 
     */
    public function getParticipacion()
    {
        return $this->participacion;
    }
}