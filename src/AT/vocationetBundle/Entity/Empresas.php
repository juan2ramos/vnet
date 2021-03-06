<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Empresas
 *
 * @ORM\Table(name="empresas")
 * @ORM\Entity
 */
class Empresas
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
     * @ORM\Column(name="nombre", type="string", length=155, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=45, nullable=true)
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=45, nullable=true)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="industria", type="string", length=45, nullable=true)
     */
    private $industria;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_linkedin", type="integer", nullable=true)
     */
    private $idLinkedin;



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
     * @return Empresas
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
     * Set tipo
     *
     * @param string $tipo
     * @return Empresas
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set size
     *
     * @param string $size
     * @return Empresas
     */
    public function setSize($size)
    {
        $this->size = $size;
    
        return $this;
    }

    /**
     * Get size
     *
     * @return string 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set industria
     *
     * @param string $industria
     * @return Empresas
     */
    public function setIndustria($industria)
    {
        $this->industria = $industria;
    
        return $this;
    }

    /**
     * Get industria
     *
     * @return string 
     */
    public function getIndustria()
    {
        return $this->industria;
    }

    /**
     * Set idLinkedin
     *
     * @param integer $idLinkedin
     * @return Empresas
     */
    public function setIdLinkedin($idLinkedin)
    {
        $this->idLinkedin = $idLinkedin;
    
        return $this;
    }

    /**
     * Get idLinkedin
     *
     * @return integer 
     */
    public function getIdLinkedin()
    {
        return $this->idLinkedin;
    }
}