<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador de foros
 * @package vocationetBundle
 * @Route("/foros")
 */
class ForosController extends Controller
{
    /**
     * Listado de carreras
     *
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @Route("/", name="foros_carreras")
     * @Template("vocationetBundle:Foros:index.html.twig")
     * @return Render
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        //if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		
        //$carreras = $em->getRepository('vocationetBundle:Carreras')->findAll();
        //$carrreras =$this->getCarrerasCountTemas();
        
        $carreras = Array();
        return array('carreras' => $carreras);
    }

	/**
	 * Función que retorna el listado de carreras con cantidad de temas por carrera
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @return Array Arreglo con datos de las carreras y temas registrados
	 */
    private function getCarrerasCountTemas()
    {
		$em = $this->getDoctrine()->getManager();
		/**
		 * @var String Consulta SQL con carreras y cantidad de temas de esas carreras
		 * SELECT c.*, count(t.id) FROM carreras c
		 * LEFT JOIN temas t ON t.carrera_id = c.id
		 * GROUP BY c.id;
		 */
		$dql = "SELECT c.id, c.nombre, COUNT(t.id) as cantidadTemas
				FROM vocationetBundle:Carreras c
				LEFT JOIN vocationetBundle:Temas t WITH t.carrera = c.id
				GROUP BY c.id";
		$query = $em->createQuery($dql);
		return $query->getResult();
	}
}
?>
