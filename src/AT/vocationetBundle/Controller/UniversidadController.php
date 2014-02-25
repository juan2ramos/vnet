<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador para universidad
 * 
 * El id para este formulario principal es 14
 *
 * @Route("/universidad")
 * @author Diego Malagón <diego@altactic.com>
 */
class UniversidadController extends Controller
{
    /**
     * Index de universidad
     * 
     * @Route("", name="universidad")
     * @Template("vocationetBundle:Universidad:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        
        $usuarioId = $security->getSessionValue("id");
        
        // Verificar pago
        $pago = $this->verificarPago($usuarioId);        
        if(!$pago)
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("no.existe.pago"), "text" => $this->get('translator')->trans("antes.de.continuar.debes.realizar.el.pago")));
            return $this->redirect($this->generateUrl('planes'));
        }
        
        // Validar posicion en el proceso
        //...
        
        $formularios_serv = $this->get('formularios');
        $form_id = $this->get('formularios')->getFormId('universidad');
        
        
        $formulario = $formularios_serv->getInfoFormulario($form_id);
        $formularios = $formularios_serv->getFormulario($form_id);
        $form = $this->createFormCuestionario();
        $ciudades = $this->getCiudades();
        $alternativas = $this->getAlternativasEstudio($usuarioId); // Alternativas de estudio
        
        return array(
            'formulario_info' => $formulario,
            'formularios' => $formularios,
            'form' => $form->createView(),
            'ciudades' => $ciudades,
            'alternativas' => $alternativas
        );
    }
    
    /**
     * Accion que recibe y procesa el cuestionario de universidad
     * 
     * @Route("/procesar", name="procesar_universidad")
     * @Method({"POST"})
     * @param \AT\vocationetBundle\Controller\Request $request
     */
    public function procesarCuestionarioAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $form = $this->createFormCuestionario();
        $form_id = $this->get('formularios')->getFormId('universidad');
        $usuarioId = $security->getSessionValue("id");
        
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $respuestas = $request->get('pregunta');
                $adicional = $request->get('adicional');
                
                $formularios_serv = $this->get('formularios');
                
                //procesar formulario
                $resultados = $formularios_serv->procesarFormulario($form_id, $usuarioId, $respuestas, false, $adicional);
                
                if($resultados['validate'])
                {
                    // Enviar notificacion al mentor
                    $this->enviarNotificacionMentor($usuarioId);

					$this->get('perfil')->actualizarpuntos('universidad', $usuarioId);
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("cuestionario.enviado"), "text" => $this->get('translator')->trans("prueba.universidad.finalizada")));
                    return $this->redirect($this->generateUrl('universidad'));
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
                    return $this->redirect($this->generateUrl('universidad'));
                }
            }
        }        
        
        return new Response();
    }
    
    /**
     * Accion para ver las respuestas de universidad
     * 
     * A esta accion solo tiene acceso el mentor del usuario
     * 
     * @Route("/{id}/resultados", name="universidad_resultados")
     * @Template("vocationetBundle:Universidad:resultados.html.twig")
     * @param integer $id id de usuario evaluado
     * @return Response
     */
    public function resultadosAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $form_id = $this->get('formularios')->getFormId('universidad');
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
        
        // Obtener respuestas adicionales
        $adicionales = $formularios_serv->getRespuestasAdicionalesParticipacion($form_id, $id);
        
        
        $security->debug($adicionales);
        
        die;
        
        
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository("vocationetBundle:Usuarios")->findOneById($id);
        
        return array(
            'formularios' => $formularios,
            'participaciones' => $participaciones,
            'usuario' => $usuario,
            'adicionales' => $adicionales
        );        
    }
    
    /**
     * Funcion que obtiene las alternativas de estudio del usuario
     * 
     * @param integer $usuario_id id de usuario
     * @return array arreglo de carreras
     */
    private function getAlternativasEstudio($usuario_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $dql = "SELECT c.nombre FROM vocationetBundle:AlternativasEstudios e
                JOIN vocationetBundle:Carreras c WITH c.id = e.carrera
                WHERE e.usuario = :usuario_id";
        
        $query = $em->createQuery($dql);
        $query->setParameter('usuario_id', $usuario_id);
        $result = $query->getResult();
        
        return $result;
    }    
    
    /**
     * Funcion para obtener un listado de ciudades
     * 
     * @return array arreglo de ciudades
     */
    private function getCiudades()
    {
        $em = $this->getDoctrine()->getManager();
        
        $dql = "SELECT g.georeferenciaNombre
                FROM vocationetBundle:Georeferencias g
                WHERE g.georeferenciaPadreId = 1 AND g.georeferenciaTipo = 'ciudad'";
        
        $query = $em->createQuery($dql);
        $result = $query->getResult();
        
        return $result;
    }
    
    /**
     * Funcion que envia un mensaje al mentor
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
            
            $subject = $this->get('translator')->trans("%usu%.ha.descrito.universidad", array('%usu%' => $usuarioNombre), 'mail');
            $link = '<a href="'. $this->get('request')->getSchemeAndHttpHost().$this->generateUrl('universidad_resultados', array('id' => $usuarioEvaluadoId)) .'" >'.$this->get('translator')->trans("resultados.universidad", array(), 'label').'</a><br/><br/>';
            $body = $this->get('translator')->trans("mensaje.universidad.descrita.%usu%", array('%usu%' => $usuarioNombre), 'mail')
                    ."<br/><br/>".$link."";
            
            $this->get("mensajes")->enviarMensaje($usuarioEvaluadoId, array($mentorId), $subject, $body);        
        }
        
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
     * Funcion que verifica el pago para universidad
     * 
     * @param integer $usuarioId id de usuario
     * @return boolean
     */
    private function verificarPago($usuarioId)
    {
        $pagoCompleto = $this->get('pagos')->verificarPagoProducto($this->get('pagos')->getProductoId('programa_orientacion'), $usuarioId);
        if($pagoCompleto)
        {
            return true;
        }
        else
        {
            $productoId = $this->get('pagos')->getProductoId('universidades'); // Producto: universidades
            $pagoIndividual = $this->get('pagos')->verificarPagoProducto($productoId, $usuarioId);
            
            if($pagoIndividual)
                return true;
            else
                return false;
        }
    }
}