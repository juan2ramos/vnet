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
    var $serv_cont;
    var $doctrine;
    var $session;
    var $mail;
    var $file;
        
    function __construct($service_container) 
    {
        $this->serv_cont = $service_container;
        $this->doctrine = $service_container->get('doctrine');
        $this->session = $service_container->get('session');
        $this->mail = $service_container->get('mail');
        $this->file = $service_container->get('file');
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
                    JOIN vocationetBundle:Relaciones r WITH r.usuario = u.id OR r.usuario2 = u.id
                WHERE 
                    (r.usuario = :usuarioId OR r.usuario2 = :usuarioId)
                    AND u.id != :usuarioId
                    AND r.estado = 1
                    AND u.usuarioEstado = 1";
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
     * @param integer $mensajeId id de mensaje padre
     * @return Object entidad de nuevo mensaje
     */
    public function enviarMensaje($fromUsuarioId, $toList, $subject, $message, $attachment = null, $mensajeId = null)
    {
        $em = $this->doctrine->getManager();
        
        // Mensaje
        $mensaje = new Mensajes();
        $mensaje->setAsunto($subject);
        $mensaje->setContenido($message);
        $mensaje->setFechaEnvio(new \DateTime());
        if($mensajeId)
        {
            $mensajePadre = $em->getRepository('vocationetBundle:Mensajes')->findOneById($mensajeId);
            $mensaje->setMensaje($mensajePadre);
        }
        
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
        
        // Enviar notificacion al correo
        $emails = $this->getEmailAddress($toList);
        $subject_mail = $this->serv_cont->get('translator')->trans("tiene.un.nuevo.mensaje", array(), 'mail');
        
        $link = $this->serv_cont->get('request')->getSchemeAndHttpHost().$this->serv_cont->get('router')->generate('mensajes'); 
        $dataRender = array(
            'title' => $subject,
            'body' => $message,
            'link' => $link,
            'link_text' => $this->serv_cont->get('translator')->trans("ir.a.inbox", array(), 'mail')
        );
        
        $this->mail->sendMail($emails, $subject_mail, $dataRender);
        
        return $mensaje;
    }
    
    /**
     * Funcion que obtiene los mensajes de bandeja de entrada
     * 
     * @param integer $usuarioId id de usuario
     * @param integet $tipoMensaje tipo de mensaje, 1: enviado, 2:recibido
     * @param integer $estadoMensaje estado de mensaje, 0: sin leer, 1: leido, 2:eliminado
     */
    public function getMensajes($usuarioId, $tipoMensaje = 2, $estadoMensaje = null)
    {
        $em = $this->doctrine->getManager();
        
        $dql = "SELECT
                    m.id,
                    m.asunto,
                    u.usuarioNombre,
                    u.usuarioApellido,
                    m.fechaEnvio,
                    mu.estado,
                    COUNT (ma.id) adjuntos
                FROM
                    vocationetBundle:Mensajes m
                    INNER JOIN vocationetBundle:MensajesUsuarios mu WITH m.id = mu.mensaje
                    LEFT JOIN vocationetBundle:MensajesArchivos ma WITH m.id = ma.mensaje
                    INNER JOIN vocationetBundle:MensajesUsuarios fu WITH m.id = fu.mensaje AND fu.tipo = 1
                    INNER JOIN vocationetBundle:Usuarios u WITH u.id = fu.usuario 
                WHERE 
                    mu.usuario = :usuarioId
                ";
        if($tipoMensaje != 0)                     
            $dql.= " AND mu.tipo = :tipoMensaje  ";
            
        if($estadoMensaje !== null)        
            $dql.= " AND mu.estado = :estadoMensaje ";
        else
            $dql.= " AND (mu.estado = 0 OR mu.estado = 1)";
        
        $dql.= "GROUP BY m.id
                ORDER BY m.fechaEnvio DESC";
        
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        
        if($tipoMensaje != 0)
            $query->setParameter('tipoMensaje', $tipoMensaje);
        
        if($estadoMensaje !== null) 
            $query->setParameter('estadoMensaje', $estadoMensaje);
        
        $result = $query->getResult();
        
        return $result;
    }
    
    /**
     * Funcion para obtener el contenido de un mensaje
     * 
     * @param integer $mensajeId id de mensaje
     */
    public function getMensaje($usuarioId, $mensajeId)
    {
        $em = $this->doctrine->getManager();
        
        // Consultar mensaje
        $dql = "SELECT
                    m.id,
                    m.asunto,
                    m.contenido,
                    u.id usuarioId,
                    u.usuarioNombre,
                    u.usuarioApellido,
                    u.usuarioImagen,
                    m.fechaEnvio,
                    mu.estado
                FROM
                    vocationetBundle:Mensajes m
                    INNER JOIN vocationetBundle:MensajesUsuarios mu WITH m.id = mu.mensaje AND mu.usuario = :usuarioId
                    INNER JOIN vocationetBundle:MensajesUsuarios fu WITH m.id = fu.mensaje AND fu.tipo = 1
                    INNER JOIN vocationetBundle:Usuarios u WITH u.id = fu.usuario 
                WHERE 
                    m.id = :mid
                    ";
        $query = $em->createQuery($dql);
        $query->setParameter('mid', $mensajeId);
        $query->setParameter('usuarioId', $usuarioId);
        $query->setMaxResults(1);
        $result = $query->getResult();
        
        if(count($result)==1)
        {
            $mensaje = $result[0];
            
            
            //Consultar usuarios relacionados
            $dql = "SELECT u.id, u.usuarioNombre, u.usuarioApellido 
                    FROM vocationetBundle:Usuarios u
                    JOIN vocationetBundle:MensajesUsuarios mu WITH u.id = mu.usuario
                    WHERE mu.mensaje = :mid
                    AND mu.tipo = 2";
            $query = $em->createQuery($dql);
            $query->setParameter('mid', $mensajeId);
            $toList = $query->getResult();
            
            // Consultar archivos adjuntos
            $adjuntos = $this->getAdjuntosMensaje($mensajeId);
        }
        
        
        return array(
            'mensaje' => $mensaje,
            'toList' => $toList,
            'adjuntos' => $adjuntos
        );
    }
        
    /**
     * Funcion para obtejer la lista de adjuntos de un mensaje
     * 
     * @param integer $mensajeId id de mensaje
     * @return array arreglo de adjuntos
     */
    public function getAdjuntosMensaje($mensajeId)
    {
        $em = $this->doctrine->getManager();
        $dql = "SELECT a.id, a.archivoNombre
                FROM vocationetBundle:Archivos a
                JOIN vocationetBundle:MensajesArchivos ma WITH a.id = ma.archivo
                WHERE ma.mensaje = :mid";
        $query = $em->createQuery($dql);
        $query->setParameter('mid', $mensajeId);
        $adjuntos = $query->getResult();
        
        return $adjuntos;
    }
        
    /**
     * Funcion para cambiar el estado de un mensaje
     * 
     * @param integer $usuarioId id de usuario
     * @param integer $mensajeId id de mensaje
     * @param integer $estado nuevo estado del mensaje, 0: sin leer, 1: leido, 2:eliminado
     */
    public function updateEstadoMensaje($usuarioId, $mensajeId, $estado)
    {
        $dql = "UPDATE vocationetBundle:MensajesUsuarios mu
                SET mu.estado = :estado
                WHERE mu.usuario = :usuarioId
                AND mu.mensaje = :mensajeId";
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('estado', $estado);
        $query->setParameter('usuarioId', $usuarioId);
        $query->setParameter('mensajeId', $mensajeId);        
        $query->getResult();
    }
    
    /**
     * Funcion para obtener el total de mensajes sin leer
     * 
     * @param integer $usuarioId id de usuario
     * @return integer numero de mensajes sin leer
     */
    public function countMensajesSinLeer($usuarioId)
    {
        $count = 0;
        $dql = "SELECT COUNT(mu.id) c FROM vocationetBundle:MensajesUsuarios mu
                WHERE mu.usuario = :usuarioId
                AND mu.tipo = 2
                AND mu.estado = 0";
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $result = $query->getResult();
        
        if(isset($result[0]['c']))
            $count = $result[0]['c'];
        
        return $count;
    }
    
    /**
     * Funcion que obtiene un array de correos a partir de un array de ids de usuario
     * 
     * @param array $arr_id array de ids de usuario
     */
    public function getEmailAddress($arr_id)
    {
        $emails = array();
        
        if(count($arr_id) > 0)
        {
            $where = implode(" OR u.id = ", $arr_id);
            $dql = "SELECT u.usuarioEmail FROM vocationetBundle:Usuarios u
                    WHERE u.id = ".$where." ";
            $em = $this->doctrine->getManager();
            $query = $em->createQuery($dql);
            $return = $query->getResult();
            
            if(count($return) > 0)
            {
                foreach($return as $r)
                {
                    $emails[] = $r['usuarioEmail'];
                }
            }
        }
        return $emails;
    }
}
?>
