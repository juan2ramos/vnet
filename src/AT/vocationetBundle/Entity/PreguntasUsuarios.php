<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PreguntasUsuarios
 *
 * @ORM\Table(name="preguntas_usuarios")
 * @ORM\Entity
 */
class PreguntasUsuarios
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
     * @var integer
     *
     * @ORM\Column(name="respuesta_numerica", type="integer", nullable=true)
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
     * @var integer
     *
     * @ORM\Column(name="usuario_id", type="integer", nullable=false)
     */
    private $usuario;

    /**
     * @var integer
     *
     * @ORM\Column(name="pregunta_id", type="integer", nullable=false)
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
     * @param integer $respuestaNumerica
     * @return PreguntasUsuarios
     */
    public function setRespuestaNumerica($respuestaNumerica)
    {
        $this->respuestaNumerica = $respuestaNumerica;
    
        return $this;
    }

    /**
     * Get respuestaNumerica
     *
     * @return integer 
     */
    public function getRespuestaNumerica()
    {
        return $this->respuestaNumerica;
    }

    /**
     * Set respuestaTexto
     *
     * @param string $respuestaTexto
     * @return PreguntasUsuarios
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
     * @return PreguntasUsuarios
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
     * Set usuario
     *
     * @param integer $usuario
     * @return PreguntasUsuarios
     */
    public function setUsuario($usuario = null)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    /**
     * Get usuario
     *
     * @return integer
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set pregunta
     *
     * @param integer $pregunta
     * @return PreguntasUsuarios
     */
    public function setPregunta($pregunta = null)
    {
        $this->pregunta = $pregunta;
    
        return $this;
    }

    /**
     * Get pregunta
     *
     * @return integer
     */
    public function getPregunta()
    {
        return $this->pregunta;
    }
}