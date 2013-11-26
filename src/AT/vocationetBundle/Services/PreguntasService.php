<?php
namespace AT\vocationetBundle\Services;

/**
 * Servicio para gestion de preguntas
 * 
 * @author Diego Malagón <diego@altactic.com>
 */
class PreguntasService
{
    var $doctrine;
    var $session;
    var $em;
    
    /**
     * Constructor
     * 
     * @param Object $doctrine
     * @param Object $session
     */
    function __construct($doctrine, $session) 
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->em = $doctrine->getManager();
    }
    
    
    /**
     * Funcion que obtiene todo el contenido de un formulario
     * 
     * Obtiene los sub-formularios, preguntas y opciones de respuesta
     * 
     * @param integer $id id de formulario
     */
    public function getFormulario($id)
    {
        $formularios = array();
        
        // Query para formularios
        $dql = "SELECT
                    f.id,
                    f.nombre,
                    f.numero
                FROM
                    vocationetBundle:Formularios f
                WHERE 
                    f.formulario = :formularioId
                ORDER BY f.numero ASC";
        $query = $this->em->createQuery($dql);
        $query->setParameter('formularioId', $id);
        $result = $query->getResult();
        
        if(count($result))
        {
            // Organizar array con id por key
            $form_ids = array();
            foreach($result as $r)
            {
                $formularios[$r['id']] = $r;
                $form_ids[] = $r['id'];
            }
        
            // Obtener preguntas de formularios
            $preguntas = $this->getPreguntasFormularios($form_ids);
            
            // Agregar preguntas al array de formularios
            foreach($preguntas as $p)
            {
                $formularios[$p['formularioId']]['preguntas'][] = $p;
            }
        }
        return $formularios;
    }
    
    /**
     * Funcion que obtiene las preguntas de varios formularios
     * 
     * Obtiene las preguntas de los id de formulario y los retorna como array
     * 
     * @param array $form_ids arreglo de ids de formularios
     */
    private function getPreguntasFormularios($form_ids)
    {
        $preguntas = array();
        // Query para preguntas
        $dql = "SELECT
                    p.id,
                    p.pregunta,
                    p.numero,
                    pt.id pregunaTipoId,
                    f.id formularioId
                FROM
                    vocationetBundle:Preguntas p
                    JOIN vocationetBundle:PreguntasTipos pt WITH p.preguntaTipo = pt.id
                    JOIN vocationetBundle:Formularios f WITH p.formulario = f.id
                WHERE 
                    (p.formulario = ".implode(" OR p.formulario = ", $form_ids).")
                ORDER BY f.numero, p.numero";
        $query = $this->em->createQuery($dql);
        $result = $query->getResult();
        
        if(count($result))
        {
            // Organizar array con id por key
            $preg_ids = array();
            foreach ($result as $r)
            {
                $preguntas[$r['id']] = $r;
                $preg_ids[] = $r['id'];
            }
        
            // Obtener opciones de respuesta de las preguntas
            $opciones = $this->getOpcionesPreguntas($preg_ids);
            
            // Agregar opciones al array de preguntas
            foreach($opciones as $o)
            {
                $preguntas[$o['preguntaId']]['opciones'][] = $o;
            }
        }
        
        return $preguntas;
    }
    
    /**
     * Funcion para obtener las opciones de varias preguntas
     * 
     * Obtiene las opciones de respuesta de los ids de pregunta y los retorna como array
     * 
     * @param array $pregunta_ids arreglo de ids de preguntas
     */
    private function getOpcionesPreguntas($pregunta_ids)
    {
        // Query para opciones de respuesta
        $dql = "SELECT
                    o.id,
                    o.nombre,
                    o.peso,
                    o.factor,
                    p.id preguntaId
                FROM 
                    vocationetBundle:Opciones o
                    JOIN vocationetBundle:Preguntas p WITH o.pregunta = p.id
                WHERE (o.pregunta = ".implode(" OR o.pregunta = ", $pregunta_ids).")";
        $query = $this->em->createQuery($dql);
        $result = $query->getResult();
        
        return $result;        
    }
    
}
?>
