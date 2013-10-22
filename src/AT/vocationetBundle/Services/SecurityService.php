<?php
namespace AT\vocationetBundle\Services;


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
}