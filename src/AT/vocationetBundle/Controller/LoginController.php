<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use \Facebook;

/**
 * controlador para autenticacion y registro de usuarios
 *
 * @Route("/")
 * @author Diego Malagón <diego@altactic.com>
 */
class LoginController extends Controller
{
    /**
     * Accion para la autenticacion de usuarios
     * 
     * @Route("/", name="login")
     * @Template("vocationetBundle:Login:login.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        $facebook = $this->get('facebook');       
        
        $login_facebook_url = $facebook->getLoginUrl();
        
        return array(
            'login_facebook_url' => $login_facebook_url,
        );
    }    
    
    /**
     * Accion para recibir la respuesta de autenticacion de facebook
     * 
     * @Route("/facebook", name="login_facebook")
     * @return Response 
     */
    public function facebookAuthAction()
    {
        $security = $this->get('security');
        $facebook = $this->get('facebook');
        
        $request = $this->getRequest(); 
        
        $response = array(
            'error' => $request->get('error'),
            'error_reason' => $request->get('error_reason'), 
            'error_description' => $request->get('error_description'), 
            'code' => $request->get('code'),
            'state' => $request->get('state') 
        );
                
        $userId = $facebook->facebook->getUser(); 
        $userProfile = $facebook->handleLoginResponse($response, $userId);
                
        if($userProfile)
        {
            // logeado correctamente -> iniciar sesion en la aplicacion
            $user_serv = $this->get('usuarios');
            
            // Verificar si el usuario ya esta registrado
            $registrado = $user_serv->existsFacebookId($userId);
            
            if($registrado)
            {
                // Iniciar sesion
            }
            else
            {
                // Registrar
                $usuarioId = $user_serv->addUsuarioFacebook($userProfile);
                
                // Iniciar sesion
            }
            $security->debug($userProfile);
        }
        else
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("autenticacion.fallida"), "text" => $this->get('translator')->trans("intento.autenticacion.fallida")));
            return $this->redirect($this->generateUrl('login'));
        }        
        
        return new Response();
    }
    
    
}
