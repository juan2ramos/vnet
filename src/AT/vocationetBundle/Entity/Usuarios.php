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
     * @var string
     *
     * @ORM\Column(name="usuario_hash", type="string", length=45, nullable=true)
     */
    private $usuarioHash;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_estado", type="integer", nullable=false)
     */
    private $usuarioEstado;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_rol_estado", type="integer", nullable=false)
     */
    private $usuarioRolEstado;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_facebookid", type="integer", nullable=true)
     */
    private $usuarioFacebookid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="usuario_fecha_nacimiento", type="date", nullable=true)
     */
    private $usuarioFechaNacimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_genero", type="string", length=45, nullable=true)
     */
    private $usuarioGenero;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_imagen", type="string", length=155, nullable=true)
     */
    private $usuarioImagen;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_tarjeta_profesional", type="string", length=155, nullable=true)
     */
    private $usuarioTarjetaProfesional;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_hoja_vida", type="string", length=155, nullable=true)
     */
    private $usuarioHojaVida;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_profesion", type="string", length=70, nullable=true)
     */
    private $usuarioProfesion;

    /**
     * @var float
     *
     * @ORM\Column(name="usuario_puntos", type="float", nullable=true)
     */
    private $usuarioPuntos;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_perfil_profesional", type="text", nullable=true)
     */
    private $usuarioPerfilProfesional;

    /**
     * @var float
     *
     * @ORM\Column(name="usuario_valor_mentoria", type="float", nullable=true)
     */
    private $usuarioValorMentoria;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_curso_actual", type="integer", nullable=true)
     */
    private $usuarioCursoActual;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="usuario_fecha_planeacion", type="date", nullable=true)
     */
    private $usuarioFechaPlaneacion;

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
     * @var \DateTime
     *
     * @ORM\Column(name="sync_linkedin", type="datetime", nullable=true)
     */
    private $syncLinkedin;

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
     * @var \Colegios
     *
     * @ORM\ManyToOne(targetEntity="Colegios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="colegio_id", referencedColumnName="id")
     * })
     */
    private $colegio;

    /**
     * @var \Georeferencias
     *
     * @ORM\ManyToOne(targetEntity="Georeferencias")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="georeferencia_id", referencedColumnName="id")
     * })
     */
    private $georeferencia;



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
     * Set usuarioRolEstado
     *
     * @param integer $usuarioRolEstado
     * @return Usuarios
     */
    public function setUsuarioRolEstado($usuarioRolEstado)
    {
        $this->usuarioRolEstado = $usuarioRolEstado;
    
        return $this;
    }

    /**
     * Get usuarioRolEstado
     *
     * @return integer 
     */
    public function getUsuarioRolEstado()
    {
        return $this->usuarioRolEstado;
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
     * Set usuarioFechaNacimiento
     *
     * @param \DateTime $usuarioFechaNacimiento
     * @return Usuarios
     */
    public function setUsuarioFechaNacimiento($usuarioFechaNacimiento)
    {
        $this->usuarioFechaNacimiento = $usuarioFechaNacimiento;
    
        return $this;
    }

    /**
     * Get usuarioFechaNacimiento
     *
     * @return \DateTime 
     */
    public function getUsuarioFechaNacimiento()
    {
        return $this->usuarioFechaNacimiento;
    }

    /**
     * Set usuarioGenero
     *
     * @param string $usuarioGenero
     * @return Usuarios
     */
    public function setUsuarioGenero($usuarioGenero)
    {
        $this->usuarioGenero = $usuarioGenero;
    
        return $this;
    }

    /**
     * Get usuarioGenero
     *
     * @return string 
     */
    public function getUsuarioGenero()
    {
        return $this->usuarioGenero;
    }

    /**
     * Set usuarioImagen
     *
     * @param string $usuarioImagen
     * @return Usuarios
     */
    public function setUsuarioImagen($usuarioImagen)
    {
        $this->usuarioImagen = $usuarioImagen;
    
        return $this;
    }

    /**
     * Get usuarioImagen
     *
     * @return string 
     */
    public function getUsuarioImagen()
    {
        return $this->usuarioImagen;
    }

    /**
     * Set usuarioTarjetaProfesional
     *
     * @param string $usuarioTarjetaProfesional
     * @return Usuarios
     */
    public function setUsuarioTarjetaProfesional($usuarioTarjetaProfesional)
    {
        $this->usuarioTarjetaProfesional = $usuarioTarjetaProfesional;
    
        return $this;
    }

    /**
     * Get usuarioTarjetaProfesional
     *
     * @return string 
     */
    public function getUsuarioTarjetaProfesional()
    {
        return $this->usuarioTarjetaProfesional;
    }

    /**
     * Set usuarioHojaVida
     *
     * @param string $usuarioHojaVida
     * @return Usuarios
     */
    public function setUsuarioHojaVida($usuarioHojaVida)
    {
        $this->usuarioHojaVida = $usuarioHojaVida;
    
        return $this;
    }

    /**
     * Get usuarioHojaVida
     *
     * @return string 
     */
    public function getUsuarioHojaVida()
    {
        return $this->usuarioHojaVida;
    }

    /**
     * Set usuarioProfesion
     *
     * @param string $usuarioProfesion
     * @return Usuarios
     */
    public function setUsuarioProfesion($usuarioProfesion)
    {
        $this->usuarioProfesion = $usuarioProfesion;
    
        return $this;
    }

    /**
     * Get usuarioProfesion
     *
     * @return string 
     */
    public function getUsuarioProfesion()
    {
        return $this->usuarioProfesion;
    }

    /**
     * Set usuarioPuntos
     *
     * @param float $usuarioPuntos
     * @return Usuarios
     */
    public function setUsuarioPuntos($usuarioPuntos)
    {
        $this->usuarioPuntos = $usuarioPuntos;
    
        return $this;
    }

    /**
     * Get usuarioPuntos
     *
     * @return float 
     */
    public function getUsuarioPuntos()
    {
        return $this->usuarioPuntos;
    }

    /**
     * Set usuarioPerfilProfesional
     *
     * @param string $usuarioPerfilProfesional
     * @return Usuarios
     */
    public function setUsuarioPerfilProfesional($usuarioPerfilProfesional)
    {
        $this->usuarioPerfilProfesional = $usuarioPerfilProfesional;
    
        return $this;
    }

    /**
     * Get usuarioPerfilProfesional
     *
     * @return string 
     */
    public function getUsuarioPerfilProfesional()
    {
        return $this->usuarioPerfilProfesional;
    }

    /**
     * Set usuarioValorMentoria
     *
     * @param float $usuarioValorMentoria
     * @return Usuarios
     */
    public function setUsuarioValorMentoria($usuarioValorMentoria)
    {
        $this->usuarioValorMentoria = $usuarioValorMentoria;
    
        return $this;
    }

    /**
     * Get usuarioValorMentoria
     *
     * @return float 
     */
    public function getUsuarioValorMentoria()
    {
        return $this->usuarioValorMentoria;
    }

    /**
     * Set usuarioCursoActual
     *
     * @param integer $usuarioCursoActual
     * @return Usuarios
     */
    public function setUsuarioCursoActual($usuarioCursoActual)
    {
        $this->usuarioCursoActual = $usuarioCursoActual;
    
        return $this;
    }

    /**
     * Get usuarioCursoActual
     *
     * @return integer 
     */
    public function getUsuarioCursoActual()
    {
        return $this->usuarioCursoActual;
    }

    /**
     * Set usuarioFechaPlaneacion
     *
     * @param \DateTime $usuarioFechaPlaneacion
     * @return Usuarios
     */
    public function setUsuarioFechaPlaneacion($usuarioFechaPlaneacion)
    {
        $this->usuarioFechaPlaneacion = $usuarioFechaPlaneacion;
    
        return $this;
    }

    /**
     * Get usuarioFechaPlaneacion
     *
     * @return \DateTime 
     */
    public function getUsuarioFechaPlaneacion()
    {
        return $this->usuarioFechaPlaneacion;
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
     * Set syncLinkedin
     *
     * @param \DateTime $syncLinkedin
     * @return Usuarios
     */
    public function setSyncLinkedin($syncLinkedin)
    {
        $this->syncLinkedin = $syncLinkedin;
    
        return $this;
    }

    /**
     * Get syncLinkedin
     *
     * @return \DateTime 
     */
    public function getSyncLinkedin()
    {
        return $this->syncLinkedin;
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

    /**
     * Set colegio
     *
     * @param \AT\vocationetBundle\Entity\Colegios $colegio
     * @return Usuarios
     */
    public function setColegio(\AT\vocationetBundle\Entity\Colegios $colegio = null)
    {
        $this->colegio = $colegio;
    
        return $this;
    }

    /**
     * Get colegio
     *
     * @return \AT\vocationetBundle\Entity\Colegios 
     */
    public function getColegio()
    {
        return $this->colegio;
    }

    /**
     * Set georeferencia
     *
     * @param \AT\vocationetBundle\Entity\Georeferencias $georeferencia
     * @return Usuarios
     */
    public function setGeoreferencia(\AT\vocationetBundle\Entity\Georeferencias $georeferencia = null)
    {
        $this->georeferencia = $georeferencia;
    
        return $this;
    }

    /**
     * Get georeferencia
     *
     * @return \AT\vocationetBundle\Entity\Georeferencias 
     */
    public function getGeoreferencia()
    {
        return $this->georeferencia;
    }
}
