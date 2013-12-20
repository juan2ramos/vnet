<?php

namespace AT\vocationetBundle\Services;

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
    
    /**
     * Guarda el mensaje u objeto de error
     * @var type 
     */
    public $last_error;
    
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
        
        $this->token = '60d20bcbfad939a9126a34c124259889ec9ed22e';
        $this->redirect_uri =  $serv_cont->get('request')->getSchemeAndHttpHost().$serv_cont->get('router')->generate('login_facebook');
        
        $this->last_error = false;
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
     * Funcion para generar la url de autenticacion con facebook
     * 
     * @return string url
     */
    public function getLoginUrl()
    {
        $endpoint = "https://www.facebook.com/dialog/oauth?";       
        
        $options = array(
            'client_id'     => $this->appId,
            'redirect_uri'	=> $this->redirect_uri,
            'scope'			=> 'email,user_education_history,user_birthday,user_location',
//            'scope'			=> 'email,user_education_history,read_stream,publish_stream,user_birthday,user_location',
            'state'         => $this->generateState()
		);
        
        $url = $endpoint.http_build_query($options);
        
        return $url;
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
     */
    public function handleLoginResponse($response)
    {
        $resturn = false;
        
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
                        'input_token' => $access_token,
                        'access_token' => $this->getAppToken()
                    ));
                    
                    if($json_response)
                    {
                        // Obtener user_id
                        $conn_array = json_decode($json_response);
                        
                        $user_id = $conn_array->data->user_id;
                        
                        $userProfile = $this->getUserInfo($user_id, $access_token);
                        
                        $resturn = $userProfile + array('access_token' => $access_token);
                    }
                }
            }
        }
        
        return $resturn;
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
        if(count($params)>0)
        {
            $request = http_build_query($params);
            $url = $endpoint.'?'.$request;            
        }
        else
        {
            $url = $endpoint;
        }
        
        $curlOptions = array (
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_POST => 0,
            CURLOPT_HTTPGET => 1,
            CURLOPT_RETURNTRANSFER => 1
        );
        
        $ch = curl_init();
        curl_setopt_array($ch,$curlOptions);
        
        $response = curl_exec($ch);
        
        // Validar respuesta
        $json_response = json_decode($response);
        if($json_response !== null && isset($json_response->error))
        {
            $this->last_error =  $json_response->error;
            $response = false;
        }
        
        return $response;
    }
       
    /**
     * Funcion para obtener el app token
     * 
     * @return string token
     */
    public function getAppToken()
    {
        // 357031241098327|Kb3YZ-kc48bQhNBRTFoAvVqAXPA
        $response = $this->sendRequest('https://graph.facebook.com/oauth/access_token', array(
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'grant_type' => 'client_credentials'
        ));
        $returned = array();
        parse_str($response, $returned);
        return $returned['access_token'];
    }
      
    /**
     * Funcion para obtener informacion basica de perfil del usuario facebook
     * 
     * @param string $access_token access_token devuelto por linkedin
     * @return array arreglo con datos del usuario
     */
    public function getUserInfo($user_id, $access_token)
    {
        $user = $this->sendRequest('https://graph.facebook.com/'.$user_id, array(
            'fields'    =>  'email,first_name,birthday,gender,last_name',
            'access_token' => $access_token
        ));
        
        $arr_user = json_decode($user, true);
        
        return $arr_user;
    }
}
?>
