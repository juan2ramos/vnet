<?php

namespace AT\vocationetBundle\Services;

use \Facebook;

/**
 * Servicio para conexion con facebook-php-sdk
 * 
 * @author Diego Malagón <diego@altactic.com>
 */
class FacebookService
{
    
    /**
     * Service container
     * @var Object 
     */
    protected $serv_cont;
        
    /**
     * Objeto Facebook
     * @var Object 
     */
    public $facebook;
    
    /**
     * app_id de aplicacion facebook
     * @var string 
     */
    protected $appId;
    
    /**
     * app_secret de aplicacion facebook
     * @var string 
     */
    protected $appSecret;
    
    /**
     * Ruta de controlador donde redirecciona facebook
     * @var string 
     */
    public $redirect_uri;
    
    
    /**
     * Constructor
     * 
     * inicializa el objeto facebook
     * 
     * @param string $fbAppId valor suministrado en parameters.yml    
     * @param string $fbSecret valor suministrado en parameters.yml
     * @param object $serv_cont service_container
     */
    function __construct($appId, $appSecret, $serv_cont) 
    {
        $this->serv_cont = $serv_cont;
        
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        
        $this->facebook = new Facebook(array(
            'appId'     =>  $appId,
            'secret'    =>  $appSecret
        ));
        
        $this->redirect_uri =  $serv_cont->get('request')->getSchemeAndHttpHost().$serv_cont->get('router')->generate('login_facebook');
        
    }
    
    
}
?>
