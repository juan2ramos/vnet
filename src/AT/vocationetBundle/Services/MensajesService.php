<?php
namespace AT\vocationetBundle\Services;

/**
 * Servicio para el control de mensajes en la aplicacion
 * 
 * @author Diego Malagón <diego@altactic.com>
 */
class SecurityService
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
}
?>
