<?php
namespace AT\vocationetBundle\Services;

/**
 * Servicio para el control de mensajes en la aplicacion
 * 
 * @author Diego Malagón <diego@altactic.com>
 */
class MensajesService
{
    var $doctrine;
    var $session;
    var $mail;
        
    function __construct($doctrine, $session, $mail) 
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->mail = $mail;
    }
    
    /**
     * Funcion que obtiene la lista de usuarios para enviar un mensaje
     * 
     * Obtiene los usuarios a los cuales el usuario logueado puede enviarle mensajes
     * 
     * @param integer $usuarioId id de usuario logueado
     * @return array arreglo de usuarios
     */
    public function getToList($usuarioId)
    {
        $toList = false;
        $em = $this->doctrine->getManager();
        $dql = "SELECT
                    u.id, 
                    u.usuarioNombre,
                    u.usuarioApellido
                FROM 
                    vocationetBundle:Usuarios u
                WHERE 
                    u.usuarioEstado = 1
                    AND u.id != :usuarioId";
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $result = $query->getResult();
        
        if(count($result)>0)
        {
            foreach($result as $r)
            {
                $toList[$r['id']] = $r['usuarioNombre'].' '.$r['usuarioApellido'];
            }
        }
        
        return $toList;
    }

    
    public function enviarMensaje($fromUsuarioId, $toList, $subject, $message)
    {
        return true;
    }
    
}
?>
