<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Foros
 *
 * @ORM\Table(name="foros")
 * @ORM\Entity
 */
class Foros
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
     * @ORM\Column(name="titulo", type="string", length=255, nullable=false)
     */
    private $titulo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified;

    /**
     * @var \Temas
     *
     * @ORM\ManyToOne(targetEntity="Temas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tema_id", referencedColumnName="id")
     * })
     */
    private $tema;

    /**
     * @var \Usuarios
     *
     * @ORM\ManyToOne(targetEntity="Usuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="uduario_id", referencedColumnName="id")
     * })
     */
    private $uduario;



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
     * Set titulo
     *
     * @param string $titulo
     * @return Foros
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    
        return $this;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Foros
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
     * @return Foros
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

    /**
     * Set tema
     *
     * @param \AT\vocationetBundle\Entity\Temas $tema
     * @return Foros
     */
    public function setTema(\AT\vocationetBundle\Entity\Temas $tema = null)
    {
        $this->tema = $tema;
    
        return $this;
    }

    /**
     * Get tema
     *
     * @return \AT\vocationetBundle\Entity\Temas 
     */
    public function getTema()
    {
        return $this->tema;
    }

    /**
     * Set uduario
     *
     * @param \AT\vocationetBundle\Entity\Usuarios $uduario
     * @return Foros
     */
    public function setUduario(\AT\vocationetBundle\Entity\Usuarios $uduario = null)
    {
        $this->uduario = $uduario;
    
        return $this;
    }

    /**
     * Get uduario
     *
     * @return \AT\vocationetBundle\Entity\Usuarios 
     */
    public function getUduario()
    {
        return $this->uduario;
    }
}