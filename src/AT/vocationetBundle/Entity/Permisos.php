<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Permisos
 *
 * @ORM\Table(name="permisos")
 * @ORM\Entity
 */
class Permisos
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
     * @ORM\Column(name="nombre", type="string", length=60, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="permiso_routes", type="text", nullable=true)
     */
    private $permisoRoutes;

    /**
     * @var string
     *
     * @ORM\Column(name="identificador", type="string", length=45, nullable=false)
     */
    private $identificador;

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
     * Set nombre
     *
     * @param string $nombre
     * @return Permisos
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Permisos
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set permisoRoutes
     *
     * @param string $permisoRoutes
     * @return Permisos
     */
    public function setPermisoRoutes($permisoRoutes)
    {
        $this->permisoRoutes = $permisoRoutes;
    
        return $this;
    }

    /**
     * Get permisoRoutes
     *
     * @return string 
     */
    public function getPermisoRoutes()
    {
        return $this->permisoRoutes;
    }

    /**
     * Set identificador
     *
     * @param string $identificador
     * @return Permisos
     */
    public function setIdentificador($identificador)
    {
        $this->identificador = $identificador;
    
        return $this;
    }

    /**
     * Get identificador
     *
     * @return string 
     */
    public function getIdentificador()
    {
        return $this->identificador;
    }

    /**
     * Set rol
     *
     * @param \AT\vocationetBundle\Entity\Roles $rol
     * @return Permisos
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