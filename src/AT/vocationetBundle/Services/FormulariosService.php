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
    var $security;
    
    /**
     * Constructor
     * 
     * @param Object $doctrine
     */
    function __construct($doctrine, $security) 
    {
        $this->em = $doctrine->getManager();
        $this->security = $security;
    }
    
    /**
     * Funcion para obtener el id de un formulario
     * 
     * No hace consulta a la base de datos
     * 
     * @param string $nombre nombre del formulario
     * @return integer|boolean id de formulario o false si no existe
     */
    public function getFormId($nombre = false)
    {
        $ids = array(
            'diagnostico' => 1,
            'mentor_experto' => 7,
            'test_vocacional' => 8,
            'evaluacion360' => 9,
            'diseno_vida' => 10,
            'mercado_laboral' => 11,
            'red_mentores' => 12,
            'ponderacion' => 13,
            'universidad' => 14
        );
        
        if($nombre){
            $id = (isset($ids[$nombre])) ? $ids[$nombre] : false;
        }
        else{
            $id = $ids;
        }
        
        
        return $id;
    }
    
    public function getFormName($id)
    {
        $ids = $this->getFormId();
        
        $nombre = array_search($id, $ids);
        
        return $nombre;
        
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
                $formularios[$p['formularioId']]['preguntas'][$p['id']] = $p;
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
                    pt.id preguntaTipoId,
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
                $preguntas[$o['preguntaId']]['opciones'][$o['id']] = $o;
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
     * @param integer $usuarioEvaluadoId id de usuario evaluado en el caso de evaluacion 360
     * @param array $adicionales arreglo con respuestas a preguntas no registradas
     * @param integer $carreraId id de carrera se usa unicamente para formulario de ponderacion
     * @return array arreglo con resultados del procesamiento
     */
    public function procesarFormulario($id, $usuarioId, $respuestas, $usuarioEvaluadoId = false, $adicionales = false, $carreraId = false)
    {
        $preguntas = $this->getListPreguntas($id);
        $validate = $this->validateFormulario($preguntas, $respuestas);
        $puntaje = 0;        
        
        if($validate)
        {
            $puntaje = $this->registrarRespuestas($id, $usuarioId, $preguntas, $respuestas, $usuarioEvaluadoId, $adicionales, $carreraId);
            
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
     * @param integer $id id de formulario principal 
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
        $result = $query->getResult();
        
        // Query para obtener opciones de respuesta de las preguntas segun el tipo
        
        //Crear array con ids de pregunta
        $preg_ids = array();
        $preguntas = array();
        foreach($result as $p)
        {
            $preguntas[$p['id']] = $p;
            if($p['preguntaTipoId'] == 1 || $p['preguntaTipoId'] == 2 || $p['preguntaTipoId'] == 3)
            {
                $preg_ids[] = $p['id'];
            }
        }
        
        if(count($preg_ids)>0)
        {
            $dql = "SELECT
                        o.id,
                        o.peso,
                        p.id preguntaId
                    FROM
                        vocationetBundle:Opciones o
                        JOIN vocationetBundle:Preguntas p WITH o.pregunta = p.id
                    WHERE 
                        (o.pregunta = ". implode("OR o.pregunta = ", $preg_ids) .")";
            $query = $this->em->createQuery($dql);
            $opciones = $query->getResult();
            
            // Agregar opciones al array de preguntas
            if(count($opciones)>0)
            {
                foreach($opciones as $o)
                {
                    $preguntas[$o['preguntaId']]['opciones'][$o['id']] = $o;  
                }
            }            
        }
        
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
     * Funcion para registrar y calificar las respuestas de usuario en base de datos 
     * 
     * @param integer $formId id de formulario principal
     * @param integer $usuarioId id de usuario que participa
     * @param array $preguntas arreglo de preguntas con id y tipo de pregunta
     * @param array $respuestas arreglo de respuestas recibido del formulario enviado
     * @param integer $usuarioEvaluadoId id de usuario evaluado en el caso de evaluacion 360
     * @param array $adicionales arreglo con respuestas a preguntas no registradas
     * @param integer $carreraId id de carrera se usa unicamente para formulario de ponderacion
     * @return integer puntuacion de las respuestas
     */
    private function registrarRespuestas($formId, $usuarioId, $preguntas, $respuestas, $usuarioEvaluadoId = false, $adicionales = false, $carreraId = false)
    {        
        // Registrar participacion        
        if($formId == $this->getFormId('evaluacion360'))
        {
            // Actualizar registro de invitacion
            $usuarioEmail = $this->security->getSessionValue("usuarioEmail");
            $participacion = $this->em->getRepository("vocationetBundle:Participaciones")->findOneBy(array("formulario" => $formId, "correoInvitacion" => $usuarioEmail, "usuarioEvaluado" => $usuarioEvaluadoId));
            if($participacion)
            {
                $participacion->setFecha(new \DateTime());
                $participacion->setUsuarioParticipa($usuarioId);
                $participacion->setEstado(1);
                $this->em->persist($participacion);
            }
        }
        else
        {
            $participacion = new \AT\vocationetBundle\Entity\Participaciones();
            $participacion->setFormulario($formId);
            $participacion->setFecha(new \DateTime());
            $participacion->setUsuarioParticipa($usuarioId);
            $participacion->setUsuarioEvaluado($usuarioId);
            $participacion->setEstado(1);
            if($carreraId)
            {
                $participacion->setCarrera($carreraId);
            }            
            
            $this->em->persist($participacion);
        }
        
        $puntaje = 0;
        
        // Registrar respuestas segun tipo de pregunta
        foreach($preguntas as $preg)
        {
            $respuesta = new \AT\vocationetBundle\Entity\Respuestas();
            
            $respuesta->setParticipacion($participacion);
            $respuesta->setPregunta($preg['id']);
            $res = $respuestas[$preg['id']];            
            
            
            // Asignar respuesta numerica o texto y calcular puntaje segun el tipo de pregunta
            switch($preg['preguntaTipoId'])
            {
                case 1: // Unica respuesta
                {
                    $respuesta->setRespuestaNumerica($res);
                    
                    $valor = 0;                    
                    if(isset($preg['opciones'][$res]['peso']))
                        $valor = $preg['opciones'][$res]['peso'];                    
                    
                    $respuesta->setValor($valor);
                    
                    break;
                }
                case 2: // Multiple respuesta
                {
                    $valor = 0;                    
                    foreach($res as $r)
                    {
                        if(isset($preg['opciones'][$r]['peso']))
                            $valor += $preg['opciones'][$r]['peso'];
                    }
                    
                    $res = implode(",", $res);
                    $respuesta->setRespuestaTexto($res);
                    $respuesta->setValor($valor);
                    break;
                }
                case 3: // Ordenamiento
                {
                    $respuesta->setRespuestaTexto($res);
                    
                    $valor = 0;                    
                    $res = explode(",", $res); 
                    $pos = count($res);
                    
                    foreach($res as $r)
                    {
                        if(isset($preg['opciones'][$r]['peso']))
                        {
                            $peso = $preg['opciones'][$r]['peso'];
                            $valor += $peso * $pos;                            
                        }
                        $pos--;
                    }
                    
                    $respuesta->setValor($valor);
                    break;
                }
                case 4: // Si o no
                {
                    $respuesta->setRespuestaNumerica($res);
                    $valor = $res;
                    $respuesta->setValor($valor);
                    break;
                }
                case 5: // Numerica
                {
                    $respuesta->setRespuestaNumerica($res);
                    $valor = $res;
                    $respuesta->setValor($valor);
                    break;
                }
                case 6: // Porcentual
                {
                    $respuesta->setRespuestaNumerica($res);
                    $valor = $res/100;
                    $respuesta->setValor($valor);
                    break;
                }
                case 7: // slider
                {
                    $respuesta->setRespuestaNumerica($res);
                    $valor = $res;
                    $respuesta->setValor($valor);
                    break;
                }
                case 8: // Abierta
                {
                    $respuesta->setRespuestaTexto($res);
                    $valor = 0;
                    $respuesta->setValor($valor);
                    break;
                }
            }
            
            $puntaje += $valor;
            
            $this->em->persist($respuesta);
        }
        
        
        if($adicionales)
        {
            foreach($adicionales as $key => $a)
            {
                $json = json_encode($a);
                
                $adicional = new \AT\vocationetBundle\Entity\RespuestasAdicionales();
                $adicional->setParticipacion($participacion);
                $adicional->setRespuestaKey($key);
                $adicional->setRespuestaJson($json);
                
                $this->em->persist($adicional);
            }
        }
        
        
        
        $this->em->flush();
        
        return $puntaje;
    }
    
    /**
     * Funcion que trae todo el contenido de un formulario con las respuestas
     * 
     * @param integer $formId id de formulario principal
     * @param integer $usuarioEvaluadoId id de usuario evaluado
     * @return array arreglo de formularios, preguntas, opciones y respuestas
     */
    public function getResultadosFormulario($formId, $usuarioEvaluadoId)
    {
        $formularios = $this->getFormulario($formId);        
        
        /*
         * SELECT f.id, p.id, r.respuesta_numerica, r.respuesta_texto, par.usuario_participa_id FROM respuestas r
         * JOIN preguntas p ON r.pregunta_id = p.id
         * JOIN formularios f ON p.formulario_id = f.id
         * JOIN participaciones par ON r.participacion_id = par.id
         * WHERE f.formulario_id = 9
         * AND par.usuario_evaluado_id = 6
         * AND par.estado = 1;
         */        
        
        $dql = "SELECT
                    f.id formularioId,
                    p.id preguntaId,
                    r.respuestaNumerica,
                    r.respuestaTexto,
                    c.nombre carreraNombre
                FROM 
                    vocationetBundle:Respuestas r
                    JOIN vocationetBundle:Preguntas p WITH r.pregunta = p.id
                    JOIN vocationetBundle:Formularios f WITH p.formulario = f.id
                    JOIN vocationetBundle:Participaciones par WITH r.participacion = par.id
                    LEFT JOIN vocationetBundle:Carreras c WITH par.carrera = c.id
                WHERE
                    f.formulario = :formId
                    AND par.usuarioEvaluado = :usuarioEvaluadoId
                    AND par.estado = 1
                ";
        $query = $this->em->createQuery($dql);
        $query->setParameter('formId', $formId);
        $query->setParameter('usuarioEvaluadoId', $usuarioEvaluadoId);
        $result = $query->getResult();
        
        foreach($result as $r)
        {
            $tipoPreguntaId = $formularios[$r['formularioId']]['preguntas'][$r['preguntaId']]['preguntaTipoId'];
            $respuesta = null;
            
            
            // Obtener respuesta segun el tipo de pregunta
            switch($tipoPreguntaId)
            {
                case 1: // Unica respuesta
                {
                    $respuesta = $formularios[$r['formularioId']]['preguntas'][$r['preguntaId']]['opciones'][$r['respuestaNumerica']]['nombre'];
                    break;
                }
                case 2: // Multiple respuesta
                case 3: // Ordenacion
                {
                    $ids = explode(",", $r['respuestaTexto']);
                    
                    foreach($ids as $i)
                    {
                        $respuesta[] = $formularios[$r['formularioId']]['preguntas'][$r['preguntaId']]['opciones'][$i]['nombre'];
                    }
                    
                    break;
                }
                case 4: // Si o no
                case 5: // Numerica
                case 6: // Porcentual
                case 7: // Slider
                {
                    $respuesta = $r['respuestaNumerica'];
                    break;
                }
                case 8: // Abierta
                {
                    $respuesta = $r['respuestaTexto'];
                    break;
                }
            }
            
            if($formId == $this->getFormId('ponderacion'))
            {
                $formularios[$r['formularioId']]['preguntas'][$r['preguntaId']]['respuestas'][] = array('valor' => $respuesta, 'carrera' => $r['carreraNombre']);
            }
            else
            {
                $formularios[$r['formularioId']]['preguntas'][$r['preguntaId']]['respuestas'][] = $respuesta;                
            }
        }
        
        
        return $formularios;
    }
    
    /**
     * Funcion que trae las participaciones en un formulario para un usuario
     * 
     * @param integer $formId id de formulario principal
     * @param integer $usuarioEvaluadoId id de usuario evaluado
     * @return array arreglo de participaciones
     */
    public function getParticipacionesFormulario($formId, $usuarioEvaluadoId)
    {
        $dql = "SELECT
                    p.fecha,
                    p.estado,
                    p.correoInvitacion,
                    u.usuarioNombre,
                    u.usuarioApellido,
                    u.id usuarioId,
                    c.nombre carreraNombre
                FROM
                    vocationetBundle:Participaciones p
                    LEFT JOIN vocationetBundle:Usuarios u WITH p.usuarioParticipa = u.id
                    LEFT JOIN vocationetBundle:Carreras c WITH p.carrera = c.id
                WHERE
                    p.formulario = :formId
                    AND p.usuarioEvaluado = :usuarioEvaluadoId
                ";
        $query = $this->em->createQuery($dql);
        $query->setParameter('formId', $formId);
        $query->setParameter('usuarioEvaluadoId', $usuarioEvaluadoId);
        $result = $query->getResult();
        
        return $result;
    }
    
    /**
     * Funcion para obtener las respuestas adicionales de un formulario par un usuario
     * 
     * @param integer $formId id de formulario principal
     * @param integer $usuarioEvaluadoId id de usuario evaluado
     * @return array arreglo de respuestas adicionales
     */
    public function getRespuestasAdicionalesParticipacion($formId, $usuarioEvaluadoId)
    {
        $dql = "SELECT 
                    ra.respuestaKey,
                    ra.respuestaJson
                FROM 
                    vocationetBundle:RespuestasAdicionales ra
                    JOIN vocationetBundle:Participaciones p WITH ra.participacion = p.id
                WHERE 
                    p.usuarioEvaluado = :usuarioEvaluadoId
                    AND p.formulario = :formId";
        $query = $this->em->createQuery($dql);
        $query->setParameter('usuarioEvaluadoId', $usuarioEvaluadoId);
        $query->setParameter('formId', $formId);
        $result = $query->getResult();
 
        $respuestas = array();
        
        
        foreach($result as $r)
        {
            $respuestas[$r['respuestaKey']][] = json_decode($r['respuestaJson'], true);
        }
        
        // Simplificar array para los casos que aplique
        foreach ($respuestas as $k => $r)
        {
            if(count($r)==1)
            {
                $respuestas[$k] = $r[0];
            }
        }
        
        return $respuestas;
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
                    p.usuarioEvaluado, 
                    u.usuarioNombre,
                    u.usuarioApellido
                FROM 
                    vocationetBundle:Participaciones p
                    JOIN vocationetBundle:Usuarios u WITH u.id = p.usuarioEvaluado
                WHERE 
                    (p.usuarioParticipa = :usuarioId
                    OR p.correoInvitacion = :usuarioEmail)
                    AND p.estado = 0";
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
