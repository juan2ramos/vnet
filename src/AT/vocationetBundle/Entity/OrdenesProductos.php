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
     * @ORM\Column(name="mentor_id", type="integer", nullable=true)
     */
    private $mentorId;

    /**
     * @var float
     *
     * @ORM\Column(name="valor", type="float", nullable=false)
     */
    private $valor;

    /**
     * @var integer
     *
     * @ORM\Column(name="estado", type="integer", nullable=false)
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
     * @var \Ordenes
     *
     * @ORM\ManyToOne(targetEntity="Ordenes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="orden_id", referencedColumnName="id")
     * })
     */
    private $orden;



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
     * Set mentorId
     *
     * @param integer $mentorId
     * @return OrdenesProductos
     */
    public function setMentorId($mentorId)
    {
        $this->mentorId = $mentorId;
    
        return $this;
    }

    /**
     * Get mentorId
     *
     * @return integer 
     */
    public function getMentorId()
    {
        return $this->mentorId;
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
     * Set estado
     *
     * @param integer $estado
     * @return OrdenesProductos
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
}