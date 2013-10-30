<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     * @author Diego Malagón <diego@altactic.com>
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $security = $this->get('security');     
        if($security->authentication()){ return $this->redirect($this->generateUrl('homepage'));}          
        
        $loginFormData = array('cta_username' => '', 'cta_passwords' => '');
        $loginForm = $this->createFormBuilder($loginFormData)
           ->add('username', 'email', array('required' => true))
           ->add('password', 'password', array('required' => true))
           ->getForm();
        
        if($request->getMethod() == 'POST') 
        {
            $loginForm->bind($request);
            if ($loginForm->isValid())
            {
                $data = $loginForm->getData();
                
                $password = $security->encriptar($data['password']);
                
                $user_serv = $this->get('usuarios');
                
                // Verificar el usuario registrado nativamente
                $usuario = $user_serv->checkUsuarioNativo($data['username'], $password);
                
                if($usuario)
                {
                    // Iniciar sesion y redireccinar a home
                    $security->login($usuario);
                    return $this->redirect($this->generateUrl('homepage'));
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("autenticacion.fallida"), "text" => $this->get('translator')->trans("verifique.datos.ingresados")));
                }
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("autenticacion.fallida"), "text" => $this->get('translator')->trans("verifique.datos.ingresados")));
            }
        }
        
        return array(
            'form' => $loginForm->createView(),
            'login_facebook_url' => $this->get('facebook')->getLoginUrl(),
            'login_linkedin_url' => $this->get('linkedin')->getLoginUrl()   
        );
    }    
    
    /**
     * Accion para recibir la respuesta de autenticacion de facebook
     * 
     * @Route("/facebook", name="login_facebook")
     * @author Diego Malagón <diego@altactic.com>
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
        
        
        $userProfile = $facebook->handleLoginResponse($response);
        
        if($userProfile)
        {
            $user_serv = $this->get('usuarios');
            
            // Verificar si el usuario ya esta registrado
            $usuario = $user_serv->getUsuarioFacebook($userProfile['id'], $userProfile['email']);
            
            if(!$usuario)
            {
                // Registrar
                $usuario = $user_serv->addUsuarioFacebook($userProfile);
            }
            
            $security->login($usuario, array('access_token' => $userProfile['access_token']));
            return $this->redirect($this->generateUrl('homepage'));
            
        }
        else
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("autenticacion.fallida"), "text" => $this->get('translator')->trans("intento.autenticacion.fallida")));
            return $this->redirect($this->generateUrl('login'));
        }        
        
        return new Response();
    }
        
    /**
     * Accion para recibir la respuesta de autenticacion de linkedin
     * 
     * @Route("/linkedin", name="login_linkedin")
     * @author Diego Malagón <diego@altactic.com>
     * @return Response 
     */
    public function linkedinAuthAction()
    {
        $security = $this->get('security');
        $linkedin = $this->get('linkedin');
        
        $request = $this->getRequest(); 
        
        $response = array(
            'error' => $request->get('error'),
            'error_reason' => $request->get('error_reason'), 
            'error_description' => $request->get('error_description'), 
            'code' => $request->get('code'),
            'state' => $request->get('state') 
        );
        
        $userProfile = $linkedin->handleLoginResponse($response);
        
        if($userProfile)
        {
            $user_serv = $this->get('usuarios');
            
            // Verificar si el usuario ya esta registrado
            $usuario = $user_serv->getUsuarioLinkedin($userProfile['email-address']);
            
            if(!$usuario)
            {
                // Registrar
                $usuario = $user_serv->addUsuarioLinkedin($userProfile);
            }
            
            $security->login($usuario, array('access_token' => $userProfile['access_token']));
            return $this->redirect($this->generateUrl('homepage'));
        }
        else
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("autenticacion.fallida"), "text" => $this->get('translator')->trans("intento.autenticacion.fallida")));
            return $this->redirect($this->generateUrl('login'));
        }
        
        return new Response();
    }
        
    /**
     * Accion para cerrar sesion
     * 
     * @Route("/logout", name="logout")
     * @author Diego Malagón <diego@altactic.com>
     * @return Response
     */
    public function logoutAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        
        $security->logout();
        
        return $this->redirect($this->generateUrl('login'));
    }
}
