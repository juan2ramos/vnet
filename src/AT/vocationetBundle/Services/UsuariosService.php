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
     * Funcion que verifica si existe un usuario registrado en la db usando facebook
     * 
     * @param integer $facebookUserId id de usuario en facebook
     */
    public function existsFacebookId($facebookUserId)
    {
        $return = false;
        $dql = "SELECT COUNT(u.id) c FROM vocationetBundle:Usuarios u
                WHERE u.usuarioFacebookid = :fbId";
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('fbId', $facebookUserId);
        $query->setMaxResults(1);
        $result = $query->getResult();
        
        if(isset($result[0]['c']) && $result[0]['c'] >= 1 )
        {
            $return = true;
        }
        
        return $return;
    }
    
    /**
     * Funcion para registrar un usuario de facebook
     * 
     * Los usuarios registrados a traves de facebook quedan con rol estudiante
     * 
     * @param array $userProfile profile retornado por facebook
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
        $usuario->setCreated(new \DateTime());
        $usuario->setRol($rolEstudiante);
        $usuario->setUsuarioEstado(1);
        $usuario->setUsuarioTipo(1);
        
        $em->persist($usuario);
        $em->flush();
        
        $perfil = new \AT\vocationetBundle\Entity\Perfiles();
        
        $perfil->setUsuario($usuario);
        $perfil->setFechanacimiento(new \DateTime($userProfile['birthday']));
        $perfil->setGenero($userProfile['gender']);
        
        $em->persist($perfil);
        $em->flush();
        
        return $usuario->getId();
    }
    
}
?>
