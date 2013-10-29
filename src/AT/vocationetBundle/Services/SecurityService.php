<?php
namespace AT\vocationetBundle\Services;

/**
 * Servicio encargado de aspectos de seguridad, autenticacion y permisos
 * 
 * @author Diego Malagón <diego@altactic.com>
 */
class SecurityService
{
    var $doctrine;
    var $session;
        
    function __construct($doctrine, $session) 
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
    }
        
    /**
     * Funcion para la encriptacion de contraseñas
     * 
     * @author Diego Malagón <diego@altactic.com>
     * @param string $txt cadena a encriptar
     * @return string cadena encriptada
     */
    public function encriptar($txt)
    {
        $key1 = "bd70eb8d55273da2596b643158cb8a14bd18e5d7";
        $key2 = "d2963414730413d0c17c8fa8ab0085e0";
        $enc = sha1(md5($txt.$key1).$key2);
        return $enc;
    }
    
    /**
     * Funcion para imprimir la estructura de una variable
     * 
     * @author Diego Malagón <diego@altactic.com>
     * @param type $var variable a depurar
     */
    public function debug($var)
    {
        if(is_object($var) || is_array($var))
        {
            echo "<pre>";
            print_r($var);
            echo "</pre>";
        }
        else
        {
            var_dump($var);
            echo "<br/>";
        }
    }
    
    /**
     * Funcion para la validacion del nivel de seguridad de contraseñas
     * 
     * @author Diego Malagón <diego@altactic.com>
     * @param type $pass
     * @return boolean
     */
    public function validarPassword($pass)
    {
        $return = false;
        $nivel = 0;
                
        if(strlen($pass) >= 6)
        {
            $nivel += 1;
            if(preg_match('`[a-z]`',$pass) && preg_match('`[A-Z]`',$pass))
            {
                $nivel += 1;
            }
            if(preg_match('`[0-9]`',$pass) && (preg_match('`[a-z]`',$pass) || preg_match('`[A-Z]`',$pass)))
            {
                $nivel += 1;
            }
            if(preg_match('`[\`,´,~,!,@,#,$,&,%, ,^,(,),+,=,{,},[,\],|,-,_,/,*,$,=,°,¡,?,¿,\,,\.,;,:,\".\',<,>]`',$pass) && (preg_match('`[a-z]`',$pass) || preg_match('`[A-Z]`',$pass)))
            {
                $nivel += 1;
            }
            if(strlen($pass) >= 8)
            {
                $nivel += 1;
            }
            if(strlen($pass) >= 10)
            {
                $nivel += 1;
            }
            
            if($nivel >= 4)
            {
                $return = true;
            }
        }
        return $return;
    }
    
    /**
     * Funcion que genera una contraseña aleatoria
     * 
     * @author Diego Malagón <diego@altactic.com>
     * @return string Contraseña generada sin encriptar
     */
    public function generarPassword()
    {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890@ñÑ";
        $pass = "";
        for($i=0;$i<10;$i++) 
        {
            $pass .= substr($str,rand(0,62),1);
        }
        return $pass;
    }
    
    /**
     * Funcion para iniciar sesion
     * 
     * @author Diego Malagón <diego@altactic.com>
     * @param Object $usuario entidad usuario
     * @param array $params parametros adicionales para guardar en sesion
     */
    public function login($usuario, $params = array())
    {
        $sess_user = array(
            'id' => $usuario->getId(),
            'usuarioNombre' => $usuario->getUsuarioNombre(),
            'usuarioApellido' => $usuario->getUsuarioApellido(),
            'usuarioEmail' => $usuario->getUsuarioEmail(),
            'usuarioFacebookid' => $usuario->getUsuarioFacebookid(),
            'usuarioImagen' => $usuario->getUsuarioImagen(),            
        );
        
        $sess_permissions = $this->getPermisosUsuario($usuario->getId());
        
        $this->session->set('sess_user',$sess_user);
        $this->session->set('sess_permissions',$sess_permissions);     
        
        if(count($params)>0)
        {
            $this->session->set('sess_parameters',$params);
        }
    }
    
    /**
     * Funcion para obtener los permisos de un usuario
     * 
     * @author Diego Malagón <diego@altactic.com>
     * @param integer $usuarioId id de usuario
     * @return array arreglo de permisos separados por identificadores y rutas
     */
    public function getPermisosUsuario($usuarioId)
    {
        $dql = "SELECT p.identificador, p.permisoRoutes FROM vocationetBundle:Permisos p
                JOIN vocationetBundle:Roles r WITH p.rol = r.id
                JOIN vocationetBundle:Usuarios u WITH u.rol = r.id
                WHERE u.id = :usuarioId
                ";
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $result = $query->getResult();
        
        $permissions = array();
        $routes = array();
        
        foreach($result as $r)
        {
            $permissions[] = $r['identificador'];
            
            $explode = explode(',', $r['permisoRoutes']);
            
            foreach($explode as $route)
            {
                if(!in_array($route, $routes))
                {
                    $routes[] = $route;
                }
            }            
        }
        
        return array(
            'permissions'   =>  $permissions,
            'routes'        =>  $routes
        );
    }
       
    /**
     * Funcion para eliminar la session
     * 
     * @author Diego Malagón <diego@altactic.com>
     */
    public function logout()
    {
        $this->session->set('sess_user',null);
        $this->session->set('sess_permissions',null);
        $this->session->set('sess_parameters',null);
    }
    
    /**
     * Funcion que verifica si el usuario esta autenticado
     * 
     * @author Diego Malagón <diego@altactic.com>
     * @return boolean
     */
    public function authentication()
    {
        $return = false;
        $sess_user = $this->session->get('sess_user');
        $sess_permissions = $this->session->get('sess_permissions');
        
        if(isset($sess_user['id']) && isset($sess_permissions['permissions']) && isset($sess_permissions['routes']))
        {
            $return = true;
        }
        
        return  $return;
    }
    
    /**
     * Funcion que verifica si el usuario tiene permiso a una ruta o modulo
     * 
     * @author Diego Malagón <diego@altactic.com>
     * @param string $route ruta de action o identificador de permiso
     * @param string $check routes|permissions indica en donde buscar el permiso
     * @return boolean true si tiene el permiso false si no
     */
    public function authorization($route, $check="routes")
    {
        $return = false;
        
        $permisos = $this->session->get('sess_permissions');        
        
        if(in_array($route, $permisos[$check]))
        {
            $return = true;
        }
        
        return $return;
    }
    
    /**
     * Funcion para obtener un valor de sesion
     * 
     * @param string $key
     */
    public function getSessionValue($key)
    {
        $return = false;
        $sess_user = $this->session->get('sess_user');
        $sess_parameters = $this->session->get('sess_parameters');
        
        if(in_array($key, $sess_user))
        {
            $return = $sess_user[$key];
        }
        elseif(in_array($key, $sess_parameters))
        {
            $return = $sess_parameters[$key];
        }
        
        return $return;
    }
}