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
 * @Route("/pagos")
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
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
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
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
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
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
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
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
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
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
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
     * Accion para ver los productos de una compra
     * 
     * @Route("/compra", name="comprar")
     * @Template("vocationetBundle:Pagos:compra.html.twig")
     * @return Response
     */
    public function compraAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));}
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $productos = $this->get('session')->get('productos');
        $usuarioEmail = $security->getSessionValue("usuarioEmail");
        
        $total = 0;
        if(count($productos))
        {
            foreach($productos as $p)
            {
                $total += $p['valor'];
            }
        }
        
        $totales = $this->get('pagos')->calcularTotales($total);
        
        $form = $this->createFormCompra();
        
        return array(
            'productos' => $productos,
            'subtotal' => $totales['subtotal'],
            'iva' => $totales['iva'],
            'total' => $totales['total'],
            'form' => $form->createView()
        );
    }
      
    /**
     * Accion para confirmar una compra
     * 
     * @Route("/confirmar_compra", name="confirmar_comprar")
     * @Method({"POST"})
     * @Template("vocationetBundle:Pagos:confirmar_compra.html.twig")
     * @return Response
     */
    public function confirmarCompraAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));}
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $productos = $this->get('session')->get('productos');
        $usuarioId = $security->getSessionValue("id");
        $usuarioEmail = $security->getSessionValue("usuarioEmail");
        
        $total = 0;
        if(count($productos))
        {
            foreach($productos as $p)
            {
                $total += $p['valor'];
            }
        }
        
        $totales = $this->get('pagos')->calcularTotales($total);
        
        $form = $this->createFormCompra();        
        
        $form->bind($request);
        if ($form->isValid())
        {
            if(count($productos) > 0)
            {
                $codigo = $this->get('pagos')->registrarCompra($usuarioId, $productos, $totales);
                
                $PayU = $this->get('payu');
                $payUDataForm = $PayU->generateDataFormWebCheckout($usuarioEmail, $codigo, $totales['total'], $totales['iva'], $totales['subtotal']);
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("seleccione.productos"), "text" => $this->get('translator')->trans("no.ha.seleccionado.productos")));
                return $this->redirect($this->generateUrl('comprar'));
            }
        }
        else
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
            return $this->redirect($this->generateUrl('comprar'));
        }
        
        return array(
            'productos' => $productos,
            'subtotal' => $totales['subtotal'],
            'iva' => $totales['iva'],
            'total' => $totales['total'],
            'form' => $form->createView(),
            'payu_form_action' => $PayU->getWebCheckoutUrl(),
            'payu_data_form' => $payUDataForm
        );
    }
        
    /**
     * @Route("/payuresponse", name="payu_response")
     * @Template("vocationetBundle:Pagos:compra.html.twig")
     * @Method({"GET"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function responsePayUAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));}
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
                
        $pagos = $this->get('pagos');
        $PayU = $this->get('payu');
        
        $transaccion = $PayU->processResponse($request);
        
        if($transaccion['status'] == 'success')
        {
            // Activar orden
            $codigo = $transaccion['referenceCode'];
            $pagos->activarOrden($codigo);
            
            // Enviar notificacion
            $usuarioEmail = $security->getSessionValue("usuarioEmail");
            $this->enviarNotificacionProductos($usuarioEmail);
            
            // Vaciar carrito
            $this->get('session')->set('productos', null);
            
            $message = $this->get('translator')->trans("gracias.por.adquirir.nuestros.productos").".<br/><br/>".$transaccion['message'];
            
            return $this->forward("vocationetBundle:Alerts:alertScreen", array(
                "type" => "success",
                "title" => $this->get('translator')->trans("compra.finalizada"),
                "message" => $message
            )); 
            
//            $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("compra.finalizada"), "text" => $this->get('translator')->trans("gracias.por.adquirir.nuestros.productos"), "time" => 10000));
//            return $this->redirect($this->generateUrl('homepage'));
        }
        else
        {
            return $this->forward("vocationetBundle:Alerts:alertScreen", array(
                "type" => "error",
                "title" => $this->get('translator')->trans("error.transaccion"),
                "message" => $transaccion['message']
            ));
            
//            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.transaccion"), "text" => $transaccion['status'], "time" => 10000));
//            return $this->redirect($this->generateUrl('comprar'));
        }
        
        
        return new Response();
    }
    
    /**
     * @Route("/payuconfirmation", name="payu_confirmation")
     * @Template("vocationetBundle:Pagos:compra.html.twig")
     * @Method({"POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function confirmationPayUAction(Request $request)
    {
        // Esta accion no hace validacion de autenticacion ni autorizacion porque la peticion es hecha por PayU
        
        $pagos = $this->get('pagos');
        $PayU = $this->get('payu');
        
        $transaccion = $PayU->processConfirmation($request);
        $usuarioEmail = $transaccion['buyerEmail'];
        $codigo = $transaccion['referenceCode'];
        
        if($transaccion !== false)
        {
            // Activar orden
            $pagos->activarOrden($codigo, 1, true);
            
            // Enviar notificacion
            $this->enviarNotificacionProductos($usuarioEmail);
        }
        else
        {
            // Desactivar orden
            $pagos->activarOrden($codigo, 0, true);
            
            // Enviar notificacion de error
            $this->enviarNotificacionError($usuarioEmail);
        }
        
        return new Response();
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
    
    /**
     * Funcion para crear un formulario vacio
     * 
     * Se usa unicamente para proteccion csrf
     * 
     * @return Object formulario
     */
    private function createFormCompra()
    {
        $form = $this->createFormBuilder()
            ->getForm();
        
        return $form;
    }
    
    /**
     * Funcion para enviar la notificacion de activacion de productos al usuario
     * 
     * se envia un correo al usuario indicandole que los productos ya fueron activados
     * 
     * @param string $email email del usuario
     */
    private function enviarNotificacionProductos($email)
    {
        $mailer = $this->get('mail');
        
        $subject = $this->get('translator')->trans("activacion.productos", array(), 'mail');
        
        $message = $this->get('translator')->trans("mensaje.activacion.productos", array(), 'mail');
        
        $link = $this->get('request')->getSchemeAndHttpHost().$this->get('router')->generate('login'); 
        $dataRender = array(
            'title' => $subject,
            'body' => $message,
            'link' => $link,
            'link_text' => $this->get('translator')->trans("ingresar", array(), 'mail')
        );
        
        $mailer->sendMail($email, $subject, $dataRender);
    }
    
    /**
     * Funcion para enviar una notificacion cuando ocurre un error en la transaccion
     * 
     * se envia un correo al usuario indicandole que los productos no fueron activados porque la transaccion fue rechazada
     * 
     * @param string $email email del usuario
     */
    private function enviarNotificacionError($email)
    {
        $mailer = $this->get('mail');
        
        $subject = $this->get('translator')->trans("error.activacion.productos", array(), 'mail');
        
        $message = $this->get('translator')->trans("mensaje.error.activacion.productos", array(), 'mail');
        
        $link = $this->get('request')->getSchemeAndHttpHost().$this->get('router')->generate('login'); 
        $dataRender = array(
            'title' => $subject,
            'body' => $message,
            'link' => $link,
            'link_text' => $this->get('translator')->trans("ingresar", array(), 'mail')
        );
        
        $mailer->sendMail($email, $subject, $dataRender);
    }
}
