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
 * @Route("/testVocacional")
 * @author CamiloQuijano <camilo@altactic.com>
 */
class TestVocacionalController extends Controller
{
    /**
     * Index de el cuestionario de diagnostico
     * 
     * @Route("/", name="test_vocacional")
     * @Template("vocationetBundle:TestVocacional:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $preguntas_serv = $this->get('preguntas');
        
        //$formularios = $preguntas_serv->getFormulario(1);
        
        //$security->debug($formularios);

        $enlaceTV = $security->getEnlaceTestVocacional();
        $informTV = $security->getRutaEnlaceTestVocacionalInfo();
        
        return array(
            //'formularios' => $formularios
            'informacion' => $informTV,
            'linkTestVocacional' => $enlaceTV
        );
        
    }

    
}
?>
