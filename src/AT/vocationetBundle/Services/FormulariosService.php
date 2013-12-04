<?php
namespace AT\vocationetBundle\Services;

/**
 * Servicio para gestion de preguntas
 * 
 * @author Diego Malagón <diego@altactic.com>
 */
class FormulariosService
{
    var $em;
    
    /**
     * Constructor
     * 
     * @param Object $doctrine
     */
    function __construct($doctrine) 
    {
        $this->em = $doctrine->getManager();
    }
    
    /**
     * Funcion para obtener el id de un formulario
     * 
     * No hace consulta a la base de datos
     * 
     * @param string $nombre nombre del formulario
     * @return integer|boolean id de formulario o false si no existe
     */
    public function getFormId($nombre)
    {
        $ids = array(
            'diagnostico' => 1,
            'evaluacion360' => 9,
        );
        
        $id = (isset($ids[$nombre])) ? $ids[$nombre] : false;
        
        return $id;
    }
    
    /**
     * Funcion para obtener la entidad de un formulario principal
     * 
     * @param integer $id id de formulario
     * @return Object entidad de formulario
     */
    public function getInfoFormulario($id)
    {
        // Query para obtener informacion del formulario
        $formulario = $this->em->getRepository("vocationetBundle:Formularios")->findOneById($id);
        
        return $formulario;
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
        
        // Query para sub-formularios
        $dql = "SELECT
                    f.id,
                    f.nombre,
                    f.descripcion,
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
                    pt.nombre preguntaTipo,
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
    
    /**
     * Funcion que valida, registra y obtiene resultados de un cuestionario
     * 
     * @param integer $id id de formulario principal
     * @param integer $usuarioId id de usuario que responde
     * @param array $respuestas arreglo de respuestas recibido del formulario enviado
     * @return array arreglo con resultados del procesamiento
     */
    public function procesarFormulario($id, $usuarioId, $respuestas)
    {
        $preguntas = $this->getListPreguntas($id);
        $validate = $this->validateFormulario($preguntas, $respuestas);
        $puntaje = 0;        
        
        if($validate)
        {
            $puntaje = $this->registrarRespuestas($usuarioId, $preguntas, $respuestas);
        }
        return array(
            'validate' => $validate,
            'puntaje' => $puntaje
        );
    }
    
    /**
     * Funcion para obtener listado de preguntas
     * 
     * Obtiene un arreglo de preguntas con id y tipo de pregunta
     * 
     * @param integer $id id de formulario principal     * 
     * @return array arreglo de preguntas
     */
    private function getListPreguntas($id)
    {
        // Query para obtener preguntas del formulario
        $dql = "SELECT
                    p.id,
                    pt.id preguntaTipoId
                FROM 
                    vocationetBundle:Preguntas p
                    JOIN vocationetBundle:PreguntasTipos pt WITH p.preguntaTipo = pt.id
                    JOIN vocationetBundle:Formularios f WITH p.formulario = f.id
                WHERE 
                    f.formulario = :formularioId";
        $query = $this->em->createQuery($dql);
        $query->setParameter('formularioId', $id);
        $preguntas = $query->getResult();
        
        return $preguntas;
    }
    
    /**
     * Funcion para validar un cuestionario 
     * 
     * Valida todas las respuestas segun el tipo de pregunta
     * 
     * @param array $preguntas arreglo de preguntas con id y tipo de pregunta
     * @param array $respuestas arreglo de respuestas recibido del formulario enviado
     * @return boolean true si pasa la validacion false en caso contrario
     */
    private function validateFormulario($preguntas, $respuestas)
    {
        $validate = false;
        
        //Validar cada respuesta segun el tipo de pregunta
        $errors = 0;
        foreach($preguntas as $preg)
        {
            if(isset($respuestas[$preg['id']]))
            {
                $respuesta = $respuestas[$preg['id']];
                
                switch($preg['preguntaTipoId'])
                {
                    case 1: // Unica respuesta
                    {
                        // Validar que sea un id
                        if(is_numeric($respuesta))
                        {
                            $respuesta = (int)$respuesta;
                            if($respuesta == 0)
                                $errors ++;
                        }
                        else $errors ++;
                        break;
                    }
                    case 2: // Multiple respuesta
                    {
                        // Validar que sea un array de ids
                        if(is_array($respuesta))
                        {
                            if(count($respuesta) > 0)
                            {
                                foreach($respuesta as $r)
                                {
                                    if((int)$r == 0)
                                        $errors ++;
                                }
                            }
                            else $errors ++;
                        }
                        else $errors ++;
                        break;
                    }
                    case 3: // Ordenamiento
                    {
                        // Validar que se pueda separar por comas y que los valores resultantes sean ids
                        $respuesta = explode(',', $respuesta);
                        if(count($respuesta) >= 2)
                        {
                            foreach($respuesta as $r)
                            {
                                if((int)$r == 0)
                                    $errors ++;
                            }
                        }
                        else $errors ++;
                        
                        break;
                    }
                    case 4: // Si o no
                    {
                        if($respuesta != 0 && $respuesta != 1)
                            $errors ++;
                        break;
                    }
                    case 5: // Numerica
                    case 7: // slider
                    {
                        // Validar que sea un numero
                        if(!is_numeric($respuesta))
                            $errors ++;
                        break;
                    }
                    case 6: // Porcentual
                    {
                        // Validar que sea un numero entre 0 y 100
                        if(is_numeric($respuesta))
                        {
                            if($respuesta < 0 || $respuesta > 100)
                                $errors ++;  
                        }
                        else $errors ++;
                        
                        break;
                    }
                    case 8: // Abierta
                    {
                        // Validar que sea un string
                        if(!is_string($respuesta))
                            $errors ++;
                        break;
                    }
                }
            }
            else
            {
                $errors ++;
            }
        }
        
        if($errors == 0)
        {
            $validate = true;
        }
        
        
        return $validate;
    }
    
    /**
     * Funcion para registrar y califica las respuestas de usuario en base de datos
     * 
     * @param array $preguntas arreglo de preguntas con id y tipo de pregunta
     * @param array $respuestas arreglo de respuestas recibido del formulario enviado
     * @return integer puntuacion de las respuestas
     */
    private function registrarRespuestas($usuarioId, $preguntas, $respuestas)
    {
        $puntaje = 0;
        
        // Registrar respuestas segun tipo de pregunta
        foreach($preguntas as $preg)
        {
            $respuesta = new \AT\vocationetBundle\Entity\PreguntasUsuarios();
            
            $respuesta->setUsuario($usuarioId);
            $respuesta->setPregunta($preg['id']);
            
            $res = $respuestas[$preg['id']];
            
            // Asignar respuesta numerica o texto y calcular puntaje segun el tipo de pregunta+
            
            switch($preg['preguntaTipoId'])
            {
                case 1: // Unica respuesta
                {
                    $respuesta->setRespuestaNumerica($res);
                    break;
                }
                case 2: // Multiple respuesta
                {
                    $res = implode(",", $res);
                    $respuesta->setRespuestaTexto($res);
                    break;
                }
                case 3: // Ordenamiento
                {
                    $respuesta->setRespuestaTexto($res);
                    break;
                }
                case 4: // Si o no
                {
                    $respuesta->setRespuestaNumerica($res);
                    break;
                }
                case 5: // Numerica
                {
                    $respuesta->setRespuestaNumerica($res);
                    break;
                }
                case 6: // Porcentual
                {
                    $respuesta->setRespuestaNumerica($res);
                    break;
                }
                case 7: // slider
                {
                    $respuesta->setRespuestaNumerica($res);
                    break;
                }
                case 8: // Abierta
                {
                    $respuesta->setRespuestaTexto($res);
                    break;
                }
            }
            
//            $this->em->persist($respuesta);
            
            
        }
        
//        $this->em->flush();
        $puntaje = 100;
        
        return $puntaje;
    }
    
    /**
     * Funcion que busca invitaciones al usuario a evaluacioes 360
     * 
     * @param integer $usuarioId id de usuario logueado
     * @param string $usuarioEmail email de usuario logueado
     * @return array|boolean arreglo de invitaciones o false si no encuentra ninguna 
     */
    public function getInvitacionesEvaluacion360($usuarioId, $usuarioEmail)
    {
        $return = false;
        $dql = "SELECT 
                    uf.usuarioEvaluado, 
                    u.usuarioNombre,
                    u.usuarioApellido
                FROM 
                    vocationetBundle:UsuariosFormularios uf
                    JOIN vocationetBundle:Usuarios u WITH u.id = uf.usuarioEvaluado
                WHERE 
                    (uf.usuarioResponde = :usuarioId
                    OR uf.correoInvitacion = :usuarioEmail)
                    AND uf.estado = 0";
        $query = $this->em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $query->setParameter('usuarioEmail', $usuarioEmail);
        $result = $query->getResult();
        
        if(count($result)>=1)
        {
            $return = $result;
        }
        
        return $return;
    }
}
?>
