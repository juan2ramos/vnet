<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador para ponderacion
 * 
 * El id para este formulario principal es 13
 *
 * @Route("/ponderacion")
 * @author Diego Malagón <diego@altactic.com>
 */
class PonderacionController extends Controller
{
    /**
     * Index de ponderacion
     * 
     * @Route("/", name="ponderacion")
     * @Template("vocationetBundle:Ponderacion:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $formularios_serv = $this->get('formularios');
        $perfil_serv = $this->get('perfil');
        $form_id = $this->get('formularios')->getFormId('ponderacion');
        $usuarioId = $security->getSessionValue("id");
        
        $em = $this->getDoctrine()->getManager();
        
        //Validar acceso a ponderacion
        $participacion = $em->getRepository("vocationetBundle:Participaciones")->findOneBy(array("formulario" => $form_id, "usuarioParticipa" => $usuarioId));
        if($participacion)
        {
            return $this->forward("vocationetBundle:Alerts:alertScreen", array(
                "title" => $this->get('translator')->trans("cuestionario.ya.ha.sido.enviado"),
                "message" => $this->get('translator')->trans("gracias.por.participar.ponderacion")
            )); 
        }
        
		$alternativasEstudio = $perfil_serv->getAlternativasEstudio($usuarioId);
        
        // Validar que ya tenga alternativas de estudio
        if(count($alternativasEstudio) == 0)
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("Acceso denegado"), "text" => $this->get('translator')->trans("no.ha.seleccionado.alternativas.estudio")));
			return $this->redirect($this->generateUrl('mercado_laboral'));
        }
        
        $formulario = $formularios_serv->getInfoFormulario($form_id);
        $formularios = $formularios_serv->getFormulario($form_id);
        
        $form = $this->createFormCuestionario();
        
        return array(
            'formulario_info' => $formulario,
            'formularios' => $formularios,
            'alternativas' => $alternativasEstudio,
            'form' => $form->createView(),
        );
    }
    
    /**
     * Accion para procesar el formulario de ponderacion
     * 
     * @Route("/procesar", name="procesar_ponderacion")
     * @Template("vocationetBundle:Ponderacion:resultado.html.twig")
     * @Method({"POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function procesarAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $form = $this->createFormCuestionario();
        $formularios_serv = $this->get('formularios');
        $form_id = $this->get('formularios')->getFormId('ponderacion');
        $usuarioId = $security->getSessionValue("id");
        
        $em = $this->getDoctrine()->getManager();
        
        //Validar acceso a ponderacion
        $participacion = $em->getRepository("vocationetBundle:Participaciones")->findOneBy(array("formulario" => $form_id, "usuarioParticipa" => $usuarioId));
        if($participacion)
        {
            return $this->forward("vocationetBundle:Alerts:alertScreen", array(
                "title" => $this->get('translator')->trans("cuestionario.ya.ha.sido.enviado"),
                "message" => $this->get('translator')->trans("gracias.por.participar.ponderacion")
            )); 
        }
        
        
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $respuestas = $request->get('pregunta');
                
                // Procesar formulario por cada alternativa de carrera como una participacion independiente
                $puntajes = array();
                $ids = array();
                foreach($respuestas as $carreraId => $res)
                {
                    $formularios_serv->procesarFormulario($form_id, $usuarioId, $res, false, false, $carreraId);
                    
                    $ids[] = $carreraId;
                    
                    // Calcular puntaje por alternativa
                    $puntajes[$carreraId] = 0;
                    foreach($res as $r)
                    {
                        $puntajes[$carreraId] += $r;
                    }
                }
                
                $carreras = $this->getCarreras($ids);
                foreach($carreras as $k => $carrera)
                {
                    $carreras[$carrera['id']]['puntaje'] = $puntajes[$carrera['id']];
                }
                
                $carreras = $this->orderMultiDimensionalArray($carreras, 'puntaje', true);
                
                // Enviar notificacion al mentor
                $this->enviarNotificacionMentor($usuarioId);
                
                return array(
                    'carrera' => $carreras[0]['nombre'],
                    'mensaje' => $this->get('translator')->trans("%carrera%.alternativa.mas.opcionada", array('%carrera%' => 'test')),
                    'carreras' => $carreras
                );
            }
        }
        
        return new Response();
    }

    /**
     * Accion para ver las respuestas de ponderacion de un usuario
     * 
     * A esta accion solo tiene acceso el mentor del usuario
     * 
     * @Route("/{id}/resultados", name="ponderacion_resultados")
     * @Template("vocationetBundle:Ponderacion:resultados.html.twig")
     * @param integer $id id de usuario evaluado
     * @return Response
     */
    public function resultadosAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $form_id = $this->get('formularios')->getFormId('ponderacion');
        $usuarioId = $security->getSessionValue('id');
        
        // Valida acceso del mentor
        if($usuarioId != $this->getMentorId($id))
        {
            $rolId = $security->getSessionValue('rolId');            
            if($rolId != 4)
            {
                throw $this->createNotFoundException();            
            }
        } 
        
        $formularios_serv = $this->get('formularios');
        $formularios = $formularios_serv->getResultadosFormulario($form_id, $id);
        $participaciones = $formularios_serv->getParticipacionesFormulario($form_id, $id);
        
//        $security->debug($formularios);
        
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository("vocationetBundle:Usuarios")->findOneById($id);
        
        $puntuacion = $this->getPuntajePonderacion($id);
        
        return array(
            'formularios' => $formularios,
            'participaciones' => $participaciones,
            'usuario' => $usuario,
            'puntuacion' => $puntuacion
        );        
    }    

    /**
     * Funcion para crear un formulario vacio para cuestionarios
     * 
     * Se usa unicamente para proteccion csrf
     * 
     * @return Object formulario
     */
    private function createFormCuestionario()
    {
        $form = $this->createFormBuilder()
            ->getForm();
        
        return $form;
    }
    
    /**
     * Funcion para obtener carreras por array de ids
     * 
     * @param array $ids arreglo de ids de carreras
     * @return array arreglo de carreras
     */
    private function getCarreras($ids)
    {
        $dql = "SELECT c.id, c.nombre FROM vocationetBundle:Carreras c WHERE (c.id = ".implode(" OR c.id = ", $ids).")";
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $result = $query->getResult();
        
        $carreras = array();
        foreach($result as $r)
        {
            $carreras[$r['id']] = $r;
        }
        
        return $carreras;
    }
    
    /**
     * Funcion para ordenar una array multidimensional
     * 
     * @param array $toOrderArray
     * @param string $field
     * @param boolean $inverse
     * @return array
     */
    private function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false) {  
        $position = array();  
        $newRow = array();  
        foreach ($toOrderArray as $key => $row) {  
                $position[$key]  = $row[$field];  
                $newRow[$key] = $row;  
        }  
        if ($inverse) {  
            arsort($position);  
        }  
        else {  
            asort($position);  
        }  
        $returnArray = array();  
        foreach ($position as $key => $pos) {       
            $returnArray[] = $newRow[$key];  
        }  
        return $returnArray;  
    } 
    
    /**
     * Funcion que obtiene el id del mentor de un usuario
     * 
     * @param integer $usuarioEvaluadoId id de usuario evaluado
     * @return integer id de mentor
     */
    private function getMentorId($usuarioEvaluadoId)
    {
        $em = $this->getDoctrine()->getManager();                
        
        //Obtener mentor del usuario
        $dql = "SELECT 
                    u1.id usuario1Id, 
                    u2.id usuario2Id
                FROM 
                    vocationetBundle:Relaciones r 
                    JOIN vocationetBundle:Usuarios u1 WITH r.usuario = u1.id
                    JOIN vocationetBundle:Usuarios u2 WITH r.usuario2 = u2.id
                WHERE 
                    (r.usuario = :usuarioId OR r.usuario2 = :usuarioId)
                    AND r.tipo = 2
                    AND r.estado = 1
                ";
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioEvaluadoId);
        $query->setMaxResults(1);
        $result = $query->getResult();
        
        $mentorId = false;
        if(isset($result[0]))
        {
            $result = $result[0];
            
            if($result['usuario1Id'] == $usuarioEvaluadoId)
            {
                $mentorId = $result['usuario2Id'];
            }
            elseif($result['usuario2Id'] == $usuarioEvaluadoId)
            {
                $mentorId = $result['usuario1Id'];
            }            
        }
        
        return $mentorId;
    }
    
    /**
     * Funcion para obtener la puntuacion por cada alternativa de carrera de un estudiante
     * 
     * @param integer $usuarioId id de usuario
     * @return array arreglo de puntuacion
     */
    private function getPuntajePonderacion($usuarioId)
    {
        $formId = $this->get('formularios')->getFormId('ponderacion');
        $dql = "SELECT 
                    SUM(r.respuestaNumerica) puntaje,
                    c.nombre
                FROM
                    vocationetBundle:Respuestas r
                    JOIN vocationetBundle:Participaciones p WITH r.participacion = p.id
                    LEFT JOIN vocationetBundle:Carreras c WITH p.carrera = c.id
                WHERE 
                    p.formulario = :formId
                    AND p.usuarioEvaluado = :usuarioId
                GROUP BY p.id";
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter("formId", $formId);
        $query->setParameter("usuarioId", $usuarioId);
        $result = $query->getResult();
        
        return $result;
    }
    
    /**
     * Funcion que envia un mensaje al mentor cuando se participa en el diseño de vida
     * 
     * @param integer $usuarioEvaluadoId id de usuario evaluado
     */
    private function enviarNotificacionMentor($usuarioEvaluadoId)
    {
        //Obtener mentor del usuario
        $mentorId = $this->getMentorId($usuarioEvaluadoId);
                
        if($mentorId)
        {
            $usuarioNombre = $this->get('security')->getSessionValue('usuarioNombre')." ".$this->get('security')->getSessionValue('usuarioApellido');
            
            // Enviar mensaje
            
            $subject = $this->get('translator')->trans("ponderacion.de.%usu%.finalizada", array('%usu%' => $usuarioNombre), 'mail');
            $link = '<a href="'. $this->get('request')->getSchemeAndHttpHost().$this->generateUrl('ponderacion_resultados', array('id' => $usuarioEvaluadoId)) .'" >'.$this->get('translator')->trans("resultados.ponderacion", array(), 'label').'</a><br/><br/>';
            $body = $this->get('translator')->trans("mensaje.ponderacion.de.%usu%.finalizada", array('%usu%' => $usuarioNombre), 'mail')
                    ."<br/><br/>".$link."";
            
            $this->get("mensajes")->enviarMensaje($usuarioEvaluadoId, array($mentorId), $subject, $body);        
        }
        
    }
}
?>
