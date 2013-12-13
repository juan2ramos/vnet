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
     * @var float
     *
     * @ORM\Column(name="valor_total", type="float", nullable=false)
     */
    private $valorTotal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hora_compra", type="datetime", nullable=false)
     */
    private $fechaHoraCompra;

    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="integer", nullable=false)
     */
    private $estado;

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
     * Set valorTotal
     *
     * @param float $valorTotal
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
     * @return float 
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
}