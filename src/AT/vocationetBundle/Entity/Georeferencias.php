<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Georeferencias
 *
 * @ORM\Table(name="georeferencias")
 * @ORM\Entity
 */
class Georeferencias
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
     * @ORM\Column(name="georeferencia_padre_id", type="integer", nullable=true)
     */
    private $georeferenciaPadreId;

    /**
     * @var string
     *
     * @ORM\Column(name="georeferencia_nombre", type="string", length=100, nullable=true)
     */
    private $georeferenciaNombre;

    /**
     * @var string
     *
     * @ORM\Column(name="georeferencia_tipo", type="string", length=70, nullable=true)
     */
    private $georeferenciaTipo;

    /**
     * @var string
     *
     * @ORM\Column(name="georeferencia_codigo", type="string", length=45, nullable=true)
     */
    private $georeferenciaCodigo;



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
     * Set georeferenciaPadreId
     *
     * @param integer $georeferenciaPadreId
     * @return Georeferencias
     */
    public function setGeoreferenciaPadreId($georeferenciaPadreId)
    {
        $this->georeferenciaPadreId = $georeferenciaPadreId;
    
        return $this;
    }

    /**
     * Get georeferenciaPadreId
     *
     * @return integer 
     */
    public function getGeoreferenciaPadreId()
    {
        return $this->georeferenciaPadreId;
    }

    /**
     * Set georeferenciaNombre
     *
     * @param string $georeferenciaNombre
     * @return Georeferencias
     */
    public function setGeoreferenciaNombre($georeferenciaNombre)
    {
        $this->georeferenciaNombre = $georeferenciaNombre;
    
        return $this;
    }

    /**
     * Get georeferenciaNombre
     *
     * @return string 
     */
    public function getGeoreferenciaNombre()
    {
        return $this->georeferenciaNombre;
    }

    /**
     * Set georeferenciaTipo
     *
     * @param string $georeferenciaTipo
     * @return Georeferencias
     */
    public function setGeoreferenciaTipo($georeferenciaTipo)
    {
        $this->georeferenciaTipo = $georeferenciaTipo;
    
        return $this;
    }

    /**
     * Get georeferenciaTipo
     *
     * @return string 
     */
    public function getGeoreferenciaTipo()
    {
        return $this->georeferenciaTipo;
    }

    /**
     * Set georeferenciaCodigo
     *
     * @param string $georeferenciaCodigo
     * @return Georeferencias
     */
    public function setGeoreferenciaCodigo($georeferenciaCodigo)
    {
        $this->georeferenciaCodigo = $georeferenciaCodigo;
    
        return $this;
    }

    /**
     * Get georeferenciaCodigo
     *
     * @return string 
     */
    public function getGeoreferenciaCodigo()
    {
        return $this->georeferenciaCodigo;
    }
}