<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Respuestas
 *
 * @ORM\Table(name="respuestas")
 * @ORM\Entity
 */
class Respuestas
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
     * @var float
     *
     * @ORM\Column(name="respuesta_numerica", type="float", nullable=true)
     */
    private $respuestaNumerica;

    /**
     * @var string
     *
     * @ORM\Column(name="respuesta_texto", type="string", length=255, nullable=true)
     */
    private $respuestaTexto;

    /**
     * @var integer
     *
     * @ORM\Column(name="valor", type="integer", nullable=true)
     */
    private $valor;

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
     * Set respuestaNumerica
     *
     * @param float $respuestaNumerica
     * @return Respuestas
     */
    public function setRespuestaNumerica($respuestaNumerica)
    {
        $this->respuestaNumerica = $respuestaNumerica;
    
        return $this;
    }

    /**
     * Get respuestaNumerica
     *
     * @return float 
     */
    public function getRespuestaNumerica()
    {
        return $this->respuestaNumerica;
    }

    /**
     * Set respuestaTexto
     *
     * @param string $respuestaTexto
     * @return Respuestas
     */
    public function setRespuestaTexto($respuestaTexto)
    {
        $this->respuestaTexto = $respuestaTexto;
    
        return $this;
    }

    /**
     * Get respuestaTexto
     *
     * @return string 
     */
    public function getRespuestaTexto()
    {
        return $this->respuestaTexto;
    }

    /**
     * Set valor
     *
     * @param integer $valor
     * @return Respuestas
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    
        return $this;
    }

    /**
     * Get valor
     *
     * @return integer 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set participacion
     *
     * @param \AT\vocationetBundle\Entity\Participaciones $participacion
     * @return Respuestas
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

    /**
     * Set pregunta
     *
     * @param \AT\vocationetBundle\Entity\Preguntas $pregunta
     * @return Respuestas
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