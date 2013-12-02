<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador para la evaluacion 360
 * 
 * El id para este formulario principal es 9
 *
 * @Route("/evaluacion360")
 * @author Diego Malagón <diego@altactic.com>
 */
class Evaluacion360Controller extends Controller
{
    /**
     * Index de la evaluacion 360
     * 
     * Aca el usuario ingresa los correos de las personas que invita
     * para que contesten su evaluacion 360
     * 
     * @Route("/", name="evaluacion360")
     * @Template("vocationetBundle:Evaluacion360:index.html.twig")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
//        
        $formularios_serv = $this->get('formularios');
        $form_id = 9;
        
        $form = $this->createFormBuilder()
            ->add('emails', 'text', array('required' => true))
            ->getForm();
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $data = $form->getData();
                
                $emails = $this->validateEmails($data);
                
                if(count($emails) < 3)
                {
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("emails.invalidos"), "text" => $this->get('translator')->trans("debe.seleccionar.mas.emails")));
                }
                else
                {
                    $usuarioId = $security->getSessionValue("id");
                    
                    $this->enviarInvitaciones($emails, $usuarioId);
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("invitaciones.enviadas"), "text" => $this->get('translator')->trans("invitaciones.enviadas.correctamente")));
                }
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
            }
        }
        
        
        $formulario = $formularios_serv->getInfoFormulario($form_id);
        
        return array(
            'formulario_info' => $formulario,
            'form' => $form->createView(),
        );
    }
    
    /**
     * Accion para cuestionario de la evaluacion 360
     *      
     * @Route("/{id}/evaluacion", name="evaluacion360_evaluacion")
     * @Template("vocationetBundle:Evaluacion360:cuestionario.html.twig")
     * @return Response
     */
    public function evaluacionAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $em = $this->getDoctrine()->getManager();
        
        $formularios_serv = $this->get('formularios');
        $form_id = 9;
        
        $formulario = $formularios_serv->getInfoFormulario($form_id);
        $usuario = $em->getRepository("vocationetBundle:Usuarios")->findOneById($id);
        
        
        return array(
            'usuario' => $usuario,
            'formulario_info' => $formulario,
        );
    }
    
    
    /**
     * Funcion que valida la lista de correos del formulario
     * @param array $data
     * @return array|boolean 
     */
    private function validateEmails($data)
    {
        $validate = $this->get('validate');
        $val = false;
            
        $emails = $data['emails'];        
        
        foreach($emails as $email)
        {
           if($validate->validateEmail($email))
           {
               $val[] = $email;
           }
        }
                
        return $val;
    }
    
    /**
     * Funcion para enviar invitaciones a usuarios
     * 
     * Envio de invitaciones para evaluacion 360
     * 
     * @param array $emails arreglo de correos
     * @param integer $usuarioId id de usuario que envia la invitacion
     */
    private function enviarInvitaciones($emails, $usuarioId)
    {
        $em = $this->getDoctrine()->getManager();
        
        $usuario = $em->getRepository("vocationetBundle:Usuarios")->findOneById($usuarioId);
        
        $usuarioNombre = $usuario->getUsuarioNombre() . ' ' . $usuario->getUsuarioApellido();
        
        $subject = $this->get('translator')->trans("%usu%.invito.contestar.evaluacion.360", array('%usu%' => $usuarioNombre), 'mail');
        $body = $this->get('translator')->trans("%usu%.invito.contestar.evaluacion.360.body", array('%usu%' => $usuarioNombre), 'mail');;
        
        $link = $this->get('request')->getSchemeAndHttpHost().$this->get('router')->generate('evaluacion360_evaluacion', array("id" => $usuarioId)); 
        $dataRender = array(
            'title' => $subject,
            'body' => $body,
            'link' => $link,
            'link_text' => $this->get('translator')->trans("participar.evaluacion", array(), 'mail')
        );
        
        $this->get('mail')->sendMail($emails, $subject, $dataRender, 'bcc');
    }
}