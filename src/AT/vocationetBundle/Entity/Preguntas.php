<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Preguntas
 *
 * @ORM\Table(name="preguntas")
 * @ORM\Entity
 */
class Preguntas
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
     * @ORM\Column(name="pregunta", type="string", length=255, nullable=true)
     */
    private $pregunta;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="integer", nullable=true)
     */
    private $numero;

    /**
     * @var \Formularios
     *
     * @ORM\ManyToOne(targetEntity="Formularios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="formulario_id", referencedColumnName="id")
     * })
     */
    private $formulario;

    /**
     * @var \PreguntasTipos
     *
     * @ORM\ManyToOne(targetEntity="PreguntasTipos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="preguntastipo_id", referencedColumnName="id")
     * })
     */
    private $preguntaTipo;



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
     * Set pregunta
     *
     * @param string $pregunta
     * @return Preguntas
     */
    public function setPregunta($pregunta)
    {
        $this->pregunta = $pregunta;
    
        return $this;
    }

    /**
     * Get pregunta
     *
     * @return string 
     */
    public function getPregunta()
    {
        return $this->pregunta;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     * @return Preguntas
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    
        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set formulario
     *
     * @param \AT\vocationetBundle\Entity\Formularios $formulario
     * @return Preguntas
     */
    public function setFormulario(\AT\vocationetBundle\Entity\Formularios $formulario = null)
    {
        $this->formulario = $formulario;
    
        return $this;
    }

    /**
     * Get formulario
     *
     * @return \AT\vocationetBundle\Entity\Formularios 
     */
    public function getFormulario()
    {
        return $this->formulario;
    }

    /**
     * Set preguntaTipo
     *
     * @param \AT\vocationetBundle\Entity\PreguntasTipos $preguntaTipo
     * @return Preguntas
     */
    public function setPreguntaTipo(\AT\vocationetBundle\Entity\PreguntasTipos $preguntaTipo = null)
    {
        $this->preguntaTipo = $preguntaTipo;
    
        return $this;
    }

    /**
     * Get preguntaTipo
     *
     * @return \AT\vocationetBundle\Entity\PreguntasTipos 
     */
    public function getPreguntaTipo()
    {
        return $this->preguntaTipo;
    }
}