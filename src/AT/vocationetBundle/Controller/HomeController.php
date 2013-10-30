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
     * @Route("/", name="homepage")
     * @Template("vocationetBundle:Home:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        
        return array();
    }
}
?>
