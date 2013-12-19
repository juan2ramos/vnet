<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para control de alertas tipo badge de la aplicacion
 * @Route("/alerts")
 * @author Diego Malagón <diego@altactic.com>
 */
class AlertsController extends Controller
{
    /**
     * Accion ajax para obtener alertas tipo badge
     * 
     * - Obtiene la cantidad de mensajes sin leer
	 * - Obtiene la cantidad de solicitudes de amistad sin aprobar
     * @Route("/badge", name="alert_badge")
     * @return string json
     */
    public function alertBadgeAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ throw $this->createNotFoundException();} 
        if(!$this->getRequest()->isXmlHttpRequest()) throw $this->createNotFoundException();
        
        $usuarioId = $security->getSessionValue('id');
        
        $count = $this->get('mensajes')->countMensajesSinLeer($usuarioId);
		$countCA = $this->get('perfil')->getCantidadAmistades($usuarioId);
        
        return new Response(json_encode(array(
            'mensajes_sin_leer' => $count,
			'amistades_sin_aprobar' => $countCA
        )));
    }
    
    /**
     * Accion para mostrar una pantalla de mensaje
     * 
     * para acceder a esta funcion se hace a traves de forward
     * 
     * @Route("/screen/{title}/{message}/{type}", name="alert_screen", defaults={"title"="", "message"="", "type"="info"})
     * @Template("vocationetBundle:Alerts:alertScreen.html.twig")
     * @param string $title
     * @param string $message
     */
    public function alertScreenAction($title = "", $message = "", $type = "info")
    {
        return array(
            "type" => $type,
            "title" => $title,
            "message" => $message
        );
    }
}
?>