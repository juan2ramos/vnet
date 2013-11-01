<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * controlador para envio y recepcion de mensajes
 *
 * @Route("/mensajes")
 * @author Diego Malagón <diego@altactic.com>
 */
class MensajesController extends Controller
{
    /**
     * Accion para el modulo de mensajes
     * 
     * @Route("/", name="mensajes")
     * @Template("vocationetBundle:Mensajes:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $form = $this->createMensajeForm();
        
        
        
        return array(
            'form' => $form->createView(),
        );
    }
    
    /**
     * Accion para enviar mensajes
     * 
     * @Route("/enviar_mensajes", name="enviar_mensajes")
     * @Method({"POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return Response
     */
    public function enviarMensajeAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $form = $this->createMensajeForm();
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $data = $form->getData();
                
                if($this->validateMensajeForm($data))
                {
                    $mensaje_serv = $this->get('mensajes');
                    $usuarioId = $security->getSessionValue('id');
                    
                    if($mensaje_serv->enviarMensaje($usuarioId, $data['to'], $data['subject'], $data['message'], $data['attachment']))
                    {
                        $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("mensaje.enviado"), "text" => $this->get('translator')->trans("mensaje.enviado.correctamente")));
                    }
                    else
                    {
                        $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.envio"), "text" => $this->get('translator')->trans("ocurrio.error.envio.mensaje")));
                    }
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
                }
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
            }
        }       
                
        return $this->redirect($this->generateUrl('mensajes'));
    }
    
    /**
     * Funcion para crear formulario de mensaje
     * 
     * @return Object formulario
     */
    private function createMensajeForm()
    {
        $mensajes = $this->get('mensajes');
        $security = $this->get('security');
        
        $usuarioId = $security->getSessionValue('id');
        $usuarios = $mensajes->getToList($usuarioId);        
        
        $dataForm = array(
            'to' => null, 
            'subject' => null,
            'message' => null,
            'attachment' => null
        );
        $form = $this->createFormBuilder($dataForm)
           ->add('to', 'choice', array('required' => true, 'choices' => $usuarios, 'expanded' => false, 'multiple' => true))
           ->add('subject', 'text', array('required' => true))
           ->add('message', 'textarea', array('required' => true))
           ->add('attachment', 'file', array('required' => false))
           ->getForm();
        
        return $form;
    }
   
    /**
     * Funcion para validar el formulario de mensajes
     * 
     * @param array $dataForm arreglo con los datos del formulario
     * @return boolean
     */
    private function validateMensajeForm($dataForm)
    {
        $validate = false;
        
        if(count($dataForm['to']) > 0 )
        {   
            if(!empty($dataForm['subject']) && !empty($dataForm['message']))
            {
                $validate = true;
            }
        }
        return $validate;
    }
    
    /**
     * Accion ajax para obtener mensajes
     * 
     * @Route("/get/{tipo}/{estado}", name="get_mensajes", defaults={"tipo"=2, "estado"=null})
     * @Template("vocationetBundle:Mensajes:mensajes.html.twig")
     * @param integer $tipo tipo de mensaje, 1: enviado, 2:recibido
     * @param integer $estado estado de mensaje, 0: sin leer, 1: leido, 2:eliminado
     */
    public function mensajesAction($tipo, $estado)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $mensajes_serv = $this->get('mensajes');
        $usuarioId = $security->getSessionValue('id');
        
        $mensajes = $mensajes_serv->getMensajes($usuarioId, $tipo, $estado);
        
//        $security->debug($mensajes);
        
        
        
        return array(
            'mensajes' => $mensajes,
        );        
    }
    
    /**
     * Accion ajax para mostrar el contenido de un mensaje
     * 
     * @Route("/mensaje/{mid}", name="show_mensaje")
     * @Template("vocationetBundle:Mensajes:show.html.twig")
     * @param integer $mid id de mensaje
     */
    public function showAction($mid)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $mensajes_serv = $this->get('mensajes');
        $usuarioId = $security->getSessionValue('id');
        
        $mensaje = $mensajes_serv->getMensaje($usuarioId, $mid);
        
        // marcar mensaje como leido
        $mensajes_serv->updateEstadoMensaje($usuarioId, $mid, 1);
        
        
        return array(
            'mid' => $mid,
            'mensaje' => $mensaje['mensaje'],
            'toList' => $mensaje['toList'],
            'adjuntos' => $mensaje['adjuntos']
        );
    }
    
    
    /**
     * Accion ajax para obtener la cantidad de mensajes sin leer
     * 
     * @Route("/count", name="count_mensajes")
     * @return string contador en formato json
     */
    public function countAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
                
        $mensajes_serv = $this->get('mensajes');
        $usuarioId = $security->getSessionValue('id');
        
        $count = $mensajes_serv->countMensajesSinLeer($usuarioId);
        
        print(json_encode(array(
            'mensajes_sin_leer' => $count
        )));
        
        return new Response();
    }
}
?>
