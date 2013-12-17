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
     * @return Response redirecciona a compraAction
     */
    public function agregarProductoAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        
        $pagos = $this->get('pagos');
        
        $productos = $this->get('session')->get('productos');
        //$security->debug($productos);
        
        // Validar id de producto existente
        if(in_array($id, $pagos->getProductoId()))
        {            
            if(!is_array($productos) || !isset($productos[$id]) || $id == $pagos->getProductoId('mentoria_ov'))// Validar que no se repita o sea una mentoria ov
            {
                // Validar que si agrega el plan completo no pueda agregar mas productos                
                if($id == $pagos->getProductoId('programa_orientacion')) // si agrega el plan completo borrar los demas productos
                {
                   if(count($productos) >= 1)
                   {
                        $this->get('session')->getFlashBag()->add('alerts', array("type" => "warning", "title" => $this->get('translator')->trans("productos.eliminados"), "text" => $this->get('translator')->trans("si.compra.plan.completo.no.puede.agregar.productos")));
                   }
                   $productos = array(); // vaciar carrito 
                }
                // Si no se ha agregado el plan completo permitir agregar producto
                if(!isset($productos[$pagos->getProductoId('programa_orientacion')]))
                {
                    $producto = $pagos->getProducto($id);
                    $key = $producto['id'];
                    if($id == $pagos->getProductoId('mentoria_ov')) $key = uniqid();
                    $productos[$key] = $producto;  
                }
                elseif($id != $pagos->getProductoId('programa_orientacion'))
                {
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "warning", "title" => $this->get('translator')->trans("no.puede.agregar.producto"), "text" => $this->get('translator')->trans("si.compra.plan.completo.no.puede.agregar.productos")));
                }
                
            }
        }
        
        $this->get('session')->set('productos', $productos);
        
        //return new Response();
        
        return $this->redirect($this->generateUrl('comprar'));
    }
    
    /**
     * Accion para eliminar producto del carrito de compras
     * 
     * @Route("/eliminar/{key}", name="eliminar_producto")
     * @param integer $key key de producto en array
     * @return Response redirecciona a compraAction
     */
    public function eliminarProductoAction($key)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        
        $productos = $this->get('session')->get('productos');
        
        unset($productos[$key]);
        
        $this->get('session')->set('productos', $productos);        
        
        return $this->redirect($this->generateUrl('comprar'));
    }
    
    /**
     * Accion para seleccionar mentoria con mentor profesional
     * 
     * @Route("/mentores", name="pagos_mentorias")
     * @Template("vocationetBundle:Pagos:mentores.html.twig")
     * @return Response
     */
    public function mentoresAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        
        $usuarioId = $security->getSessionValue('id');
        
        $mentores = $this->getMentoresUsuario($usuarioId);
        
        if(!$mentores)
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "warning", "title" => $this->get('translator')->trans("debe.seleccionar.mentor.experto"), "text" => $this->get('translator')->trans("debe.seleccionar.mentor.para.comprar.mentorias")));
            return $this->redirect($this->generateUrl('red_mentores'));
        }
        
        return array(
            'mentores' => $mentores
        );
    }

    /**
     * Accion para agregar producto mentoria al carrito de compras
     * 
     * @Route("/agregar_mentoria/{id}", name="agregar_producto_mentoria")
     * @param integer $id id de mentor
     * @return Response redirecciona a compraAction
     */
    public function agregarMentoriaAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        
        $pagos = $this->get('pagos');
        
        $productoId = $pagos->getProductoId('mentoria_profesional');
        
        $productos = $this->get('session')->get('productos');
        
        //$security->debug($productos);
        
        // Si no se ha agregado el plan completo permitir agregar producto
        if(!isset($productos[$pagos->getProductoId('programa_orientacion')]))
        {
            $mentor = $this->getMentor($id);            
            
            $producto = $pagos->getProducto($productoId);
            $producto['mentorId'] = $id;
            $producto['mentor'] = $mentor['usuarioNombre']." ".$mentor['usuarioApellido'];
            $producto['valor'] = $mentor['usuarioValorMentoria'];
            $key = uniqid();
            
            $productos[$key] = $producto;  
        }
        else
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "warning", "title" => $this->get('translator')->trans("no.puede.agregar.producto"), "text" => $this->get('translator')->trans("si.compra.plan.completo.no.puede.agregar.productos")));
        }
        
        $this->get('session')->set('productos', $productos);
        
        //return new Response();
        
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
    
    /**
     * Funcion que obtiene los mentores profesionales del usuario
     * 
     * @param integer $usuarioId id de usuario estudiante
     * @return array
     */
    private function getMentoresUsuario($usuarioId)
    {
        $dql = "SELECT 
                    u.id,
                    u.usuarioNombre,
                    u.usuarioApellido,
                    u.usuarioImagen,
                    u.usuarioValorMentoria
                FROM
                    vocationetBundle:Usuarios u
                    JOIN vocationetBundle:Relaciones r WITH r.usuario = u.id OR r.usuario2 = u.id
                WHERE
                    (r.usuario = :usuarioId OR r.usuario2 = :usuarioId)
                    AND u.id != :usuarioId
                    AND r.tipo = 3
                    AND r.estado = 1
        ";         
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $result = $query->getResult();
        
        return $result;
    }
    
    /**
     * Funcion para obtener informacion de un mentor
     * 
     * @param integer $mentorId id de mentor
     * @return array
     */
    private function getMentor($mentorId)
    {
        $dql = "SELECT u.usuarioNombre, u.usuarioApellido, u.usuarioValorMentoria
                FROM vocationetBundle:Usuarios u 
                WHERE u.id = :mentorId";
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('mentorId', $mentorId);
        $query->setMaxResults(1);
        $result = $query->getResult();
        
        $mentor = false;
        if($result)
        {
            $mentor = $result[0];
        }
        
        return $mentor;
    }
}
