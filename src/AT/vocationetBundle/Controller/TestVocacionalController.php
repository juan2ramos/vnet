<?php

namespace AT\vocationetBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador de test vocacional
 * @package vocationetBundle
 * @Route("/testVocacional")
 * @author Camilo Quijano <camilo@altactic.com>
 * @version 1
 */
class TestVocacionalController extends Controller
{
    /**
     * Index de test vocacional
     * 
     * @Route("/", name="test_vocacional")
     * @Template("vocationetBundle:TestVocacional:index.html.twig")
     * @Method("GET")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		//if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$usuarioId = $security->getSessionValue('id');
        $seleccionarMentor = $pr->confirmarMentorOrientacionVocacional($usuarioId);

        if($seleccionarMentor) {
			$enlaceTV = $security->getEnlaceTestVocacional();
			$informTV = $security->getRutaEnlaceTestVocacionalInfo();
		} else {
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("Acceso denegado"), "text" => $this->get('translator')->trans("no.ha.seleccionado.mentor.ov")));
			return $this->redirect($this->generateUrl('lista_mentores_ov'));
		}
        
        return array(
            'informacion' => $informTV,
            'linkTestVocacional' => $enlaceTV
        );
    }

    
}
?>
