<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AlternativaEstudio
 *
 * @ORM\Table(name="alternativa_estudio")
 * @ORM\Entity
 */
class AlternativaEstudio
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
     * @var \Usuarios
     *
     * @ORM\ManyToOne(targetEntity="Usuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;

    /**
     * @var \Carreras
     *
     * @ORM\ManyToOne(targetEntity="Carreras")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="carrera_id", referencedColumnName="id")
     * })
     */
    private $carrera;



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
     * Set usuario
     *
     * @param \AT\vocationetBundle\Entity\Usuarios $usuario
     * @return AlternativaEstudio
     */
    public function setUsuario(\AT\vocationetBundle\Entity\Usuarios $usuario = null)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    /**
     * Get usuario
     *
     * @return \AT\vocationetBundle\Entity\Usuarios 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set carrera
     *
     * @param \AT\vocationetBundle\Entity\Carreras $carrera
     * @return AlternativaEstudio
     */
    public function setCarrera(\AT\vocationetBundle\Entity\Carreras $carrera = null)
    {
        $this->carrera = $carrera;
    
        return $this;
    }

    /**
     * Get carrera
     *
     * @return \AT\vocationetBundle\Entity\Carreras 
     */
    public function getCarrera()
    {
        return $this->carrera;
    }
}