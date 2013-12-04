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
                JOIN vocationetBundle:PermisosRoles pr WITH p.id = pr.permiso 
                JOIN vocationetBundle:Roles r WITH pr.rol = r.id
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
        
        if (isset($sess_user[$key]))
        {
            $return = $sess_user[$key];
        }
        elseif (isset($sess_parameters[$key]))
        {
            $return = $sess_parameters[$key];
        }
        return $return;
    }

    
    /**
     * Funcion para validar base64
     * 
     * @param string $code64 cadena codificada en base64
     * @return boolean
     */
    public function validateBase64($code64)
    {
        $validate = false;
        $dec = base64_decode($code64, true);
        
        $enc = base64_encode($dec);
        
        $val_deco = base64_decode($enc, true);
        
        if($val_deco !== false)
        {
            $validate = true;
        }
        return $validate;
    }

    /**
     * Funcion para obtener parametros de vocationet
     * @param type $parameter
     * @return string
     */
    public static function getParameter($parameter = false)
    {
        $parameters = array(
            'enlace_test_vocacional' => 'http://www.uanl.mx/utilerias/test/',
            'enlace_pdf_test_vocacional_info' => 'uploads/vocationet/testvocacional.pdf',
            'enlace_vocationet_info' => 'http://www.vocationet.com',
            'ruta_files_mercado_laboral' => 'uploads/vocationet/ml/',
            'dias_habiles_informe_mercado_laboral' => 5,
            'diagnostico_rango_puntaje_1' => array('min'=>0, 'max'=>50),
            'diagnostico_rango_puntaje_2' => array('min'=>51, 'max'=>90),
            'diagnostico_rango_puntaje_3' => array('min'=>91, 'max'=>111),
        );
        
        if($parameter)
        {
            return $parameters[$parameter];
        }
        else
        {
            return $parameters;
        }        
    }
}
