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
    private $permisos;

    /**
     * @var \Roles
     *
     * @ORM\ManyToOne(targetEntity="Roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roles_id", referencedColumnName="id")
     * })
     */
    private $roles;



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
     * Set permisos
     *
     * @param \AT\vocationetBundle\Entity\Permisos $permisos
     * @return PermisosRoles
     */
    public function setPermisos(\AT\vocationetBundle\Entity\Permisos $permisos = null)
    {
        $this->permisos = $permisos;
    
        return $this;
    }

    /**
     * Get permisos
     *
     * @return \AT\vocationetBundle\Entity\Permisos 
     */
    public function getPermisos()
    {
        return $this->permisos;
    }

    /**
     * Set roles
     *
     * @param \AT\vocationetBundle\Entity\Roles $roles
     * @return PermisosRoles
     */
    public function setRoles(\AT\vocationetBundle\Entity\Roles $roles = null)
    {
        $this->roles = $roles;
    
        return $this;
    }

    /**
     * Get roles
     *
     * @return \AT\vocationetBundle\Entity\Roles 
     */
    public function getRoles()
    {
        return $this->roles;
    }
}