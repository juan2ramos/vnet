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
     * Verifica si existe un producto disponible en una orden pagada
     * 
     * @param integer $productoId id de producto
     * @param integer $usuarioId id de usuario
     * @param integer $mentorId id de mentor en caso de pago de mentorias individuales
     * @return boolean|integer id de registro en OrdenesProductos o false si no existe el pago
     */
    public function verificarPagoProducto($productoId, $usuarioId, $mentorId = false)
    {
        $pago = false;
        
        $dql = "SELECT op.id
                FROM vocationetBundle:Ordenes o
                JOIN vocationetBundle:OrdenesProductos op WITH op.orden = o.id
                JOIN vocationetBundle:Productos p WITH op.producto = p.id
                WHERE
                    o.usuario = :usuarioId
                    AND o.estado = 1
                    AND p.id = :productoId
                    AND op.estado = 0
                 ";
        if($mentorId)
        {
            $dql .= " AND op.mentorId = :mentorId ";
        }
        
        $query = $this->em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $query->setParameter('productoId', $productoId);
        if($mentorId) $query->setParameter('mentorId', $mentorId);
        $query->setMaxResults(1);
        $result = $query->getResult();
        
        if($result)
        {
            $pago = $result[0]['id'];
        }
        
        return $pago;
    }
    
    /**
     * Funcion que marca un producto de una orden como usado
     * 
     * Se utiliza para impedir que el usuario puede acceder varias veces a
     * un mismo producto con un solo pago
     * 
     * @param integer $ordeProductoId id del registro en OrdenesProductos
     */
    public function marcarPagoUsado($ordeProductoId)
    {
        if(is_numeric($ordeProductoId))
        {
            // Marcar producto como usado
            $dql = "UPDATE vocationetBundle:OrdenesProductos op 
                    SET op.estado = 1
                    WHERE op.id = :ordenProductoId ";
            $query = $this->em->createQuery($dql);
            $query->setParameter('ordenProductoId', $ordeProductoId);
            $query->getResult();
        }
    }
}