<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador para diseño de vida
 * 
 * El id para este formulario principal es 10
 *
 * @Route("/disenovida")
 * @author Diego Malagón <diego@altactic.com>
 */
class DisenoVidaController extends Controller
{
    /**
     * Index de diseño de vida
     * 
     * @Route("/", name="diseno_vida")
     * @Template("vocationetBundle:DisenoVida:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $usuarioId = $security->getSessionValue("id");
        
        // Verificar pago
        $pago = $this->verificarPago($usuarioId);        
        if(!$pago)
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("no.existe.pago"), "text" => $this->get('translator')->trans("antes.de.continuar.debes.realizar.el.pago")));
            return $this->redirect($this->generateUrl('planes'));
        }

        $return = $this->get('perfil')->validarPosicionActual($usuarioId, 'disenovida');
		if (!$return['status']) {
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("Acceso denegado"), "text" => $this->get('translator')->trans($return['message'])));
			return $this->redirect($this->generateUrl($return['redirect']));
		}
        
        $formularios_serv = $this->get('formularios');
        $form_id = $formularios_serv->getFormId('diseno_vida');

        //Validar acceso a diseño de vida
		$em = $this->getDoctrine()->getManager();
        $participacion = $em->getRepository("vocationetBundle:Participaciones")->findOneBy(array("formulario" => $form_id, "usuarioParticipa" => $usuarioId));
        if($participacion)
        {
            return $this->forward("vocationetBundle:Alerts:alertScreen", array(
                "title" => $this->get('translator')->trans("cuestionario.ya.ha.sido.enviado"),
                "message" => $this->get('translator')->trans("gracias.por.participar.diseno.vida"),
				"file" => true,
                "path" => $participacion->getArchivoReporte(),
            )); 
        }

        $formulario = $formularios_serv->getInfoFormulario($form_id);
        $formularios = $formularios_serv->getFormulario($form_id);
        $form = $this->createFormCuestionario();
        
        return array(
            'formulario_info' => $formulario,
            'formularios' => $formularios,
            'form' => $form->createView(),
        );
    }
        
    /**
     * Accion para porcesar el formulario de diseño de vida
     * 
     * @Route("/procesar", name="procesar_disenovida")
     * @Method({"POST"})
     * @return Response
     */
    public function procesarAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $form = $this->createFormCuestionario();
        $form_id = $this->get('formularios')->getFormId('diseno_vida');
        $usuarioId = $security->getSessionValue("id");
        
        $em = $this->getDoctrine()->getManager();
        
        //Validar acceso a diseño de vida
        $participacion = $em->getRepository("vocationetBundle:Participaciones")->findOneBy(array("formulario" => $form_id, "usuarioParticipa" => $usuarioId));
        if($participacion)
        {
            return $this->forward("vocationetBundle:Alerts:alertScreen", array(
                "title" => $this->get('translator')->trans("cuestionario.ya.ha.sido.enviado"),
                "message" => $this->get('translator')->trans("gracias.por.participar.diseno.vida")
            )); 
        }
        
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $respuestas = $request->get('pregunta');
                $adicional = $request->get('adicional');
                
//                $security->debug($respuestas);
//                $security->debug($adicional);
                
                $formularios_serv = $this->get('formularios');
                
                //procesar formulario
                $resultados = $formularios_serv->procesarFormulario($form_id, $usuarioId, $respuestas, false, $adicional);
                
                if($resultados['validate'])
                {
                    // Enviar notificacion al mentor
                    $this->enviarNotificacionMentor($usuarioId);

					$this->get('perfil')->actualizarpuntos('universidad', $usuarioId);
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("cuestionario.enviado"), "text" => $this->get('translator')->trans("diseno.vida.enviado.ahora.separar.metoria")));
                    return $this->redirect($this->generateUrl('lista_mentores_ov'));
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
                    return $this->redirect($this->generateUrl('diseno_vida'));
                }
            }
        }
        
        return new Response();
    }
    
    /**
     * Accion para ver las respuestas del diseño de vida de un usuario
     * 
     * A esta accion solo tiene acceso el mentor del usuario
     * 
     * @Route("/{id}/resultados", name="disenovida_resultados")
     * @Template("vocationetBundle:DisenoVida:resultados.html.twig")
     * @param integer $id id de usuario evaluado
     * @return Response
     */
    public function resultadosAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $FormServ = $this->get('formularios');
        $form_id = $FormServ->getFormId('diseno_vida');
        $usuarioId = $security->getSessionValue('id');
        $rolId = $security->getSessionValue('rolId');
        
        // Valida acceso del mentor
        if(!$FormServ->validateAccesoMentor($usuarioId, $rolId, $id)) throw $this->createNotFoundException();     
        
        $formularios_serv = $this->get('formularios');
        $formularios = $formularios_serv->getResultadosFormulario($form_id, $id);
        $participaciones = $formularios_serv->getParticipacionesFormulario($form_id, $id);
        
        // Obtener respuestas adicionales
        $adicionales = $formularios_serv->getRespuestasAdicionalesParticipacion($form_id, $id);
        
//        $security->debug($adicionales);
        
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
     * Funcion que envia un mensaje al mentor cuando se participa en el diseño de vida
     * 
     * @param integer $usuarioEvaluadoId id de usuario evaluado
     */
    private function enviarNotificacionMentor($usuarioEvaluadoId)
    {
        //Obtener mentor del usuario
        $mentorId = $this->get('formularios')->getMentorId($usuarioEvaluadoId);
                
        if($mentorId)
        {
            $usuarioNombre = $this->get('security')->getSessionValue('usuarioNombre')." ".$this->get('security')->getSessionValue('usuarioApellido');
            
            // Enviar mensaje
            
            $subject = $this->get('translator')->trans("diseno.vida.de.%usu%.finalizada", array('%usu%' => $usuarioNombre), 'mail');
            $link = '<a href="'. $this->get('request')->getSchemeAndHttpHost().$this->generateUrl('disenovida_resultados', array('id' => $usuarioEvaluadoId)) .'" >'.$this->get('translator')->trans("resultados.diseno.vida", array(), 'label').'</a><br/><br/>';
            $body = $this->get('translator')->trans("mensaje.diseno.vida.de.%usu%.finalizada", array('%usu%' => $usuarioNombre), 'mail')
                    ."<br/><br/>".$link."";
            
            $this->get("mensajes")->enviarMensaje($usuarioEvaluadoId, array($mentorId), $subject, $body);        
        }
        
    }
    
    /**
     * Funcion que verifica el pago para diseño de vida
     * 
     * @param integer $usuarioId id de usuario
     * @return boolean
     */
    private function verificarPago($usuarioId)
    {
        $pagoCompleto = $this->get('pagos')->verificarPagoProducto($this->get('pagos')->getProductoId('programa_orientacion'), $usuarioId);
        
        if($pagoCompleto)
            return true;
        else
            return false;
    }
}

?>
