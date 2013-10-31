<?php
namespace AT\vocationetBundle\Services;

use AT\vocationetBundle\Entity\Mensajes;
use AT\vocationetBundle\Entity\MensajesUsuarios;

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
    var $file;
        
    function __construct($doctrine, $session, $mail, $file) 
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->mail = $mail;
        $this->file = $file;
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

    /**
     * Funcion para enviar un mensaje
     * 
     * @param integer $fromUsuarioId id de usuario que envia el mensaje
     * @param array $toList arreglo de ids de usuarios
     * @param string $subject asunto del mensaje
     * @param string $message cuerpo del mensaje
     * @return boolean
     */
    public function enviarMensaje($fromUsuarioId, $toList, $subject, $message, $attachment = null)
    {
        $em = $this->doctrine->getManager();
        
        // Mensaje
        $mensaje = new Mensajes();
        $mensaje->setAsunto($subject);
        $mensaje->setContenido($message);
        $mensaje->setFechaEnvio(new \DateTime());
        
        $em->persist($mensaje);
        
        // Registro de emisor
        $mensajeFrom = new MensajesUsuarios();
        $mensajeFrom->setMensaje($mensaje);
        $mensajeFrom->setUsuario($fromUsuarioId);
        $mensajeFrom->setTipo(1); // from
        $mensajeFrom->setEstado(1); // leido
        
        $em->persist($mensajeFrom);
        
        // Registro de receptores
        foreach($toList as $toId)
        {
            $mensajeTo = new MensajesUsuarios();
            $mensajeTo->setMensaje($mensaje);
            $mensajeTo->setUsuario($toId);
            $mensajeTo->setTipo(2); // to
            $mensajeTo->setEstado(0); // sin leer           
            
            $em->persist($mensajeTo);
        }
        
        // Subir adjuntos
        if($attachment)
        {
            $files_id = $this->file->upload($attachment, 'mensajes/');
            
            // Registrar relacion mensaje_archivos
            foreach($files_id as $fi)
            {
                 $mensajeArchivos = new \AT\vocationetBundle\Entity\MensajesArchivos();
                 $mensajeArchivos->setMensaje($mensaje);
                 $mensajeArchivos->setArchivo($fi);
                 
                 $em->persist($mensajeArchivos);
            }
        }
        
        $em->flush();
        
        return true;
    }
    
}
?>
