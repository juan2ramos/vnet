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
                
                // Calcular fecha de fin agregandole 1 hora a la fecha de inicio
                $mentoriaInicio = new \DateTime($data['fecha'].' '.$data['hora'].':'.$data['min'].':00');
                $mentoriaFin = $this->modifyDateTime($mentoriaInicio, '+1 hour');
                
                // Validar cruces con otras mentorias
                $cruce = $this->validarCruceMentoria($usuarioId, $mentoriaInicio, $mentoriaFin);

                if (!$cruce) 
                {                                        
                    // Validar que no sea una fecha pasada (inferior a la actual)
                    $currentDate = new \DateTime();                
                    $interval = $currentDate->diff($mentoriaInicio);
                    
					if($interval->invert != 1)
					{ 
                        // Insertar mentoria(s)
                        $this->registrarMentorias($usuarioId, $mentoriaInicio, $mentoriaFin, $data['repetir']);

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
        $publicidad = $this->get('perfil')->getPublicidad($security->getSessionValue("rolId"));
        
        return array(
            'form' => $form->createView(),
            'mentorias' => $mentorias,
            'publicidad' => $publicidad,
        );
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
    
    
    /**
     * Funcion para insertar mentorias en base de datos
     * 
     * @author Diego Malagón <diego@altactic.com>
     * @param integer $usuarioId id de usuario mentor
     * @param DateTime $mentoriaInicio fecha de inicio de la mentoria
     * @param DateTime $mentoriaFin fecha de fin de la mentoria
     * @param integer $semanas numero de semanas a repetir la mentoria
     */
    private function registrarMentorias($usuarioId, $mentoriaInicio, $mentoriaFin, $semanas)
    {
        // Preparar query
        $insert_head = "INSERT INTO mentorias(usuario_mentor_id, mentoria_inicio, mentoria_fin) VALUES ";
        $insert_values = array();
         
        // valores para la mentoria en la fecha ingresada por el usuario
        $insert_values[] = '('.$usuarioId.', "'. $mentoriaInicio->format('y-m-d H:i:s') .'", "'. $mentoriaFin->format('y-m-d H:i:s') .'")';
        
        // Repetir mentorias para las semanas indicadas
        if($semanas >= 1 && $semanas <= 4)
        {                            
            $insert_values = array_merge($insert_values, $this->duplicarMentoria($usuarioId, $mentoriaInicio, $mentoriaFin, $semanas));
        }
        
        $sql = $insert_head . implode(", ", $insert_values);
        
        // Ejecutar query
        $query = $this->getDoctrine()->getManager()
                ->getConnection()
                ->prepare($sql);
        $query->execute();        
    }
    
    /**
     * Funcion para duplicar una mentoria las n semanas indicadas
     * 
     * @author Diego Malagón <diego@altactic.com>
     * @param integer $usuarioId id de usuario mentor
     * @param DateTime $mentoriaInicio fecha de inicio de la mentoria
     * @param DateTime $mentoriaFin fecha de fin de la mentoria
     * @param integer $semanas numero de semanas a repetir la mentoria
     * @return array arreglo de valores para el insert sql
     */
    private function duplicarMentoria($usuarioId, $mentoriaInicio, $mentoriaFin, $semanas)
    {
        $insert_values = array();
        
        // Inicia a duplicar desde el dia siguiente a la fecha ingresada por el usuario
        $dia_mentoria = $mentoriaInicio->format('w') + 1;
        $fecha_ini_dup = $mentoriaInicio;
        $fecha_fin_dup = $mentoriaFin;        
        
        // Repetir mentorias para las semanas indicadas
        for($s = 1; $s <= $semanas; $s++)
        {
            /*
             * Para la primera semana inicia a duplicar desde el dia siguiente a la fecha ingresada por el usuario hasta el viernes (dia 5)
             * para las siguientes semanas inicia desde el lunes (dia 1) hasta el viernes (dia 5)
             */
            for($i = $dia_mentoria; $i <= 5; $i++)
            {
                $fecha_ini_dup = $this->modifyDateTime($fecha_ini_dup, '+1 day');
                $fecha_fin_dup = $this->modifyDateTime($fecha_fin_dup, '+1 day');
                
                $insert_values[] = '('.$usuarioId.', "'. $fecha_ini_dup->format('y-m-d H:i:s') .'", "'. $fecha_fin_dup->format('y-m-d H:i:s') .'")';
                
                if($i == 5) // si el dia es viernes agrego 2 dias mas para omitir sabados y domingos
                {
                    $fecha_ini_dup = $this->modifyDateTime($fecha_ini_dup, '+2 day');
                    $fecha_fin_dup = $this->modifyDateTime($fecha_fin_dup, '+2 day');      
                }
            }


            $dia_mentoria = 1; // Reinicio la variable a lunes (dia 1)
        }
        
        return $insert_values;
    }    
    
    /**
     * Funcion para validar que no existan cruce de horarios entre mentorias
     * 
     * @author Camilo Quijano
     * @author Diego Malagón
     * @param integer $mentorId id de mentor
     * @param DateTime $fechaInicio fecha de inicio de mentoria
     * @param DateTime $fechaFin fecha de finalizacion de mentoria
     * @return boolean true si existe cruce, false si no 
     */
    private function validarCruceMentoria($mentorId, $fechaInicio, $fechaFin)
    {
		/**
		 * SELECT * FROM mentorias
		 * WHERE usuario_mentor_id = 12 AND (mentoria_inicio between '2014-01-23 11:55:00' AND '2014-01-23 12:55:00')
		 * OR (mentoria_fin between '2014-01-23 11:55:00' AND '2014-01-23 12:55:00');
		*/

		$dql = "SELECT COUNT(m.id) AS c FROM vocationetBundle:Mentorias m
				WHERE m.usuarioMentor =:mentorId AND ((m.mentoriaInicio BETWEEN :fInicio AND :fFin) OR (m.mentoriaFin BETWEEN :fInicio AND :fFin))";
		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery($dql);
		$query->setParameter('fInicio', $fechaInicio);
		$query->setParameter('fFin', $fechaFin);
		$query->setParameter('mentorId', $mentorId);
		$result = $query->getResult();
        
        
		$return = ($result[0]['c'] >= 1) ? true : false;
        
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
            'fecha'     => date('Y-m-d'),
            'hora'      => null,
            'min'       => null,
            'repetir'   => null
        );
        $form = $this->createFormBuilder($data)
           ->add('fecha', 'text', array('required' => true))
           ->add('hora', 'text', array('required' => true))
           ->add('min', 'text', array('required' => true))
           ->add('repetir', 'text', array('required' => false))
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
     * Funcion que retorna un DateTime modificado con respecto a otro
     * 
     * @param DateTime $DateTime fecha
     * @param string $modify modificacion (+1 day)
     * @return \DateTime
     */
    private function modifyDateTime($DateTime, $modify)
    {
        $mentoriaFin = new \DateTime($DateTime->format('y-m-d H:i:s'));
        $mentoriaFin->modify($modify);
        
        return $mentoriaFin;
    }
}
?>
