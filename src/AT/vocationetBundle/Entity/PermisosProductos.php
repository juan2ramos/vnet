<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PermisosProductos
 *
 * @ORM\Table(name="permisos_productos")
 * @ORM\Entity
 */
class PermisosProductos
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
     *   @ORM\JoinColumn(name="permiso_id", referencedColumnName="id")
     * })
     */
    private $permiso;

    /**
     * @var \Productos
     *
     * @ORM\ManyToOne(targetEntity="Productos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="producto_id", referencedColumnName="id")
     * })
     */
    private $producto;



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
     * @param \AT\vocationetBundle\Entity\Permisos $permiso
     * @return PermisosProductos
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
     * Set producto
     *
     * @param \AT\vocationetBundle\Entity\Productos $producto
     * @return PermisosProductos
     */
    public function setProducto(\AT\vocationetBundle\Entity\Productos $producto = null)
    {
        $this->producto = $producto;
    
        return $this;
    }

    /**
     * Get producto
     *
     * @return \AT\vocationetBundle\Entity\Productos 
     */
    public function getProducto()
    {
        return $this->producto;
    }
}