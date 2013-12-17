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
        
        $productosId = $this->get('pagos')->getProductoId();
        
        return array(
            'precio_programa_orientacion' => $precios[1],
            'precio_mercado_laboral' => $precios[2],
            'precio_mentoria_ov' => $precios[4],
            'productos_ids' => $productosId
        );
    }
    
    /**
     * Accion para agregar producto al carrito de compras
     * 
     * @Route("/agregar/{id}", name="agregar_producto")
     * @param integer $id id de producto
     * @return Response
     */
    public function agregarProductoAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        
        $productos = $this->get('session')->get('productos');
        $security->debug($productos);
        
        // Validar id de producto existente
        if(in_array($id, $this->get('pagos')->getProductoId()))
        {
            $producto = $this->get('pagos')->getProducto($id);
            
            if(!is_array($productos) || !in_array($producto, $productos))// Validar que no se repita
            {
                $productos[] = $producto;
            }
        }
        
        $this->get('session')->set('productos', $productos);
        
        return $this->redirect($this->generateUrl('comprar'));
    }
    
    /**
     * Accion para finalizar una compra
     * 
     * @Route("/compra", name="comprar")
     * @Template("vocationetBundle:Pagos:compra.html.twig")
     * @return Response
     */
    public function compraAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));}
        
        $productos = $this->get('session')->get('productos');
        
        $total = 0;
        if(count($productos))
        {
            foreach($productos as $p)
            {
                $total += $p['valor'];
            }
        }
        
        $totales = $this->get('pagos')->calcularTotales($total);
        
        return array(
            'productos' => $productos,
            'subtotal' => $totales['subtotal'],
            'iva' => $totales['iva'],
            'total' => $totales['total']
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
