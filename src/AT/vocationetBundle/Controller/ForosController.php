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
 * @Route("/carrera")
 */
class ForosController extends Controller
{
    /**
     * Listado de foros y temas de la carrera que ingresa por parametro
     *
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @Route("_{id}/temas_{temaId}", name="foros_temas", defaults={"temaId" = 0})
     * @Template("vocationetBundle:Foros:index.html.twig")
     * @Method("GET")
     * @return Render
     */
    public function indexAction($id, $temaId)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        //if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$em = $this->getDoctrine()->getManager();
        $carreras = $em->getRepository('vocationetBundle:Carreras')->findAll();
        $temas = $this->getTemasCountForos($id);
		$foros = $this->ForosCarrerasTemas($id, $temaId);
        //$carreras =$this->getCarrerasCountTemas();

        $PosActual = Array('carreraId' => $id, 'temaId' => $temaId);
        return array('carreras' => $carreras, 'temas' => $temas, 'id' => $id, 'actual' => $PosActual, 'foros' => $foros);
    }

	/**
	 * Función que retorna el listado de temas de una carrera y cantidad de foros por tema
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @return Array Arreglo con datos del tema, y cantidad de foros
	 */
    private function getTemasCountForos($carreraId)
    {
		$em = $this->getDoctrine()->getManager();
		$dql = "SELECT t.id, t.nombre, COUNT(f.id) as cantidadForos
				FROM vocationetBundle:Temas t
				LEFT JOIN vocationetBundle:Foros f WITH f.tema = t.id
				WHERE t.carrera =:carreraId
				GROUP BY t.id";
		$query = $em->createQuery($dql);
		$query->setParameter('carreraId', $carreraId);
		return $query->getResult();
	}

	/**
	 * Funcion que trae los foros de la carrera y/o tema filtrados
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $carreraId Id de la carrera
     * @param Int $temaId Id del tema por el que se quiere filtrar (valor default = 0)
     * @return Array Arreglo con foros que estan incluidos en el filtro
	 */
	private function ForosCarrerasTemas($carreraId, $temaId)
	{
		$auxWhere = '';
		if ($temaId) {
			$auxWhere = ' AND t.id ='.$temaId;
		}
		$em = $this->getDoctrine()->getManager();
		/**
		 * SELECT * FROM foros f
		 * INNER JOIN temas t ON t.id = f.tema_id
		 * INNER JOIN carreras c ON c.id = t.carrera_id
		 * LEFT JOIN comentarios com ON com.foro_id = f.id;;
		 */
		$dql = "SELECT f.id, f.titulo, f.created, t.nombre AS temaNombre, COUNT(comment.id) AS countComent
				FROM vocationetBundle:Foros f
				JOIN f.tema t
				JOIN t.carrera c
				LEFT JOIN vocationetBundle:Comentarios comment WITH comment.foro = f.id
				WHERE c.id =:carreraId".$auxWhere."
				GROUP BY f.id";
		$query = $em->createQuery($dql);
		$query->setParameter('carreraId', $carreraId);
		return $query->getResult();
	}
}
?>
