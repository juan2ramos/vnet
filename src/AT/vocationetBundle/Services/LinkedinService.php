<?php

namespace AT\vocationetBundle\Services;

/**
 * Servicio para conexion con linkedin
 * 
 * @author Diego Malagón <diego@altactic.com>
 */
class LinkedinService
{
    /**
     * Service container
     * @var Object 
     */
    protected $serv_cont;
    
    /**
     * key de la aplicacion likedin
     * @var string 
     */
    protected $apiKey;
    
    /**
     * secret key de la aplicacion linkedin
     * @var type 
     */
    protected $secretKey;
    
    /**
     * Ruta de controlador donde redirecciona linkedin
     * @var string 
     */
    public $redirect_uri;

    /**
     * token para proteccion de csrf
     * @var string 
     */
    protected $token;
    
    /**
     * Almacena el ultimo error generado
     * @var Object|string 
     */
    public $last_error;
    
    /**
     * Constructor
     * 
     * @param string $apiKey valor suministrado en parameters.yml    
     * @param string $secret valor suministrado en parameters.yml
     * @param object $serv_cont service_container
     */
    function __construct($apiKey, $secretKey, $serv_cont) 
    {
        $this->serv_cont = $serv_cont;
        
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey; 
        
        $this->redirect_uri =  $serv_cont->get('request')->getSchemeAndHttpHost().$serv_cont->get('router')->generate('login_linkedin');
        
        $this->token = '8652f3a202e57e0d9cf2d112a97761133ea75896';
    }
    
    /**
     * Funcion para generar la url de autenticacion con linkedin
     * 
     * @return string url
     */
    public function getLoginUrl()
    {
        $endpoint = "https://www.linkedin.com/uas/oauth2/authorization?";
        
        $options = array(
            'response_type' =>  'code',
            'client_id'     =>  $this->apiKey,
            'scope'         =>  'r_fullprofile r_emailaddress r_contactinfo r_network',
            'state'         =>  $this->generateState(),
            'redirect_uri'  =>  $this->redirect_uri
        );
        
        
        $url = $endpoint.http_build_query($options);
        
        return $url;
    }
    
    /**
     * Funcion para generar un token adicional a las peticiones para linkedin
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
     * Funcion para validar state retornado por peticion a linkedin
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
     * Funcion que verifica la autenticacion con linkedin
     * 
     * @param array $response arreglo con los valores retornados por linkedin en la url
     */
    public function handleLoginResponse($response)
    {
        $return = false;
        if($response['error'] !== 'access_denied')
        {
            if($this->validateState($response['state']))
            {
                // Enviar peticion para validar token
                $access_token_response = $this->sendRequest('https://www.linkedin.com/uas/oauth2/accessToken', array(
                    'grant_type'    =>  'authorization_code',
                    'code'          =>  $response['code'],
                    'redirect_uri'  =>  $this->redirect_uri,
                    'client_id'     =>  $this->apiKey,
                    'client_secret' =>  $this->secretKey
                ));
                
                $access_token_response = json_decode($access_token_response);
                
                if($access_token_response)
                {
                    // Llamadas a la api para obtener informacion basica del usuario                    
                    $userProfile = $this->getUserInfo($access_token_response->access_token);
                    
                    $return = $userProfile + array('access_token' => $access_token_response->access_token);
                }
            }
        }
        else
        {
            $this->last_error = $response;
        }
        
        return $return;
    }
    
    /**
     * Funcion para enviar peticiones a linkedin y retornar la repuesta
     * 
     * @param string $endpoint url de la peticion
     * @param array $params parametros get para la peticion
     * @return type repuesta de linkedin
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
     * Funcion para obtener informacion basica de perfil del usuario linkedin
     * 
     * @param string $access_token access_token devuelto por linkedin
     * @return array arreglo con datos del usuario
     */
    public function getUserInfo($access_token)
    {
        $userProfile = array();
        
        $arr_access_token = array('oauth2_access_token'   =>  $access_token);
                
        // las llamadas a la api de linkedin retornan un xml
        $userProfile['first-name'] = (string)simplexml_load_string($this->sendRequest('https://api.linkedin.com/v1/people/~/first-name', $arr_access_token));
        $userProfile['last-name'] = (string)simplexml_load_string($this->sendRequest('https://api.linkedin.com/v1/people/~/last-name', $arr_access_token));
        $userProfile['picture-url'] = (string)simplexml_load_string($this->sendRequest('https://api.linkedin.com/v1/people/~/picture-url', $arr_access_token));
        $userProfile['email-address'] = (string)simplexml_load_string($this->sendRequest('https://api.linkedin.com/v1/people/~/email-address', $arr_access_token));
        $userProfile['date-of-birth'] = (string)simplexml_load_string($this->sendRequest('https://api.linkedin.com/v1/people/~/date-of-birth', $arr_access_token));
        
        return $userProfile;
    }
}
?>
