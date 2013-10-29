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
     * Almacena el ultimo error generado
     * @var Object|string 
     */
    public $last_error;
    
    /**
     * token para proteccion de csrf
     * @var string 
     */
    protected $token;
    
    /**
     * Constructor
     * 
     * inicializa el objeto facebook
     * 
     * @param string $appId valor suministrado en parameters.yml    
     * @param string $appSecret valor suministrado en parameters.yml
     * @param object $serv_cont service_container
     */
    function __construct($appId, $appSecret, $serv_cont) 
    {
        $this->serv_cont = $serv_cont;
        
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        
        $this->facebook = new Facebook(array(
            'appId'     =>  $appId,
            'secret'    =>  $appSecret,
            'cookie'    =>  false
        ));
        
        $this->redirect_uri =  $serv_cont->get('request')->getSchemeAndHttpHost().$serv_cont->get('router')->generate('login_facebook');
        
        $this->token = '60d20bcbfad939a9126a34c124259889ec9ed22e';
    }
    
    /**
     * Funcion para generar la url de autenticacion con facebook
     * 
     * @return string url
     */
    public function getLoginUrl()
    {
        $loginUrl = $this->facebook->getLoginUrl(array(
            'client_id'     => $this->appId,
            'redirect_uri'	=> $this->redirect_uri,
            'scope'			=> 'email,user_education_history,read_stream,publish_stream,user_birthday,user_location',
            'state'         => $this->generateState()
		));
        
        return $loginUrl;
    }
    
    /**
     * Funcion para generar un token adicional a las peticiones para facebook
     * @return string state
     */
    public function generateState()
    {
        $security = $this->serv_cont->get('security');
        $unique = uniqid('fb');        
        $state = $unique.'.'.$security->encriptar($unique.$this->token); 
        
        return $state;
    }
    
    /**
     * Funcion para validar state retornado por peticion a facebook
     * @return boolean
     */
    public function validateState($state)
    {
        $validate = false;
        if(!empty($state))
        {
            $security = $this->serv_cont->get('security');
            
            $explode = explode('.', $state);
            $unique = (isset($explode[0])) ? $explode[0] : false;
            $enc = (isset($explode[1])) ? $explode[1] : false;
            
            $val_enc = $security->encriptar($unique.$this->token);
            
            if($enc == $val_enc)
            {
                $validate = true;
            }            
        }
        
        return $validate;
    }
    
    /**
     * Funcion que verifica la autenticacion con facebook
     * 
     * @param array $response arreglo con los valores retornados por facebook en la url
     * @param integer $userId user id retornado por facebook->getUser()
     * @return array|boolean retorna array con perfil del usuario o false si la autenticacion fallo
     */
    public function handleLoginResponse($response, $userId)
    {
        $return = false;
        if($response['error'] !== 'access_denied')
        {
            if($this->validateState($response['state']))
            {
                if($userId)
                {
                    try
                    {
                        $userProfile = $this->facebook->api('/me');
                        $return = $userProfile;
                    }
                    catch(FacebookApiException $e)
                    {
                        $userId = NULL;
                        $this->last_error = $e;
                    }
                }
            }
        }
        else
        {
            $this->last_error = $response;
        }
        
        return $return;
    }
}
?>
