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
     * app_id de facebook
     * @var type 
     */
    public $appId;
    
    /**
     * token para proteccion de csrf
     * @var string 
     */
    public $token;
    
    /**
     * Constructor
     * 
     * inicializa el objeto facebook
     * 
     * @param string $fbAppId valor suministrado en parameters.yml    
     * @param string $fbSecret valor suministrado en parameters.yml
     */
    function __construct($appId, $appSecret, $serv_cont) 
    {
        $this->serv_cont = $serv_cont;
        
        $this->appId = $appId;
        
        $this->facebook = new Facebook(array(
            'appId'     =>  $appId,
            'secret'    =>  $appSecret,
            'fileUpload'=>  false
        ));
        
        $this->token = '60d20bcbfad939a9126a34c124259889ec9ed22e';
    }
    
    
}
?>
