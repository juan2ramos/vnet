<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para la agenda de mentor
 *
 * @Route("/agenda_mentor")
 * @author Diego Malagón <diego@altactic.com>
 */
class AgendaMentorController extends Controller
{
    /**
     * Index de la agenda de mentor
     * 
     * @Route("/", name="agenda_mentor")
     * @Template("vocationetBundle:AgendaMentor:index.html.twig")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        $usuarioId = $security->getSessionValue('id');
        
        
        $form = $this->createMentoriaForm();
        
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $data = $form->getData();
                $usuarioId = $security->getSessionValue('id');
                
                $mentoriaInicio = new \DateTime($data['fecha'].' '.$data['hora'].':'.$data['min'].':00');
                $mentoriaFin = new \DateTime();
                $mentoriaFin->setTimestamp($mentoriaInicio->getTimestamp());
                $mentoriaFin->modify('+1 hour');
                
                $currentDate = new \DateTime();
                
                $interval = $currentDate->diff($mentoriaInicio);

                $tralapa = $this->getValidarMentoriaTralapadas($usuarioId, $mentoriaInicio, $mentoriaFin);

                if (!$tralapa) {
					if($interval->invert != 1)
					{ 
						$mentoria = new \AT\vocationetBundle\Entity\Mentorias();
						$mentoria->setUsuarioMentor($usuarioId);
						$mentoria->setMentoriaInicio($mentoriaInicio);
						$mentoria->setMentoriaFin($mentoriaFin);

						$em = $this->getDoctrine()->getManager();
						$em->persist($mentoria);
						$em->flush();

						$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("mentoria.agregada"), "text" => $this->get('translator')->trans("mentoria.agregada.correctamente")));
					}
					else
					{
						$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("fecha.invalida"), "text" => $this->get('translator')->trans("fecha.hora.incorrecta")));
					}
				}
				else
				{
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("mentoria.traslapada")));
				}
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
            }
        }
        
        $mentorias = $this->getMentorias($usuarioId);
        return array(
            'form' => $form->createView(),
            'mentorias' => $mentorias
        );
    }

	/**
	 * Funcion que retorna Bool, TRUE cuando la mentoria se traslada, o se cruza con otra, y FALSE, caso contrario
	 */
    private function getValidarMentoriaTralapadas($mentorId, $fechaInicio, $fechaFin)
    {
		/**
		 * SELECT * FROM mentorias
		 * WHERE usuario_mentor_id = 12 AND (mentoria_inicio between '2014-01-23 11:55:00' AND '2014-01-23 12:55:00')
		 * OR (mentoria_fin between '2014-01-23 11:55:00' AND '2014-01-23 12:55:00');
		*/

		$dql = "SELECT m.id FROM vocationetBundle:Mentorias m
				WHERE m.usuarioMentor =:mentorId AND ((m.mentoriaInicio BETWEEN :fInicio AND :fFin) OR (m.mentoriaFin BETWEEN :fInicio AND :fFin))";
		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery($dql);
		$query->setParameter('fInicio', $fechaInicio);
		$query->setParameter('fFin', $fechaFin);
		$query->setParameter('mentorId', $mentorId);
		$aux = $query->getResult();
		$return = ($aux) ? true : false;
		return $return;
	}
    
    /**
     * Funcion para crear el formulario de mentoria
     * 
     * @return Object formulario
     */
    private function createMentoriaForm()
    {
        $data = array(
            'fecha' => date('Y-m-d'),
            'hora' => null,
            'min' => null
        );
        $form = $this->createFormBuilder($data)
           ->add('fecha', 'text', array('required' => true))
           ->add('hora', 'text', array('required' => true))
           ->add('min', 'text', array('required' => true))
           ->getForm();
        return $form; 
    }
    
    /**
     * Funcion para obtener las mentorias registradas por el usuario
     * 
     * @param type $usuarioId
     */
    private function getMentorias($usuarioId)
    {
        $dql = "SELECT
                    m.id,
                    m.mentoriaInicio,
                    m.mentoriaFin,
                    m.mentoriaEstado,
                    u.id estudianteId,
                    u.usuarioNombre,
                    u.usuarioApellido
                FROM 
                    vocationetBundle:Mentorias m
                    LEFT JOIN vocationetBundle:Usuarios u WITH m.usuarioEstudiante = u.id
                WHERE
                    m.usuarioMentor = :usuarioId ";
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $result = $query->getResult();
        return $result;
    }
    
    /**
     * Accion ajax para detalle de una mentoria
     * 
     * @Route("/show/{id}", name="show_mentoria_mentor")
     * @Template("vocationetBundle:AgendaMentor:show.html.twig")
     * @return Response
     */
    public function showMentoriaAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        if(!$this->getRequest()->isXmlHttpRequest()) throw $this->createNotFoundException();
        
        $usuarioId = $security->getSessionValue('id');
        
        $mentoria = false;
        
        $dql = "SELECT
                    m.id,
                    m.mentoriaInicio,
                    m.mentoriaFin,
                    m.mentoriaEstado,
                    u.id estudianteId,
                    u.usuarioNombre,
                    u.usuarioApellido,
                    u.usuarioImagen
                FROM 
                    vocationetBundle:Mentorias m
                    LEFT JOIN vocationetBundle:Usuarios u WITH m.usuarioEstudiante = u.id
                WHERE
                    m.usuarioMentor = :usuarioId
                    AND m.id = :mentoriaId";
        
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
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
     * Accion para eliminar una mentoria
     * 
     * @Route("/delete/{id}", name="delete_mentoria_mentor")
     * @return Response
     */
    public function deleteMentoriaAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        $usuarioId = $security->getSessionValue('id');
        
        $em = $this->getDoctrine()->getManager();
        
        $mentoria = $em->getRepository('vocationetBundle:Mentorias')->findOneBy(array('id' => $id, 'usuarioMentor' => $usuarioId));
      
        if($mentoria)
        {
            if($mentoria->getUsuarioEstudiante() === NULL)
            {
                $em->remove($mentoria);
                $em->flush();
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("mentoria.eliminada"), "text" => $this->get('translator')->trans("mentoria.eliminada.correctamente")));
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("mentoria.no.eliminada"), "text" => $this->get('translator')->trans("mentoria.ya.esta.asignada")));
            }
        }
        else
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("mentoria.no.eliminada"), "text" => $this->get('translator')->trans("mentoria.no.se.puede.eliminar")));
        }
        return $this->redirect($this->generateUrl('agenda_mentor'));
    }
    
     /**
     * Accion para marcar como finalizada una mentoria
     * 
     * @Route("/finalizar/{id}", name="finalizar_mentoria_mentor")
     * @return Response
     */
    public function finalizarMentoriaAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        $usuarioId = $security->getSessionValue('id');
        $rolId = $security->getSessionValue('rolId');
        
        $em = $this->getDoctrine()->getManager();
        $mentoria = $em->getRepository('vocationetBundle:Mentorias')->findOneBy(array('id' => $id, 'usuarioMentor' => $usuarioId));
        
        if($mentoria)
        {
            $mentoria->setMentoriaEstado(1);
            $em->persist($mentoria);
            $em->flush();

			if ($rolId == 2) {
				$this->get('perfil')->actualizarpuntos('redmentores', $mentoria->getUsuarioEstudiante());
			}
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("mentoria.finalizada"), "text" => $this->get('translator')->trans("mentoria.finalizada.correctamente")));
        }
        
        return $this->redirect($this->generateUrl('agenda_mentor'));
    }
}
?>
