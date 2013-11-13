<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * controlador para control de alertas tipo badge de la aplicacion
 * @Route("/badge")
 * @author Diego Malagón <diego@altactic.com>
 */
class AlertBadgeController extends Controller
{
    /**
     * Accion ajax para obtener alertas tipo badge
     * 
     * - Obtiene la cantidad de mensajes sin leer
	 * - Obtiene la cantidad de solicitudes de amistad sin aprobar
     * @Route("/", name="alert_badge")
     * @return string json
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ throw $this->createNotFoundException();} 
        if(!$this->getRequest()->isXmlHttpRequest()) throw $this->createNotFoundException();
        
        $mensajes_serv = $this->get('mensajes');
        $usuarioId = $security->getSessionValue('id');
        
        $count = $mensajes_serv->countMensajesSinLeer($usuarioId);
		$countCA = $this->get('perfil')->getCantidadAmistades($usuarioId);
        
        print(json_encode(array(
            'mensajes_sin_leer' => $count,
			'amistades_sin_aprobar' => $countCA
        )));
        
        return new Response();
    }
}
?>