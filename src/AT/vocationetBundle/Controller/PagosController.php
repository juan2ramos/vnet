<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * Controlador para pagos
 *
 * @Route("/planes")
 * @author Diego Malagón <diego@altactic.com>
 */
class PagosController extends Controller
{
    /**
     * Seccion de informacion
     * 
     * @Route("/", name="planes")
     * @Template("vocationetBundle:Pagos:planes.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        
        $precios = $this->getPrecios();        
        
        return array(
            'precio_programa_orientacion' => $precios[1],
            'precio_mercado_laboral' => $precios[2],
            'precio_mentoria_ov' => $precios[4]
        );
    }
    
    
    /**
     * Funcion para obtener los precios de los productos
     * 
     * @return array arreglo de precios
     */
    private function getPrecios()
    {
        $dql = "SELECT p.id, p.valor FROM vocationetBundle:Productos p";
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $result = $query->getResult();
        
        $precios = array();
        foreach($result as $r)
        {
            $precios[$r['id']] = $r['valor'];
        }
        
        return $precios;        
    }
}
