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
     * token para proteccion de csrf
     * @var string 
     */
    protected $token;
    
    /**
     * Ruta de controlador donde redirecciona facebook
     * @var string 
     */
    public $redirect_uri;
    
    public $message;
    
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
        $this->appSecret = $appSecret;
        
        $this->facebook = new Facebook(array(
            'appId'     =>  $appId,
            'secret'    =>  $appSecret,
            'fileUpload'=>  false
        ));
        
        $this->token = '60d20bcbfad939a9126a34c124259889ec9ed22e';
        $this->redirect_uri =  $serv_cont->get('request')->getSchemeAndHttpHost().$serv_cont->get('router')->generate('login_facebook');
    }
    
    /**
     * Funcion para obtener el app_id de la aplicacion facebook
     * @return string app_id
     */
    public function getId()
    {
        return $this->appId;
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
     * Funcion que retorna la url de facebook para autenticacion
     * 
     * @return string
     */
    public function getEndpointLogin()
    {
        $state = $this->generateState();
        return 'https://www.facebook.com/dialog/oauth?client_id='.$this->appId.'&redirect_uri='.$this->redirect_uri.'&state='.$state.'&scope=publish_actions';
    }
    
    /**
     * Funcion para enviar peticiones a facebook y retornar la repuesta
     * 
     * @param string $endpoint url de la peticion
     * @param array $params parametros get para la peticion
     * @return type repuesta de facebook
     */
    public function sendRequest($endpoint, $params = array())
    {
        $request = http_build_query($params);
        
        $curlOptions = array (
            CURLOPT_URL => $endpoint,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => $request
        );
        
        $ch = curl_init();
        curl_setopt_array($ch,$curlOptions);
        
        $response = curl_exec($ch);
        
        // Validar respuesta
        $json_response = json_decode($response);
        if($json_response !== null && isset($json_response->error))
        {
            $this->message =  $json_response->error->message;
//            $response = false;
        }
        
        return $response;
    }
    
    public function handleLoginResponse($response)
    {
        $validate_auth = false;
        
        if($response['error'] !== 'access_denied')
        {
            if($this->validateState($response['state']))
            {
                // Obtener token
                $token_response = $this->sendRequest('https://graph.facebook.com/oauth/access_token', array(
                    'client_id' => $this->appId,
                    'redirect_uri' => $this->redirect_uri,
                    'client_secret' => $this->appSecret,
                    'code' => $response['code']
                ));
                
                if($token_response)
                {
                    $returned = array();
                    parse_str($token_response, $returned);
                    $access_token = $returned['access_token'];
                    
                    
                    // Validar token
                    $json_response = $this->sendRequest('https://graph.facebook.com/debug_token', array(
                        'input_token' => $response['state'],
                        'access_token' => $access_token, 
                    ));
                    
                    if($json_response)
                    {
                        // Obtener user_id
                        
                    }
                    
                    $this->serv_cont->get('security')->debug($json_response);
//                    $this->serv_cont->get('security')->debug($response_params);
                    
                    
                    
                    
                    
                    $validate_auth = true;
                }
                
                
                
            }
        }
        
        echo "<div>".$this->message."</div>";
        
        return $validate_auth;
    }
    
    
            
    
    
    /**
     * Funcion para obtener el app token
     * 
     * @return string token
     */
    public function getAppToken()
    {
        $response = $this->sendRequest('https://graph.facebook.com/oauth/access_token', array(
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'grant_type' => 'client_credentials'
        ));
        $returned = array();
        parse_str($response, $returned);
        return $returned['access_token'];
    }
            
}
?>
