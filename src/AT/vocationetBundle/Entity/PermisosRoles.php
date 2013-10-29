<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PermisosRoles
 *
 * @ORM\Table(name="permisos_roles")
 * @ORM\Entity
 */
class PermisosRoles
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
     * @var \Permisos
     *
     * @ORM\ManyToOne(targetEntity="Permisos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="permisos_id", referencedColumnName="id")
     * })
     */
    private $permiso;

    /**
     * @var \Roles
     *
     * @ORM\ManyToOne(targetEntity="Roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roles_id", referencedColumnName="id")
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
     * Set permiso
     *
     * @param \AT\vocationetBundle\Entity\Permisos $permisos
     * @return PermisosRoles
     */
    public function setPermiso(\AT\vocationetBundle\Entity\Permisos $permiso = null)
    {
        $this->permiso = $permiso;
    
        return $this;
    }

    /**
     * Get permiso
     *
     * @return \AT\vocationetBundle\Entity\Permisos 
     */
    public function getPermiso()
    {
        return $this->permiso;
    }

    /**
     * Set rol
     *
     * @param \AT\vocationetBundle\Entity\Roles $roles
     * @return PermisosRoles
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