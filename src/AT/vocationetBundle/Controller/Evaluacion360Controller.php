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
        
        $usuarioId = $security->getSessionValue("id");
        
        // Verificar pago
        $pago = $this->verificarPago($usuarioId);        
        if(!$pago)
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("no.existe.pago"), "text" => $this->get('translator')->trans("antes.de.continuar.debes.realizar.el.pago")));
            return $this->redirect($this->generateUrl('planes'));
        }
                
        
        $formularios_serv = $this->get('formularios');
        $form_id = $this->get('formularios')->getFormId('evaluacion360');
        
        // Validar si ya selecciono mentor ov
        $seleccionarMentor = $this->get('perfil')->confirmarMentorOrientacionVocacional($usuarioId);
        if(!$seleccionarMentor) {
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("Acceso denegado"), "text" => $this->get('translator')->trans("no.ha.seleccionado.mentor.ov")));
			return $this->redirect($this->generateUrl('lista_mentores_ov'));
		}
        
        
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
                    $this->enviarInvitaciones($emails, $usuarioId);
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("invitaciones.enviadas"), "text" => $this->get('translator')->trans("invitaciones.enviadas.correctamente")));
                }
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
            }
        }
        
        
        $participaciones = $formularios_serv->getParticipacionesFormulario($form_id, $usuarioId);
        $formulario = $formularios_serv->getInfoFormulario($form_id);
        
        //Contar participaciones finalizadas
        $count_participaciones = 0;
        foreach($participaciones as $p)
        {
            if($p['estado'] == 1)
            {
                $count_participaciones ++;
            }
        }
        
        
        return array(
            'formulario_info' => $formulario,
            'form' => $form->createView(),
            'participaciones' => $participaciones,
            'count_participaciones' => $count_participaciones
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
    public function procesarAction(Request $request, $id)
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
                
                $formularios_serv = $this->get('formularios');
                
                $usuarioId = $security->getSessionValue('id');
                
                //Procesar formulario
                $resultados = $formularios_serv->procesarFormulario($form_id, $usuarioId, $respuestas, $id);
                
                if($resultados['validate'])
                {
                    // Enviar mensaje de notificacion al mentor
                    $this->enviarNotificacionMentor($id);
                    
                    // Enviar mensaje de agradecimiento
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
     * Accion para ver las respuestas de las evaluaciones 360 de un usuario
     * 
     * A esta accion solo tiene acceso el mentor del usuario
     * 
     * @Route("/{id}/resultados", name="evaluacion360_resultados")
     * @Template("vocationetBundle:Evaluacion360:resultados.html.twig")
     * @param integer $usuarioId id de usuario evaluado
     * @return Response
     */
    public function resultadosAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $form_id = $this->get('formularios')->getFormId('evaluacion360');
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
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository("vocationetBundle:Usuarios")->findOneById($id);
        
        return array(
            'formularios' => $formularios,
            'participaciones' => $participaciones,
            'usuario' => $usuario
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
            $participacion = $em->getRepository("vocationetBundle:Participaciones")->findOneBy(array("formulario" => $form_id, "usuarioEvaluado" => $usuarioId, "correoInvitacion" => $email));
            
            if(!$participacion)
            {
                // Registrar invitacion
                $participacion = new \AT\vocationetBundle\Entity\Participaciones();
                $participacion->setFecha(new \DateTime());
                $participacion->setFormulario($form_id);
                $participacion->setUsuarioEvaluado($usuarioId);
                $participacion->setCorreoInvitacion($email);
                $participacion->setEstado(0);

                $em->persist($participacion);
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
     * @return Object entidad Participaciones
     */
    private function validarAcceso($usuarioRespondeEmail, $usuarioEvaluadoId)
    {
        $form_id = $this->get('formularios')->getFormId('evaluacion360');
        $em = $this->getDoctrine()->getManager();
        
        $participacion = $em->getRepository("vocationetBundle:Participaciones")->findOneBy(array("formulario" => $form_id, "correoInvitacion" => $usuarioRespondeEmail, "usuarioEvaluado" => $usuarioEvaluadoId));
        
        return $participacion;
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
     * Funcion que envia un mensaje al mentor cuando se participa en una evaluacion 360
     * 
     * @param integer $usuarioEvaluadoId id de usuario evaluado
     */
    private function enviarNotificacionMentor($usuarioEvaluadoId)
    {
        $em = $this->getDoctrine()->getManager();                
        
        //Obtener mentor del usuario
        $mentorId = $this->getMentorId($usuarioEvaluadoId);
                
        if($mentorId)
        {
            $usuario = $em->getRepository("vocationetBundle:Usuarios")->findOneById($usuarioEvaluadoId);
            $usuarioNombre = $usuario->getUsuarioNombre()." ".$usuario->getUsuarioApellido();
            
            // Enviar mensaje
            
            $subject = $this->get('translator')->trans("evaluacion.360.de.%usu%.finalizada", array('%usu%' => $usuarioNombre), 'mail');
            $link = '<a href="'. $this->get('request')->getSchemeAndHttpHost().$this->generateUrl('evaluacion360_resultados', array('id' => $usuarioEvaluadoId)) .'" >'.$this->get('translator')->trans("resultados.evaluacion.360", array(), 'label').'</a><br/><br/>';
            $body = $this->get('translator')->trans("mensaje.evaluacion.360.de.%usu%.finalizada", array('%usu%' => $usuarioNombre), 'mail')
                    ."<br/><br/>".$link."";
            
            $this->get("mensajes")->enviarMensaje($usuarioEvaluadoId, array($mentorId), $subject, $body);        
        }
        
    }
    
    /**
     * Funcion que verifica el pago para test vocacional
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