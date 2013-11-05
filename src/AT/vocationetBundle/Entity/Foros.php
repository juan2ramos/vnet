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
     * @ORM\Column(name="foro_titulo", type="string", length=255, nullable=false)
     */
    private $foroTitulo;

    /**
     * @var string
     *
     * @ORM\Column(name="foro_texto", type="text", nullable=true)
     */
    private $foroTexto;

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
     * @var \Usuarios
     *
     * @ORM\ManyToOne(targetEntity="Usuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set foroTitulo
     *
     * @param string $foroTitulo
     * @return Foros
     */
    public function setForoTitulo($foroTitulo)
    {
        $this->foroTitulo = $foroTitulo;
    
        return $this;
    }

    /**
     * Get foroTitulo
     *
     * @return string 
     */
    public function getForoTitulo()
    {
        return $this->foroTitulo;
    }

    /**
     * Set foroTexto
     *
     * @param string $foroTexto
     * @return Foros
     */
    public function setForoTexto($foroTexto)
    {
        $this->foroTexto = $foroTexto;
    
        return $this;
    }

    /**
     * Get foroTexto
     *
     * @return string 
     */
    public function getForoTexto()
    {
        return $this->foroTexto;
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
     * Set usuario
     *
     * @param \AT\vocationetBundle\Entity\Usuarios $usuario
     * @return Foros
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
}