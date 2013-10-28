<?php
namespace AT\vocationetBundle\Services;

/**
 * Servicio para el control de usuarios
 * 
 * @author Diego Malagón <diego@altactic.com>
 */
class UsuariosService
{
    var $doctrine;
    var $session;
        
    function __construct($doctrine, $session) 
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
    }
            
    /**
     * Funcion para obtener la entidad usuario con el id de facebook
     * 
     * @param integer $usuarioId id de usuario en facebook
     * @return Object entidad usuario
     */
    public function getUsuarioFacebook($usuarioId)
    {
        $em = $this->doctrine->getManager();
        
        $usuario = $em->getRepository('vocationetBundle:Usuarios')->findOneByUsuarioFacebookid($usuarioId);
        
        return $usuario;
    }
        
    /**
     * Funcion para registrar un usuario de facebook
     * 
     * Los usuarios registrados a traves de facebook quedan con rol estudiante
     * 
     * @param array $userProfile profile retornado por facebook
     * @return Object entidad de usuario
     */
    public function addUsuarioFacebook($userProfile)
    {
        $em = $this->doctrine->getManager();
        
        $rolEstudiante = $em->getRepository('vocationetBundle:Roles')->findOneById(1);
        
        $usuario = new \AT\vocationetBundle\Entity\Usuarios();
        
        $usuario->setUsuarioNombre($userProfile['first_name']);
        $usuario->setUsuarioApellido($userProfile['last_name']);
        $usuario->setUsuarioEmail($userProfile['username']);
        $usuario->setUsuarioFacebookid($userProfile['id']);
        $usuario->setUsuarioImagen('https://graph.facebook.com/'.$userProfile['id'].'/picture');
        $usuario->setCreated(new \DateTime());
        $usuario->setRol($rolEstudiante);
        $usuario->setUsuarioEstado(1);
        if(isset($userProfile['birthday'])) $usuario->setUsuarioFechaNacimiento(new \DateTime($userProfile['birthday']));
        if(isset($userProfile['gender'])) $usuario->setUsuarioGenero($userProfile['gender']);        
        
        $em->persist($usuario);
        $em->flush();
        
        return $usuario;
    }
    
    /**
     * Funcion para obtener un usuario registrado nativamente
     * 
     * @param string $user correo del usuario
     * @param string $pass contraseña del usuario encriptada
     * @return Object|boolean entidad usuario o false si no existe
     */
    public function checkUsuarioNativo($user, $pass)
    {
        $dql = "SELECT u FROM vocationetBundle:Usuarios u
                WHERE 
                    u.usuarioEmail = :email 
                    AND u.usuarioPassword = :pass
                    AND u.usuarioEstado = 1";
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('email', $user);
        $query->setParameter('pass', $pass);
        $query->setMaxResults(1);
        $result = $query->getResult();
        
        if(isset($result[0]))
        {
            return $result[0];
        }
        else return false;
    }
}
?>
