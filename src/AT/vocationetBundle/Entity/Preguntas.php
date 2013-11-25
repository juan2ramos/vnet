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
     * @var \Preguntastipos
     *
     * @ORM\ManyToOne(targetEntity="Preguntastipos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="preguntastipo_id", referencedColumnName="id")
     * })
     */
    private $preguntastipo;



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
     * Set preguntastipo
     *
     * @param \AT\vocationetBundle\Entity\Preguntastipos $preguntastipo
     * @return Preguntas
     */
    public function setPreguntastipo(\AT\vocationetBundle\Entity\Preguntastipos $preguntastipo = null)
    {
        $this->preguntastipo = $preguntastipo;
    
        return $this;
    }

    /**
     * Get preguntastipo
     *
     * @return \AT\vocationetBundle\Entity\Preguntastipos 
     */
    public function getPreguntastipo()
    {
        return $this->preguntastipo;
    }
}