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
     * @author Diego Malagón
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
    
    
    
}