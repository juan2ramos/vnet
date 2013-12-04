<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador para el cuestionario de diagnostico
 * 
 * El id para este formulario principal es 1
 *
 * @Route("/diagnostico")
 * @author Diego Malagón <diego@altactic.com>
 */
class DiagnosticoController extends Controller
{
    /**
     * Index del cuestionario de diagnostico
     * 
     * @Route("/", name="diagnostico")
     * @Template("vocationetBundle:Diagnostico:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $usuarioId = $security->getSessionValue("id");
        $formularios_serv = $this->get('formularios');
        $form_id = $this->get('formularios')->getFormId('diagnostico');
        
        $em = $this->getDoctrine()->getManager();
        
        //Validar acceso a diagnostico
        $usuarioFormulario = $em->getRepository("vocationetBundle:UsuariosFormularios")->findOneBy(array("formulario" => $form_id, "usuarioResponde" => $usuarioId));
        if($usuarioFormulario)
        {
            return $this->forward("vocationetBundle:Alerts:alertScreen", array(
                "title" => $this->get('translator')->trans("cuestionario.ya.ha.sido.enviado"),
                "message" => $this->get('translator')->trans("gracias.por.participar.diagnostico")
            )); 
        }
        
        $formularios = $formularios_serv->getFormulario($form_id);
        $formulario = $formularios_serv->getInfoFormulario($form_id);
        $form = $this->createFormCuestionario();
        
        return array(
            'formularios' => $formularios,
            'formulario_info' => $formulario,
            'form' => $form->createView(),
        );
    }
    
    /**
     * Accion que recibe y procesa el cuestionario de diagnostico
     * 
     * @Route("/procesar", name="procesar_diagnostico")
     * @Template("vocationetBundle:Diagnostico:resultado.html.twig")
     * @Method({"POST"})
     * @param \AT\vocationetBundle\Controller\Request $request
     */
    public function procesarCuestionarioAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $form = $this->createFormCuestionario();
        $form_id = $this->get('formularios')->getFormId('diagnostico');
        
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
                    $puntaje = $resultados['puntaje'];
                    
                    $titulo_mensaje = '';
                    $mensaje = '';
                    
                    $rango1 = $security->getParameter('diagnostico_rango_puntaje_1');
                    $rango2 = $security->getParameter('diagnostico_rango_puntaje_2');
                    $rango3 = $security->getParameter('diagnostico_rango_puntaje_3');
                    
                    if($puntaje <= $rango1['max'])
                    {
                        $titulo_mensaje = $this->get('translator')->trans("diagnostico.titulo.resultado.1");
                        $mensaje = $this->get('translator')->trans("diagnostico.mensaje.resultado.1");
                    }
                    elseif($puntaje >= $rango2['min'] && $puntaje <= $rango2['max'])
                    {
                        $titulo_mensaje = $this->get('translator')->trans("diagnostico.titulo.resultado.2");
                        $mensaje = $this->get('translator')->trans("diagnostico.mensaje.resultado.2");
                    }
                    elseif($puntaje >= $rango3['min'])
                    {
                        $titulo_mensaje = $this->get('translator')->trans("diagnostico.titulo.resultado.3");
                        $mensaje = $this->get('translator')->trans("diagnostico.mensaje.resultado.3");
                    }
                    
                    // Actualizar registro UsuariosFormularios
                    $em = $this->getDoctrine()->getManager();
                    $usuarioFormulario = new \AT\vocationetBundle\Entity\UsuariosFormularios();
                    $usuarioFormulario->setFormulario($form_id);
                    $usuarioFormulario->setUsuarioResponde($usuarioId);
                    $usuarioFormulario->setUsuarioEvaluado($usuarioId);
                    $usuarioFormulario->setEstado(1);
                    $em->persist($usuarioFormulario);
                    $em->flush();
                    
                    return array(
                        'titulo_mensaje' => $titulo_mensaje,
                        'mensaje' => $mensaje
                    );
                    
                    //$security->debug($resultados);
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
                    return $this->redirect($this->generateUrl('diagnostico'));
                }
                
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
                return $this->redirect($this->generateUrl('diagnostico'));
            }
        }
        return new Response();
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
}
?>
