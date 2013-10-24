<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ColegiosPerfiles
 *
 * @ORM\Table(name="colegios_perfiles")
 * @ORM\Entity
 */
class ColegiosPerfiles
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
     * @var \Colegios
     *
     * @ORM\ManyToOne(targetEntity="Colegios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="colegio_id", referencedColumnName="id")
     * })
     */
    private $colegio;

    /**
     * @var \Perfiles
     *
     * @ORM\ManyToOne(targetEntity="Perfiles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="perfil_id", referencedColumnName="id")
     * })
     */
    private $perfil;



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
     * Set colegio
     *
     * @param \AT\vocationetBundle\Entity\Colegios $colegio
     * @return ColegiosPerfiles
     */
    public function setColegio(\AT\vocationetBundle\Entity\Colegios $colegio = null)
    {
        $this->colegio = $colegio;
    
        return $this;
    }

    /**
     * Get colegio
     *
     * @return \AT\vocationetBundle\Entity\Colegios 
     */
    public function getColegio()
    {
        return $this->colegio;
    }

    /**
     * Set perfil
     *
     * @param \AT\vocationetBundle\Entity\Perfiles $perfil
     * @return ColegiosPerfiles
     */
    public function setPerfil(\AT\vocationetBundle\Entity\Perfiles $perfil = null)
    {
        $this->perfil = $perfil;
    
        return $this;
    }

    /**
     * Get perfil
     *
     * @return \AT\vocationetBundle\Entity\Perfiles 
     */
    public function getPerfil()
    {
        return $this->perfil;
    }
}