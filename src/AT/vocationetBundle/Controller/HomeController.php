<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para la pagina principal de la aplicacion
 *
 * @Route("/home")
 * @author Diego Malagón <diego@altactic.com>
 */
class HomeController extends Controller
{
    /**
     * Index de la aplicacion
     * 
	 * @author Diego Malagón <diego@altactic.com>
	 * @author Camilo Quijano <camilo@altactic.com>
	 * @version 1 - Notificaciones de evaluacion 360
	 * @version 2 - Mapa y recorrido del usuario
     * @Route("/", name="homepage")
     * @Template("vocationetBundle:Home:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $usuarioId = $security->getSessionValue("id");
        $usuarioEmail = $security->getSessionValue("usuarioEmail");
        
        $invitaciones360 = $this->get('formularios')->getInvitacionesEvaluacion360($usuarioId, $usuarioEmail);
		
		
		$em = $this->getDoctrine()->getManager();
        //$participaciones = $em->getRepository('vocationetBundle:Participaciones')->findBy(array('usuarioParticipa'=> $usuarioId), array('id' => 'ASC'));
        $dql = "SELECT p FROM vocationetBundle:Participaciones p
                WHERE (p.usuarioParticipa =:usuarioId OR p.usuarioEvaluado =:usuarioId) AND p.estado = 1";
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $participaciones = $query->getResult();
		
		// mentorias realizadas por el usuario con mentor experto vocacional.
		//$mentorias = $em->getRepository('vocationetBundle:Mentorias')->findBy(array('usuarioEstudiante'=> $usuarioId, 'mentoriaEstado'=> 1), array('id' => 'ASC'));
		$dql = "SELECT m FROM vocationetBundle:Mentorias m
				JOIN vocationetBundle:Usuarios u WITH m.usuarioMentor = u.id
				WHERE m.usuarioEstudiante =:usuarioId AND m.mentoriaEstado = 1 AND u.rol = 3";
		$query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $mentorias = $query->getResult();
		
		$cm = count($mentorias); //Cantidad de mentorias
		
		/**
		* P1. DIAGNOSTICO
		* P2. TEST VOCACIONAL (QUEMADO - REVISAR)
		* P9. EVALUACION 360
		* P10. DISEÑO DE VIDA
		* P11. MERCADO LABORAL
		* P12. RED DE MENTORES
		* P13. PONDERACION
		*/
		
		$recorrido = Array(
			'P1' => false, 'M1' => ($cm >= 1) ? true : false,
			'P2' => ($cm >= 1) ? true : false, 'M2' => ($cm >= 2) ? true : false,
			'P9' => false, 'M3' => ($cm >= 3) ? true : false,
			'P10' => false, 'M4' => ($cm >= 4) ? true : false,
			'P11' => false, 'M5' => ($cm >= 5) ? true : false,
			'P12' => false,
			'P13' => false, 'M6' => ($cm >= 6) ? true : false,
		);
		
		$auxCt360 = 0; // Contador de participantes 360
		foreach($participaciones as $part){
			$formId = $part->getFormulario();
			if ($formId == 9) // Evaluacion 360
			{
				$auxCt360 += 1;
				if ($auxCt360 >= 3) {
					$recorrido['P'.$formId] = true;
				}
			} 
			else 
			{
				$recorrido['P'.$formId] = true;
			}
		}
		PRINT($auxCt360);
		
        return array(
            'invitaciones360' => $invitaciones360,
			'recorrido' => $recorrido,
        );
    }
}
?>
