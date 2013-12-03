<?php

namespace AT\vocationetBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador de mercado laboral
 * @package vocationetBundle
 * @Route("/mercadolaboral")
 * @author Camilo Quijano <camilo@altactic.com>
 * @version 1
 */
class MercadoLaboralController extends Controller
{
    /**
     * Index de test vocacional
     * 
     * @Route("/", name="mercado_laboral")
     * @Template("vocationetBundle:MercadoLaboral:index.html.twig")
     * @Method({"GET", "POST"})
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		//if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$usuarioId = $security->getSessionValue('id');
        $seleccionarMentor = $this->get('perfil')->confirmarMentorOrientacionVocacional($usuarioId);

        if($seleccionarMentor) {
			$formulario = $this->get('formularios')->getInfoFormulario(11);

			
			if ($request->getMethod() == "POST") {
				$alternativas = $request->request->get('carrerasSeleccionadas');
				print_r($alternativas);
				print('entro aqui');
				
			}
			
		} else {
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("Acceso denegado"), "text" => $this->get('translator')->trans("no.ha.seleccionado.mentor.ov")));
			return $this->redirect($this->generateUrl('lista_mentores_ov'));
		}
		
		$em = $this->getDoctrine()->getManager();
        $carreras = $em->getRepository('vocationetBundle:Carreras')->findAll();
        
        return array(
			'formulario_info' => $formulario,
			'carreras' => $carreras,
        );
    }
}
?>
