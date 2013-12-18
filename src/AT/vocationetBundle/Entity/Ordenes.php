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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hora_compra", type="datetime", nullable=false)
     */
    private $fechaHoraCompra;

    /**
     * @var float
     *
     * @ORM\Column(name="subtotal", type="float", nullable=false)
     */
    private $subtotal;

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float", nullable=false)
     */
    private $iva;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", nullable=false)
     */
    private $total;

    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="integer", nullable=false)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="usuario_id", type="integer", nullable=false)
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
     * Set subtotal
     *
     * @param float $subtotal
     * @return Ordenes
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
    
        return $this;
    }

    /**
     * Get subtotal
     *
     * @return float 
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set iva
     *
     * @param float $iva
     * @return Ordenes
     */
    public function setIva($iva)
    {
        $this->iva = $iva;
    
        return $this;
    }

    /**
     * Get iva
     *
     * @return float 
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set total
     *
     * @param float $total
     * @return Ordenes
     */
    public function setTotal($total)
    {
        $this->total = $total;
    
        return $this;
    }

    /**
     * Get total
     *
     * @return float 
     */
    public function getTotal()
    {
        return $this->total;
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
     * Set usuario
     *
     * @param integer $usuario
     * @return Ordenes
     */
    public function setUsuario($usuario = null)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    /**
     * Get usuario
     *
     * @return integer
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}