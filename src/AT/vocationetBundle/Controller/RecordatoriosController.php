<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para recordatorios de la aplicacion
 *
 * @Route("/recordatorios")
 * @author Camilo Quijano <camilo@altactic.com>
 */
class RecordatoriosController extends Controller
{
    /**
     * Recordatorio de mentorias del dia enviada tanto a mentor, como a estudiante
     * 
	 * @version 1
     * @Route("/", name="recordatorios")
     * @return Response
     */
    public function indexAction()
    {
		/**
		 * @var String Consulta SQL que trae las mentorias que estan programadas para empezar entre el rango de fechas ingresado
		 * SELECT * FROM mentorias
		 * WHERE mentoria_inicio > '2014-01-20'  and mentoria_inicio < '2014-01-24'
		 * AND usuario_estudiante_id IS NOT NULL
		 */
		$fechaHoy = date('Y-m-d');
		$fechaManana = date('Y-m-d', strtotime($fechaHoy.'+1 day'));


		$dql = "SELECT m.id, m.mentoriaInicio, m.mentoriaFin,
					u.usuarioEmail as mailMentor, u.usuarioApellido AS usuarioApellidoM, u.usuarioNombre AS usarioNombreM ,
					u2.usuarioEmail as mailEstudiante, u2.usuarioApellido AS usuarioApellidoE, u2.usuarioNombre AS usarioNombreE
				FROM vocationetBundle:Mentorias m
				INNER JOIN vocationetBundle:Usuarios u WITH m.usuarioEstudiante = u.id
				INNER JOIN vocationetBundle:Usuarios u2 WITH m.usuarioMentor = u2.id
			WHERE m.mentoriaInicio >=:fechaHoy AND m.mentoriaInicio <=:fechaManana AND m.usuarioEstudiante IS NOT NULL";
		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery($dql);
		$query->setParameter('fechaHoy', $fechaHoy);
		$query->setParameter('fechaManana', $fechaManana);
		$mentorias = $query->getResult();

		$subject = $this->get('translator')->trans('recordatorio.mentoria', Array(), 'mail');
		if ($mentorias) {
			foreach ($mentorias as $ment)
			{
				$emails = Array($ment['mailMentor'], $ment['mailEstudiante']);
				$message = $this->get('translator')->trans('hoy.tiene.mentoria', Array(), 'mail').'<br>'.
							$this->get('translator')->trans('mentor', Array(), 'mail').': '.$ment['usuarioApellidoM'].' '.$ment['usarioNombreM'].'<br>'.
							$this->get('translator')->trans('estudiante', Array(), 'mail').': '.$ment['usuarioApellidoE'].' '.$ment['usarioNombreE'].'<br>'.
							$this->get('translator')->trans('hora', Array(), 'mail').': '.$ment["mentoriaInicio"]->format("Y-m-d H:s").'';
				$dataRender = array(
					'title' => $subject,
					'body' => $message,
					'link' => false,
					'link_text' => false,
				);
        
				$this->get('mail')->sendMail($emails, $subject, $dataRender);
				$emails = false;
			}
		}
        return new Response();
    }
}
?>
