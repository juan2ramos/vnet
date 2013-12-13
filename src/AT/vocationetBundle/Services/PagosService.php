<?php

namespace AT\vocationetBundle\Services;

/**
 * Servicio para el control de pagos en la aplicacion
 * 
 * @author Diego Malagón <diego@altactic.com>
 */
class PagosService
{
    /**
     * Contenedor de servicios
     * 
     * @var Object 
     */
    var $serv_cont;
    
    /**
     * Entity manager  
     * 
     * @var Object 
     */
    var $em;
    
    /**
     * Constructor
     * 
     * @param Object $service_container contenedor de servicios
     */
    function __construct($service_container) 
    {
        $this->serv_cont = $service_container;
        $this->em = $service_container->get('doctrine')->getManager();
    }
    
    
    /**
     * Funcion que verifica si el usuario tiene un pago activo para un producto
     * 
     * @param integer $productoId id de producto
     * @param integer $usuarioId id de usuario
     */
    public function verificarPagoProducto($productoId, $usuarioId)
    {
        $dql = "SELECT COUNT(o.id) c FROM vocationetBundle:Ordenes o
                JOIN vocationetBundle:OrdenesProductos op WITH op.orden = o.id
                WHERE 
                    op.producto = :productoId
                    AND o.usuario = :usuarioId
                    AND o.estado = 1";
        $query = $this->em->createQuery($dql);
        $query->setParameter("productoId", $productoId);
        $query->setParameter("usuarioId", $usuarioId);
        $result = $query->getResult();
        
        
        $pago = false;
        
        if(isset($result[0]['c']))
        {
            if($result[0]['c'] == 1)
            {
                $pago = true;
            }
        }
        
        return $pago;
    }
}