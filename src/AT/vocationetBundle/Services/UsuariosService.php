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
    
    /**
     * Funcion para obtener la entidad usuario con el id de facebook
     * 
     * @param integer $usuarioId id de usuario en facebook
     * @return \AT\vocationetBundle\Entity\Usuarios entidad usuario
     */
    public function getUsuarioFacebook($userId, $email)
    {
        $em = $this->doctrine->getManager();
//        $usuario = $em->getRepository('vocationetBundle:Usuarios')->findOneBy(array('usuarioFacebookid' =>$usuarioId, 'usuarioEmail' => $email));
        
        $usuario = false;
        
        $dql = "SELECT u FROM vocationetBundle:Usuarios u
                WHERE u.usuarioEmail = :email OR u.usuarioFacebookid = :userId";
        $query = $em->createQuery($dql);
        $query->setParameter('userId', $userId);
        $query->setParameter('email', $email);
        $query->setMaxResults(1);
        $result = $query->getResult();
        
        if(isset($result[0]))
        {
            $usuario = $result[0];
        }        
        
        return $usuario;
    }
        
    /**
     * Funcion para registrar un usuario de facebook
     * 
     * Los usuarios registrados a traves de facebook quedan con rol estudiante
     * 
     * @param array $userProfile profile retornado por facebook
     * @return \AT\vocationetBundle\Entity\Usuarios entidad de usuario
     */
    public function addUsuarioFacebook($userProfile)
    {
        $em = $this->doctrine->getManager();
        
        $rolEstudiante = $em->getRepository('vocationetBundle:Roles')->findOneById(1);
        
        $usuario = new \AT\vocationetBundle\Entity\Usuarios();
        
        $usuario->setUsuarioNombre($userProfile['first_name']);
        $usuario->setUsuarioApellido($userProfile['last_name']);
        $usuario->setUsuarioEmail($userProfile['email']);
        $usuario->setUsuarioFacebookid($userProfile['id']);
        $usuario->setUsuarioImagen('https://graph.facebook.com/'.$userProfile['id'].'/picture');
        $usuario->setCreated(new \DateTime());
        $usuario->setRol($rolEstudiante);
        $usuario->setUsuarioEstado(1);
        $usuario->setUsuarioHash(uniqid('u_f_', true));
        if(isset($userProfile['birthday'])) $usuario->setUsuarioFechaNacimiento(new \DateTime($userProfile['birthday']));
        if(isset($userProfile['gender'])) $usuario->setUsuarioGenero($userProfile['gender']);        
        
        $em->persist($usuario);
        $em->flush();
        
        return $usuario;
    }
    
    /**
     * Funcion para obtener la entidad de usuario con el email de linkedin
     * 
     * @param string $usuarioEmail email del usuario
     * @return \AT\vocationetBundle\Entity\Usuarios entidad usuario
     */
    public function getUsuarioLinkedin($usuarioEmail)
    {
        $em = $this->doctrine->getManager();
        
        $usuario = $em->getRepository('vocationetBundle:Usuarios')->findOneByUsuarioEmail($usuarioEmail);
        
        return $usuario;
    }
    
    /**
     * Funcion para registrar un usuario de linkedin
     * 
     * @param array $userProfile datos basicos del usuario linkedin
     * @return \AT\vocationetBundle\Entity\Usuarios entidad usuario
     */
    public function addUsuarioLinkedin($userProfile)
    {
        $em = $this->doctrine->getManager();
        
        $rolMentor = $em->getRepository('vocationetBundle:Roles')->findOneById(2);
        
        $usuario = new \AT\vocationetBundle\Entity\Usuarios();
        
        $usuario->setUsuarioNombre($userProfile['first-name']);
        $usuario->setUsuarioApellido($userProfile['last-name']);
        $usuario->setUsuarioEmail($userProfile['email-address']);
        if(!empty($userProfile['picture-url'])) $usuario->setUsuarioImagen($userProfile['picture-url']);
        $usuario->setCreated(new \DateTime());
        $usuario->setRol($rolMentor);
        $usuario->setUsuarioEstado(1);
        $usuario->setUsuarioHash(uniqid('u_l_', true));
        if(!empty($userProfile['date-of-birth'])) $usuario->setUsuarioFechaNacimiento(new \DateTime($userProfile['date-of-birth']));
        
        $em->persist($usuario);
        $em->flush();
        
        return $usuario;
    }
    
    /**
     * Funcion para registrar un usuario nativo
     * 
     * @param array $dataForm arreglo con los campos del formulario
     * @param string $password contraseña encriptada del usuario
     * @return \AT\vocationetBundle\Entity\Usuarios entidad de usuario
     */
    public function addUsuarioNativo($dataForm, $password)
    {
        $em = $this->doctrine->getManager();
        
        $rolEstudiante = $em->getRepository('vocationetBundle:Roles')->findOneById(1);
        
        $usuario = new \AT\vocationetBundle\Entity\Usuarios();
        
        $usuario->setUsuarioNombre($dataForm['first_name']);
        $usuario->setUsuarioApellido($dataForm['last_name']);
        $usuario->setUsuarioEmail($dataForm['email']);
        $usuario->setUsuarioPassword($password);
        $usuario->setCreated(new \DateTime());
        $usuario->setRol($rolEstudiante);
        $usuario->setUsuarioEstado(0);
        $usuario->setUsuarioHash(uniqid('u_n_', true));
        
        $em->persist($usuario);
        $em->flush();
        
        return $usuario;
    }
    
    /**
     * Funcion que verifica si existe un usuario
     * 
     * @param string $email email del usuario
     */
    public function existsUsuario($email)
    {
        $return = false;
        
        $dql = "SELECT COUNT(u.id) c FROM vocationetBundle:Usuarios u
                WHERE u.usuarioEmail = :email";
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('email', $email);
        $query->setMaxResults(1);
        $result = $query->getResult();
        
        if($result[0]['c']== 1)
        {
            $return = true;
        }
        
        return $return;
    }
}
?>
