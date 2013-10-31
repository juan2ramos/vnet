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
        
        return new Response();
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
        );
        $form = $this->createFormBuilder($dataForm)
           ->add('to', 'choice', array('required' => true, 'choices' => $usuarios, 'expanded' => false, 'multiple' => true))
           ->add('subject', 'text', array('required' => true))
           ->add('message', 'textarea', array('required' => true))
           ->getForm();
        
        return $form;
    }
}
?>
