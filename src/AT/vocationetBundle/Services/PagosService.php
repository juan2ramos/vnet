<?php

namespace AT\vocationetBundle\Services;

use AT\vocationetBundle\Services\SecurityService as SecurityService;

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
    protected $serv_cont;
    
    /**
     * Entity manager  
     * 
     * @var Object 
     */
    protected $em;
    
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
     * Funcion para obtener el id de un producto
     * 
     * No hace consulta a la base de datos
     * 
     * @param string|boolean $nombre nombre del producto
     * @return integer|boolean|array id de formulario o false si no existe o array completo si no se pasa nombre
     */
    public function getProductoId($nombre = false)
    {
        $ids = array(
            'programa_orientacion' => 1,
            'informe_mercado_laboral' => 2,
            'mentoria_profesional' => 3,
            'mentoria_ov' => 4
        );
        
        if($nombre)
        {
            $id = (isset($ids[$nombre])) ? $ids[$nombre] : false;

            return $id;
        }
        else
        {
            return $ids;
        }
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
    
    /**
     * Funcion para obtener un producto
     * 
     * @param integer $productoId id de producto
     * @return array arreglo de producto
     */
    public function getProducto($productoId)
    {
        $dql = "SELECT p.id, p.nombre, p.valor
                FROM vocationetBundle:Productos p
                WHERE p.id = :productoId";
        $query = $this->em->createQuery($dql);
        $query->setParameter("productoId", $productoId);
        $query->setMaxResults(1);
        $result = $query->getResult();

        if($result)
        {
            return $result[0];
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Funcion que calcula el iva de un valor
     * 
     * se basa en la configuracion de iva
     * 
     * @param float $valor 
     * @return float valor del iva
     */
    public function getIva($valor)
    {
        $porc_iva = SecurityService::getParameter('iva');
        
        $iva = ($porc_iva * $valor) / 100;        
        
        return $iva;
    }
    
    /**
     * Funcion para calcular totales de un total de compra
     * 
     * @param float $valor
     * @return array arreglo con subtotal, iva y total
     */
    public function calcularTotales($valor)
    {
        $iva = $this->getIva($valor);
        
        $tipo_iva = SecurityService::getParameter('tipo_iva');
        
        if($tipo_iva == 'incluido')
        {
            $total = $valor;
            $subtotal = $total - $iva;
        }
        elseif($tipo_iva == 'agregado')
        {
            $subtotal = $valor;
            $total = $subtotal + $iva;
        }
        
        return array(
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total
        );
    }
    
    /**
     * Funcion para registrar la orden y sus productos en la db
     * 
     * La orden se registra con estado 0
     * 
     * @param integer $usuarioId id de usuario
     * @param array $productos arreglo de productos del carrito
     * @param array $totales arreglo con los totales de la compra
     * @return string Codigo de la orden
     */
    public function registrarCompra($usuarioId, $productos, $totales)
    {        
        $codigo = uniqid($usuarioId);
        
        $orden = new \AT\vocationetBundle\Entity\Ordenes();
        $orden->setCodigo($codigo);
        $orden->setFechaHoraCompra(new \DateTime());
        $orden->setUsuario($usuarioId);
        $orden->setSubtotal($totales['subtotal']);
        $orden->setIva($totales['iva']);
        $orden->setTotal($totales['total']);
        $orden->setEstado(0);
        $orden->setConfirmacion(0);
        
        $this->em->persist($orden);
        
        foreach($productos as $p)
        {
            $ordenProducto = new \AT\vocationetBundle\Entity\OrdenesProductos();
            $ordenProducto->setProducto($p['id']);
            $ordenProducto->setOrden($orden);
            if(isset($p['mentorId']))
            {
                $ordenProducto->setMentorId($p['mentorId']);
            }
            $ordenProducto->setValor($p['valor']);
            $ordenProducto->setEstado(0);
            
            $this->em->persist($ordenProducto);
        }
        
        $this->em->flush();
        
        return $codigo;
    }
           
    /**
     * Funcion para activar o desactivar una orden de pago
     * 
     * @param string $codigo codigo de la orden
     * @param integer $estado indica si esta activada o no {1|0}
     * @param boolean $confirmada indica si la transaccion fue confirmada
     */
    public function activarOrden($codigo, $estado = 1, $confirmada = false)
    {
        $dql = "UPDATE vocationetBundle:Ordenes o
                    SET o.estado = :estado,
                    o.confirmacion = :confirmacion
                WHERE 
                    o.codigo = :codigo
                ";
        $query = $this->em->createQuery($dql);
        $query->setParameter('estado', $estado);
        $query->setParameter('confirmacion', $confirmada);
        $query->setParameter('codigo', $codigo);
        $query->setMaxResults(1);
        $query->getResult();
    }    
}