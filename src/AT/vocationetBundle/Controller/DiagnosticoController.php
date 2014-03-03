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
        $participacion = $em->getRepository("vocationetBundle:Participaciones")->findOneBy(array("formulario" => $form_id, "usuarioParticipa" => $usuarioId));
        if($participacion)
        {
            return $this->forward("vocationetBundle:Alerts:alertScreen", array(
                "title" => $this->get('translator')->trans("certificado.no.generado"),
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
        $usuarioId = $security->getSessionValue('id');
        $em = $this->getDoctrine()->getManager();
        
        //Validar acceso a diagnostico
        $participacion = $em->getRepository("vocationetBundle:Participaciones")->findOneBy(array("formulario" => $form_id, "usuarioParticipa" => $usuarioId));
        if($participacion)
        {
            return $this->forward("vocationetBundle:Alerts:alertScreen", array(
                "title" => $this->get('translator')->trans("cuestionario.ya.ha.sido.enviado"),
                "message" => $this->get('translator')->trans("gracias.por.participar.diagnostico")
            )); 
        }
        
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $respuestas = $request->get('pregunta');
                
                $formularios_serv = $this->get('formularios');
                
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

                    $auxRango = false;
                    if($puntaje <= $rango1['max'])
                    {
                        $titulo_mensaje = $this->get('translator')->trans("diagnostico.titulo.resultado.1");
                        $mensaje = $this->get('translator')->trans("diagnostico.mensaje.resultado.1");
                        $auxRango = 1;
                    }
                    elseif($puntaje >= $rango2['min'] && $puntaje <= $rango2['max'])
                    {
                        $titulo_mensaje = $this->get('translator')->trans("diagnostico.titulo.resultado.2");
                        $mensaje = $this->get('translator')->trans("diagnostico.mensaje.resultado.2");
                        $auxRango = 2;
                    }
                    elseif($puntaje >= $rango3['min'])
                    {
                        $titulo_mensaje = $this->get('translator')->trans("diagnostico.titulo.resultado.3");
                        $mensaje = $this->get('translator')->trans("diagnostico.mensaje.resultado.3");
                        $auxRango = 3;
                    }

                    $this->get('perfil')->actualizarpuntos('diagnostico', $usuarioId, array('rango'=>$auxRango, 'puntaje' => $puntaje));
                    
                    return array(
                        'titulo_mensaje' => $titulo_mensaje,
                        'mensaje' => $mensaje
                    );
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
     * Accion para ver las respuestas de diagnostico de un usuario
     * 
     * A esta accion solo tiene acceso el mentor del usuario
     * 
     * @Route("/{id}/resultados", name="diagnostico_resultados")
     * @Template("vocationetBundle:Diagnostico:resultados.html.twig")
     * @param integer $id id de usuario evaluado
     * @return Response
     */
    public function resultadosAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $form_id = $this->get('formularios')->getFormId('diagnostico');
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
}
?>
