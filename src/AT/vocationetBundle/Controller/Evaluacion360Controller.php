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
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $formularios_serv = $this->get('formularios');
        $form_id = $this->get('formularios')->getFormId('evaluacion360');
        
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
     * @param integer $id id de usuario a evaluar
     * @return Response
     */
    public function evaluacionAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $em = $this->getDoctrine()->getManager();
        
        $formularios_serv = $this->get('formularios');
        $form_id = $this->get('formularios')->getFormId('evaluacion360');
        
        //Validar acceso de usuario
        $usuarioEmail = $security->getSessionValue("usuarioEmail");
        $acceso = $this->validarAcceso($usuarioEmail, $id);        
        if(!$acceso)
        {
            return $this->forward("vocationetBundle:Alerts:alertScreen", array(
                "title" => $this->get('translator')->trans("no.tiene.invitacion"),
                "message" => $this->get('translator')->trans("solicita.invitacion.usuario")
            ));            
        }
        elseif($acceso->getEstado() == 1)
        {
            return $this->forward("vocationetBundle:Alerts:alertScreen", array(
                "title" => $this->get('translator')->trans("cuestionario.ya.ha.sido.enviado"),
                "message" => $this->get('translator')->trans("gracias.por.participar.evaluacion.360")
            ));                        
        }
                
        $formulario = $formularios_serv->getInfoFormulario($form_id);
        $usuario = $em->getRepository("vocationetBundle:Usuarios")->findOneById($id);
        $formularios = $formularios_serv->getFormulario($form_id);
        $form = $this->createFormCuestionario();
        
        return array(
            'usuario' => $usuario,
            'formulario_info' => $formulario,
            'formularios' => $formularios,
            'form' => $form->createView(),
        );
    }
       
    /**
     * Accion para procesar cuestionario de la evaluacion 360
     *      
     * @Route("/{id}/procesar", name="procesar_evaluacion360")
     * @Method({"POST"})
     * @param Request $request 
     * @param integer $id id de usuario a evaluar
     * @return Response
     */
    public function procesarEvaluacion360Action(Request $request, $id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $form = $this->createFormCuestionario();
        $form_id = $this->get('formularios')->getFormId('evaluacion360');
        
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $respuestas = $request->get('pregunta');
                //$security->debug($respuestas);
                
                $formularios_serv = $this->get('formularios');
                
                $usuarioId = $security->getSessionValue('id');
                
                //Validar formulario
                $resultados = $formularios_serv->procesarFormulario($form_id, $usuarioId, $respuestas);
                
                if($resultados['validate'])
                {
                    $em = $this->getDoctrine()->getManager();
                    
                    // Actualizar registro UsuariosFormularios
                    $usuarioEmail = $security->getSessionValue("usuarioEmail");
                    $usuarioFormulario = $em->getRepository("vocationetBundle:UsuariosFormularios")->findOneBy(array("formulario" => $form_id, "correoInvitacion" => $usuarioEmail, "usuarioEvaluado" => $id));
                    if($usuarioFormulario)
                    {
                        $usuarioFormulario->setUsuarioResponde($usuarioId);
                        $usuarioFormulario->setEstado(1);
                        $em->persist($usuarioFormulario);
                        $em->flush();
                    }
                    
                    // Enviar email de agradecimiento
                    $subject = $this->get('translator')->trans("gracias.participar.mi.evaluacion.360");
                    $body = $this->get('translator')->trans("mensaje.gracias.participar.mi.evaluacion.360")
                            ."<br/><br/>
                              Cordialmente,<br/>
                              Vocationet";
                    $this->get("mensajes")->enviarMensaje($id, array($usuarioId), $subject, $body);
                    
                    
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("cuestionario.enviado"), "text" => $this->get('translator')->trans("gracias.por.participar.evaluacion.360")));
                    return $this->redirect($this->generateUrl('homepage'));
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
                    return $this->redirect($this->generateUrl('evaluacion360_evaluacion', array("id" => $id)));
                }
                
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
                return $this->redirect($this->generateUrl('evaluacion360_evaluacion', array("id" => $id)));
            }
        }
        
        return new Response();
    }

    /**
     * Funcion que valida la lista de correos del formulario
     * @param array $data
     * @return array|boolean 
     */
    private function validateEmails($data)
    {
        $validate = $this->get('validate');
        $val = array();
        $usuarioEmail = $this->get('security')->getSessionValue("usuarioEmail");
        
        
        $emails = $data['emails'];        
        
        foreach($emails as $email)
        {
           if($validate->validateEmail($email))
           {
               if($email != $usuarioEmail)
               {
                   $val[] = $email;
               }
           }
        }
                
        return array_unique($val);
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
        $form_id = $this->get('formularios')->getFormId('evaluacion360');
        
        $em = $this->getDoctrine()->getManager();
        
        $usuario = $em->getRepository("vocationetBundle:Usuarios")->findOneById($usuarioId);        
        $usuarioNombre = $usuario->getUsuarioNombre() . ' ' . $usuario->getUsuarioApellido();        
        
        // Envio de email
        $subject = $this->get('translator')->trans("%usu%.invito.contestar.evaluacion.360", array('%usu%' => $usuarioNombre), 'mail');
        $body = $this->get('translator')->trans("%usu%.invito.contestar.evaluacion.360.body", array('%usu%' => $usuarioNombre), 'mail');
        
        $link = $this->get('request')->getSchemeAndHttpHost().$this->get('router')->generate('evaluacion360_evaluacion', array("id" => $usuarioId)); 
        $dataRender = array(
            'title' => $subject,
            'body' => $body,
            'link' => $link,
            'link_text' => $this->get('translator')->trans("participar.evaluacion", array(), 'mail')
        );
        
        $this->get('mail')->sendMail($emails, $subject, $dataRender, 'bcc');
        
        // Registro de invitaciones en la db        
        foreach($emails as $email)
        {
            // Validar que no exista la invitacion
            $usuarioFormulario = $em->getRepository("vocationetBundle:UsuariosFormularios")->findOneBy(array("formulario" => $form_id, "usuarioEvaluado" => $usuarioId, "correoInvitacion" => $email));
            
            if(!$usuarioFormulario)
            {
                // Registrar invitacion
                $usuarioFormulario = new \AT\vocationetBundle\Entity\UsuariosFormularios();
                $usuarioFormulario->setFormulario($form_id);
                $usuarioFormulario->setCorreoInvitacion($email);
                $usuarioFormulario->setEstado(0);
                $usuarioFormulario->setUsuarioEvaluado($usuarioId);

                $em->persist($usuarioFormulario);
            }
        }        
        $em->flush();
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
     * Funcion que valida si un usuario ha sido invitado a la evaluacion 360 de otro usuario
     * 
     * @param integer $usuarioRespondeId id de usuario que ingresa
     * @param integer $usuarioEvaluadoId id de usuario a evaluar
     * @return Object entidad UsuariosFormularios
     */
    private function validarAcceso($usuarioRespondeEmail, $usuarioEvaluadoId)
    {
        $form_id = $this->get('formularios')->getFormId('evaluacion360');
        $em = $this->getDoctrine()->getManager();
        
        $usuarioFormulario = $em->getRepository("vocationetBundle:UsuariosFormularios")->findOneBy(array("formulario" => $form_id, "correoInvitacion" => $usuarioRespondeEmail, "usuarioEvaluado" => $usuarioEvaluadoId));
        
        return $usuarioFormulario;
    }
    
}