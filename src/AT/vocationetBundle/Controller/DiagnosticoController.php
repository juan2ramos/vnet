<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para el cuestionario de diagnostico
 *
 * @Route("/diagnostico")
 * @author Diego Malagón <diego@altactic.com>
 */
class DiagnosticoController extends Controller
{
    /**
     * Index de el cuestionario de diagnostico
     * 
     * @Route("/", name="diagnostico")
     * @Template("vocationetBundle:Diagnostico:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        
        return array();
    }
}
?>
