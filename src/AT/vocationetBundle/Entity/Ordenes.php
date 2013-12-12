<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ordenes
 *
 * @ORM\Table(name="ordenes")
 * @ORM\Entity
 */
class Ordenes
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
     * @ORM\Column(name="cantidad", type="integer", nullable=true)
     */
    private $cantidad;

    /**
     * @var integer
     *
     * @ORM\Column(name="valor_total", type="integer", nullable=true)
     */
    private $valorTotal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hora_compra", type="datetime", nullable=true)
     */
    private $fechaHoraCompra;

    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="integer", nullable=true)
     */
    private $estado;

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
     * @var \Usuarios
     *
     * @ORM\ManyToOne(targetEntity="Usuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;

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
     * @return Ordenes
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
     * Set valorTotal
     *
     * @param integer $valorTotal
     * @return Ordenes
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
    
        return $this;
    }

    /**
     * Get valorTotal
     *
     * @return integer 
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    /**
     * Set fechaHoraCompra
     *
     * @param \DateTime $fechaHoraCompra
     * @return Ordenes
     */
    public function setFechaHoraCompra($fechaHoraCompra)
    {
        $this->fechaHoraCompra = $fechaHoraCompra;
    
        return $this;
    }

    /**
     * Get fechaHoraCompra
     *
     * @return \DateTime 
     */
    public function getFechaHoraCompra()
    {
        return $this->fechaHoraCompra;
    }

    /**
     * Set estado
     *
     * @param integer $estado
     * @return Ordenes
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
     * Set producto
     *
     * @param \AT\vocationetBundle\Entity\Productos $producto
     * @return Ordenes
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
     * Set usuario
     *
     * @param \AT\vocationetBundle\Entity\Usuarios $usuario
     * @return Ordenes
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
     * Set mentoria
     *
     * @param \AT\vocationetBundle\Entity\Mentorias $mentoria
     * @return Ordenes
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