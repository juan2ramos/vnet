<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador para ponderacion
 * 
 * El id para este formulario principal es 13
 *
 * @Route("/ponderacion")
 * @author Diego Malagón <diego@altactic.com>
 */
class PonderacionController extends Controller
{
    /**
     * Index de ponderacion
     * 
     * @Route("/", name="ponderacion")
     * @Template("vocationetBundle:Ponderacion:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        
        
        return array(
            
        );
    }
}
?>
