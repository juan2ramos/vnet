<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Usuarios
 *
 * @ORM\Table(name="usuarios")
 * @ORM\Entity
 */
class Usuarios
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
     * @ORM\Column(name="usuario_nombre", type="string", length=45, nullable=false)
     */
    private $usuarioNombre;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_apellido", type="string", length=45, nullable=false)
     */
    private $usuarioApellido;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_email", type="string", length=100, nullable=false)
     */
    private $usuarioEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_password", type="string", length=45, nullable=true)
     */
    private $usuarioPassword;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_estado", type="integer", nullable=false)
     */
    private $usuarioEstado;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_hash", type="string", length=45, nullable=true)
     */
    private $usuarioHash;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_facebookid", type="integer", nullable=true)
     */
    private $usuarioFacebookid;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_tipo", type="integer", nullable=false)
     */
    private $usuarioTipo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified;

    /**
     * @var \Roles
     *
     * @ORM\ManyToOne(targetEntity="Roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rol_id", referencedColumnName="id")
     * })
     */
    private $rol;



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
     * Set usuarioNombre
     *
     * @param string $usuarioNombre
     * @return Usuarios
     */
    public function setUsuarioNombre($usuarioNombre)
    {
        $this->usuarioNombre = $usuarioNombre;
    
        return $this;
    }

    /**
     * Get usuarioNombre
     *
     * @return string 
     */
    public function getUsuarioNombre()
    {
        return $this->usuarioNombre;
    }

    /**
     * Set usuarioApellido
     *
     * @param string $usuarioApellido
     * @return Usuarios
     */
    public function setUsuarioApellido($usuarioApellido)
    {
        $this->usuarioApellido = $usuarioApellido;
    
        return $this;
    }

    /**
     * Get usuarioApellido
     *
     * @return string 
     */
    public function getUsuarioApellido()
    {
        return $this->usuarioApellido;
    }

    /**
     * Set usuarioEmail
     *
     * @param string $usuarioEmail
     * @return Usuarios
     */
    public function setUsuarioEmail($usuarioEmail)
    {
        $this->usuarioEmail = $usuarioEmail;
    
        return $this;
    }

    /**
     * Get usuarioEmail
     *
     * @return string 
     */
    public function getUsuarioEmail()
    {
        return $this->usuarioEmail;
    }

    /**
     * Set usuarioPassword
     *
     * @param string $usuarioPassword
     * @return Usuarios
     */
    public function setUsuarioPassword($usuarioPassword)
    {
        $this->usuarioPassword = $usuarioPassword;
    
        return $this;
    }

    /**
     * Get usuarioPassword
     *
     * @return string 
     */
    public function getUsuarioPassword()
    {
        return $this->usuarioPassword;
    }

    /**
     * Set usuarioEstado
     *
     * @param integer $usuarioEstado
     * @return Usuarios
     */
    public function setUsuarioEstado($usuarioEstado)
    {
        $this->usuarioEstado = $usuarioEstado;
    
        return $this;
    }

    /**
     * Get usuarioEstado
     *
     * @return integer 
     */
    public function getUsuarioEstado()
    {
        return $this->usuarioEstado;
    }

    /**
     * Set usuarioHash
     *
     * @param string $usuarioHash
     * @return Usuarios
     */
    public function setUsuarioHash($usuarioHash)
    {
        $this->usuarioHash = $usuarioHash;
    
        return $this;
    }

    /**
     * Get usuarioHash
     *
     * @return string 
     */
    public function getUsuarioHash()
    {
        return $this->usuarioHash;
    }

    /**
     * Set usuarioFacebookid
     *
     * @param integer $usuarioFacebookid
     * @return Usuarios
     */
    public function setUsuarioFacebookid($usuarioFacebookid)
    {
        $this->usuarioFacebookid = $usuarioFacebookid;
    
        return $this;
    }

    /**
     * Get usuarioFacebookid
     *
     * @return integer 
     */
    public function getUsuarioFacebookid()
    {
        return $this->usuarioFacebookid;
    }

    /**
     * Set usuarioTipo
     *
     * @param integer $usuarioTipo
     * @return Usuarios
     */
    public function setUsuarioTipo($usuarioTipo)
    {
        $this->usuarioTipo = $usuarioTipo;
    
        return $this;
    }

    /**
     * Get usuarioTipo
     *
     * @return integer 
     */
    public function getUsuarioTipo()
    {
        return $this->usuarioTipo;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Usuarios
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
     * @return Usuarios
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
     * Set rol
     *
     * @param \AT\vocationetBundle\Entity\Roles $rol
     * @return Usuarios
     */
    public function setRol(\AT\vocationetBundle\Entity\Roles $rol = null)
    {
        $this->rol = $rol;
    
        return $this;
    }

    /**
     * Get rol
     *
     * @return \AT\vocationetBundle\Entity\Roles 
     */
    public function getRol()
    {
        return $this->rol;
    }
}