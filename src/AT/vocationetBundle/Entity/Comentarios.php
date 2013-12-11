<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comentarios
 *
 * @ORM\Table(name="comentarios")
 * @ORM\Entity
 */
class Comentarios
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
     * @var string
     *
     * @ORM\Column(name="texto", type="text", nullable=false)
     */
    private $texto;

    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="integer", nullable=false)
     */
    private $estado;

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
     * @var \Comentarios
     *
     * @ORM\ManyToOne(targetEntity="Comentarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comentario_id", referencedColumnName="id")
     * })
     */
    private $comentario;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Comentarios
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
     * @return Comentarios
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
     * Set texto
     *
     * @param string $texto
     * @return Comentarios
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;
    
        return $this;
    }

    /**
     * Get texto
     *
     * @return string 
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * Set estado
     *
     * @param integer $estado
     * @return Comentarios
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
        return $this;
    }

    /**
     * Get estado
     *
     * @return integer 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set foro
     *
     * @param \AT\vocationetBundle\Entity\Foros $foro
     * @return Comentarios
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
     * Set comentario
     *
     * @param \AT\vocationetBundle\Entity\Comentarios $comentario
     * @return Comentarios
     */
    public function setComentario(\AT\vocationetBundle\Entity\Comentarios $comentario = null)
    {
        $this->comentario = $comentario;
    
        return $this;
    }

    /**
     * Get comentario
     *
     * @return \AT\vocationetBundle\Entity\Comentarios 
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * Set usuario
     *
     * @param \AT\vocationetBundle\Entity\Usuarios $usuario
     * @return Comentarios
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
}