<?php

namespace AT\vocationetBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador de test vocacional
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
        $seleccionarMentor = $this->get('perfil')->confirmarMentorOrientacionVocacional($usuarioId);

        if($seleccionarMentor) {
			$formulario = $this->get('formularios')->getInfoFormulario(8);
		} else {
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("Acceso denegado"), "text" => $this->get('translator')->trans("no.ha.seleccionado.mentor.ov")));
			return $this->redirect($this->generateUrl('lista_mentores_ov'));
		}
        
        return array(
			'formulario_info' => $formulario,
        );
    }
}
?>
