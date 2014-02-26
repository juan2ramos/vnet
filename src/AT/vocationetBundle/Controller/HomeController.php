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

        $rol = $security->getSessionValue("rolId");
        
        if ($rol == 2) {	return $this->redirect($this->generateUrl('agenda_mentor'));  }// Mentor Experto
        if ($rol == 3) {	return $this->redirect($this->generateUrl('lista_usuarios_mentor'));  }// Mentor Orientación Vocacional
        if ($rol == 4) {	return $this->redirect($this->generateUrl('mensajes'));  }// Administrador
        
        $usuarioId = $security->getSessionValue("id");
        $usuarioEmail = $security->getSessionValue("usuarioEmail");
        
        $invitaciones360 = $this->get('formularios')->getInvitacionesEvaluacion360($usuarioId, $usuarioEmail);
		$estadoActual = $this->get('perfil')->getEstadoActualPlataforma($usuarioId);
		$programa_orientacion = $this->get('pagos')->verificarPagoProducto($this->get('pagos')->getProductoId('programa_orientacion'), $usuarioId);

        return array(
            'invitaciones360' => $invitaciones360,
			'recorrido' => $estadoActual,
			'programa_orientacion' => $programa_orientacion,
        );
    }
}
?>
