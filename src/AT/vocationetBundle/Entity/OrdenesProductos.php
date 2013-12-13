<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrdenesProductos
 *
 * @ORM\Table(name="ordenes_productos")
 * @ORM\Entity
 */
class OrdenesProductos
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
     * @var integer
     *
     * @ORM\Column(name="cantidad", type="integer", nullable=false)
     */
    private $cantidad;

    /**
     * @var float
     *
     * @ORM\Column(name="valor", type="float", nullable=false)
     */
    private $valor;

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
     * @var \Ordenes
     *
     * @ORM\ManyToOne(targetEntity="Ordenes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="orden_id", referencedColumnName="id")
     * })
     */
    private $orden;

    /**
     * @var \Mentorias
     *
     * @ORM\ManyToOne(targetEntity="Mentorias")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mentoria_id", referencedColumnName="id")
     * })
     */
    private $mentoria;



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
     * Set cantidad
     *
     * @param integer $cantidad
     * @return OrdenesProductos
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    
        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set valor
     *
     * @param float $valor
     * @return OrdenesProductos
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    
        return $this;
    }

    /**
     * Get valor
     *
     * @return float 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set producto
     *
     * @param \AT\vocationetBundle\Entity\Productos $producto
     * @return OrdenesProductos
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

    /**
     * Set orden
     *
     * @param \AT\vocationetBundle\Entity\Ordenes $orden
     * @return OrdenesProductos
     */
    public function setOrden(\AT\vocationetBundle\Entity\Ordenes $orden = null)
    {
        $this->orden = $orden;
    
        return $this;
    }

    /**
     * Get orden
     *
     * @return \AT\vocationetBundle\Entity\Ordenes 
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set mentoria
     *
     * @param \AT\vocationetBundle\Entity\Mentorias $mentoria
     * @return OrdenesProductos
     */
    public function setMentoria(\AT\vocationetBundle\Entity\Mentorias $mentoria = null)
    {
        $this->mentoria = $mentoria;
    
        return $this;
    }

    /**
     * Get mentoria
     *
     * @return \AT\vocationetBundle\Entity\Mentorias 
     */
    public function getMentoria()
    {
        return $this->mentoria;
    }
}