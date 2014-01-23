<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para la agenda de estudiante
 *
 * @Route("/agenda_estudiante")
 * @author Diego Malagón <diego@altactic.com>
 */
class AgendaEstudianteController extends Controller
{
    /**
     * Index de la agenda de estudiante
     * - Redireccionado desde ContactosController:seleccionarMentorAction
     * 
     * @Route("/", name="agenda_estudiante")
     * @Template("vocationetBundle:AgendaEstudiante:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        $usuarioId = $security->getSessionValue('id');
        
        $mentorias = $this->getMentorias($usuarioId, false);
        
        return array(
            'mentorias' => $mentorias
        );
    }

    
    /**
     * Funcion para obtener las mentorias <!-- disponibles -> y aceptadas
     * 
     * Obtiene las mentorias disponibles registradas por los mentores con los que tiene relacion
     * Obtiene tambien las mentorias a las que aplico
     * 
     * @param integer $usuarioId id de usuario
     */
    private function getMentorias($usuarioId)
    {
        /**
         * SELECT u.id, u.usuario_nombre, m.* FROM 
         * usuarios u
         * JOIN relaciones r ON r.usuario_id = u.id OR r.usuario2_id = u.id
         * JOIN mentorias m ON u.id = m.usuario_mentor_id
         * WHERE 
         * (r.usuario_id = 6 OR r.usuario2_id = 6)
         * AND u.id != 6
         * AND r.tipo = 2
         * AND (m.usuario_estudiante_id = 6 OR m.usuario_estudiante_id IS NULL);
        */

        $dql = "SELECT 
                    m.id,
                    m.mentoriaInicio,
                    m.mentoriaFin,
                    u.usuarioNombre,
                    u.usuarioApellido,
                    m.usuarioEstudiante estudianteId,
                    m.mentoriaEstado
                FROM
                    vocationetBundle:Usuarios u
                    JOIN vocationetBundle:Relaciones r WITH r.usuario = u.id OR r.usuario2 = u.id
                    JOIN vocationetBundle:Mentorias m WITH u.id = m.usuarioMentor
                WHERE
                    (r.usuario = :usuarioId OR r.usuario2 = :usuarioId)
                    AND u.id != :usuarioId
                    AND (r.tipo = 2 OR r.tipo = 3)
                    AND r.estado = 1
                    AND (m.usuarioEstudiante = :usuarioId)
                GROUP BY m.id";
        // OR m.usuarioEstudiante IS NULL
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $result = $query->getResult();
        return $result;
    }
    
    /**
     * Accion ajax para el detalle de una mentoria
     * 
     * @Route("/{id}", name="show_mentoria_estudiante")
     * @Template("vocationetBundle:AgendaEstudiante:show.html.twig")
     * @return Response
     */
    public function showMentoriaAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        if(!$this->getRequest()->isXmlHttpRequest()) throw $this->createNotFoundException();
        
        $mentoria = false;
        
        $dql = "SELECT
                    m.id,
                    m.mentoriaInicio,
                    m.mentoriaFin,
                    m.usuarioEstudiante estudianteId,
                    m.mentoriaEstado,
                    u.usuarioNombre,
                    u.usuarioApellido,
                    u.usuarioImagen,
                    r.descripcion rolNombre
                FROM 
                    vocationetBundle:Mentorias m
                    LEFT JOIN vocationetBundle:Usuarios u WITH m.usuarioMentor = u.id
                    LEFT JOIN vocationetBundle:Roles r WITH u.rol = r.id
                WHERE
                    m.id = :mentoriaId";

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('mentoriaId', $id);
        $result = $query->getResult();
        
        if(isset($result[0]))
        {
            $mentoria = $result[0];
        }
        
        return array(
            'mentoria' => $mentoria
        );
        
    }
    
    /**
     * 
     * Accion ajax para separar una mentoria
     * 
     * @Route("/separar/{id}", name="separar_mentoria_estudiante")
     * @Method({"POST"})
     * @param integer $id id de mentoria
     * @return Response
     */
    public function separarMentoriaAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        if(!$this->getRequest()->isXmlHttpRequest() || $this->getRequest()->getMethod() != 'POST') throw $this->createNotFoundException();
        
        $response = '';
        
        $usuarioId = $security->getSessionValue('id');
        $em = $this->getDoctrine()->getManager();
        
        // Verificar pago
        $pago = $this->verificarPago($usuarioId, $id);
        
        if($pago)
        {
			$mentoria = $em->getRepository("vocationetBundle:Mentorias")->findOneById($id);
			$tralapa = false;
			if ($mentoria) {
				$tralapa = $this->getValidarMentoriaTralapadas($usuarioId, $mentoria->getMentoriaInicio(), $mentoria->getMentoriaFin());
			}

			if (!$tralapa) {
			
				$dql = "UPDATE vocationetBundle:Mentorias m 
						SET m.usuarioEstudiante = :usuarioId
						WHERE m.id = :mentoriaId";
				$query = $em->createQuery($dql);
				$query->setParameter('usuarioId', $usuarioId);
				$query->setParameter('mentoriaId', $id);
				$separada = $query->getResult();
				
				if($separada)
				{
					// Obtener informacion del mentor y estudiante para enviar notificacion
					
					$mentorId = $mentoria->getUsuarioMentor();
					$estudianteNombre = $security->getSessionValue("usuarioNombre")." ".$security->getSessionValue("usuarioApellido");
					$hora = $mentoria->getMentoriaInicio()->format('H:i d-m-Y');
					
					// Enviar notificacion al tutor
					$mensajes = $this->get('mensajes');

					$subject = $this->get('translator')->trans("%user%.ha.separado.mentoria", array('%user%'=> $estudianteNombre));
					$message = $this->get('translator')->trans("%user%.ha.separado.mentoria.%detalle%", array('%user%'=> $estudianteNombre, '%detalle%' => $hora));

					$mensajes->enviarMensaje($usuarioId, array($mentorId), $subject, $message);
					
					// Marcar producto como usado
					$this->get('pagos')->marcarPagoUsado($pago);                
					
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("mentoria.separada"), "text" => $this->get('translator')->trans("mentoria.separada.correctamente")));
					$response = (json_encode(array(
						'status' => 'success',
						'message' => $this->get('translator')->trans("mentoria.separada"),
						'detail' => $this->get('translator')->trans("mentoria.separada.correctamente"),
						'redirect' => $this->generateUrl('agenda_estudiante')
					)));
				}
				else
				{
					$response = (json_encode(array(
						'status' => 'error',
						'message' => $this->get('translator')->trans("mentoria.no.separada"),
						'detail' => $this->get('translator')->trans("mentoria.no.se.puede.separar"),
					)));
				}
			}
			else {
				//translapada
				$response = (json_encode(array(
						'status' => 'error',
						'message' => $this->get('translator')->trans("mentoria.no.separada"),
						'detail' => $this->get('translator')->trans("mentoria.traslapada"),
					)));
			}
        }
        else
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("no.existe.pago"), "text" => $this->get('translator')->trans("antes.de.continuar.debes.realizar.el.pago")));
            $response = (json_encode(array(
                'status' => 'error',
                'message' => $this->get('translator')->trans("no.existe.pago"),
                'detail' => $this->get('translator')->trans("antes.de.continuar.debes.realizar.el.pago"),
                'redirect' => $this->generateUrl('planes')
            )));
        }
        
        return new Response($response);
    }

    /**
	 * Funcion que retorna Bool, TRUE cuando la mentoria se traslada, o se cruza con otra, y FALSE, caso contrario
	 */
    private function getValidarMentoriaTralapadas($estudianteId, $fechaInicio, $fechaFin)
    {
		/**
		 * SELECT * FROM mentorias
		 * WHERE usuario_mentor_id = 12 AND (mentoria_inicio between '2014-01-23 11:55:00' AND '2014-01-23 12:55:00')
		 * OR (mentoria_fin between '2014-01-23 11:55:00' AND '2014-01-23 12:55:00');
		*/

		$dql = "SELECT m.id FROM vocationetBundle:Mentorias m
				WHERE m.usuarioEstudiante =:estudianteId AND ((m.mentoriaInicio BETWEEN :fInicio AND :fFin) OR (m.mentoriaFin BETWEEN :fInicio AND :fFin))";
		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery($dql);
		$query->setParameter('fInicio', $fechaInicio);
		$query->setParameter('fFin', $fechaFin);
		$query->setParameter('estudianteId', $estudianteId);
		$aux = $query->getResult();
		$return = ($aux) ? true : false;
		return $return;
	}
    
    /**
     * Funcion que verifica si existe un pago para mentoria
     * 
     * Verifica si tiene un pago para el paquete completo o mentoria individual.
     * tiene en cuenta si es para mentoria de orientacion vocacional o profesional
     * 
     * @param integer $usuarioId id de usuario
     * @param integer $mentoriaId id de mentoria para obtener el tipo de mentor
     * @return boolean|integer false si no existe ningun pago, true si es pago por plan completo, id de ordenProducto si es un pago individual
     */
    private function verificarPago($usuarioId, $mentoriaId)
    {
        $pagado = false;
        
        $pagoCompleto = $this->get('pagos')->verificarPagoProducto($this->get('pagos')->getProductoId('programa_orientacion'), $usuarioId);
        
        if($pagoCompleto)
        {
            $pagado = true;
        }
        else
        {
            $em = $this->getDoctrine()->getManager();
            
            // Verificar tipo de mentor para verificar pago
            $dql = "SELECT r.id rolId, u.id mentorId FROM vocationetBundle:Mentorias m
                    JOIN vocationetBundle:Usuarios u WITH m.usuarioMentor = u.id
                    JOIN vocationetBundle:Roles r WITH u.rol = r.id
                    WHERE m.id = :mentoriaId";  
            $query = $em->createQuery($dql);
            $query->setParameter('mentoriaId', $mentoriaId);
            $query->setMaxResults(1);
            $mentorRol = $query->getResult();
            if($mentorRol)
            {
                $mentorId = $mentorRol[0]['mentorId'];
                $mentorRol = $mentorRol[0]['rolId'];
            }
            
            if($mentorRol == 2) // Mentor experto
            {
                $productoId = $this->get('pagos')->getProductoId('mentoria_profesional'); // Producto: Mentoría con profesional
                
                // Validar pago de producto individual para el mentor seleccionado
                $pagado = $this->get('pagos')->verificarPagoProducto($productoId, $usuarioId, $mentorId);                        
            }
            elseif($mentorRol == 3) // Mentor de orientacion vocacional
            {
                $productoId = $this->get('pagos')->getProductoId('mentoria_ov'); // Producto: Mentoría con experto en orientación vocacional
                
                // Validar pago de producto individual
                $pagado = $this->get('pagos')->verificarPagoProducto($productoId, $usuarioId);                        
            }

        }
        
        return $pagado;
    }
}
?>
