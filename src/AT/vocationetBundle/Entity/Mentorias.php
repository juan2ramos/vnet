<?php

namespace AT\vocationetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mentorias
 *
 * @ORM\Table(name="mentorias")
 * @ORM\Entity
 */
class Mentorias
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
     * @ORM\Column(name="mentoria_inicio", type="datetime", nullable=false)
     */
    private $mentoriaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mentoria_fin", type="datetime", nullable=false)
     */
    private $mentoriaFin;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mentoria_estado", type="boolean", nullable=true)
     */
    private $mentoriaEstado;

    /**
     * @var \Usuarios
     * 
     * @ORM\Column(name="usuario_mentor_id", type="integer", nullable=false)
     */
    private $usuarioMentor;

    /**
     * @var \Usuarios
     * 
     * @ORM\Column(name="usuario_estudiante_id", type="integer", nullable=true)
     */
    private $usuarioEstudiante;



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
     * Set mentoriaInicio
     *
     * @param \DateTime $mentoriaInicio
     * @return Mentorias
     */
    public function setMentoriaInicio($mentoriaInicio)
    {
        $this->mentoriaInicio = $mentoriaInicio;
    
        return $this;
    }

    /**
     * Get mentoriaInicio
     *
     * @return \DateTime 
     */
    public function getMentoriaInicio()
    {
        return $this->mentoriaInicio;
    }

    /**
     * Set mentoriaFin
     *
     * @param \DateTime $mentoriaFin
     * @return Mentorias
     */
    public function setMentoriaFin($mentoriaFin)
    {
        $this->mentoriaFin = $mentoriaFin;
    
        return $this;
    }

    /**
     * Get mentoriaFin
     *
     * @return \DateTime 
     */
    public function getMentoriaFin()
    {
        return $this->mentoriaFin;
    }

    /**
     * Set mentoriaEstado
     *
     * @param boolean $mentoriaEstado
     * @return Mentorias
     */
    public function setMentoriaEstado($mentoriaEstado)
    {
        $this->mentoriaEstado = $mentoriaEstado;
    
        return $this;
    }

    /**
     * Get mentoriaEstado
     *
     * @return boolean 
     */
    public function getMentoriaEstado()
    {
        return $this->mentoriaEstado;
    }

    /**
     * Set usuarioMentor
     *
     * @param \AT\vocationetBundle\Entity\Usuarios $usuarioMentor
     * @return Mentorias
     */
    public function setUsuarioMentor($usuarioMentor = null)
    {
        $this->usuarioMentor = $usuarioMentor;
    
        return $this;
    }

    /**
     * Get usuarioMentor
     *
     * @return \AT\vocationetBundle\Entity\Usuarios 
     */
    public function getUsuarioMentor()
    {
        return $this->usuarioMentor;
    }

    /**
     * Set usuarioEstudiante
     *
     * @param \AT\vocationetBundle\Entity\Usuarios $usuarioEstudiante
     * @return Mentorias
     */
    public function setUsuarioEstudiante($usuarioEstudiante = null)
    {
        $this->usuarioEstudiante = $usuarioEstudiante;
    
        return $this;
    }

    /**
     * Get usuarioEstudiante
     *
     * @return \AT\vocationetBundle\Entity\Usuarios 
     */
    public function getUsuarioEstudiante()
    {
        return $this->usuarioEstudiante;
    }
}