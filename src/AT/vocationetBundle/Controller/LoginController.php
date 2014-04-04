<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
	
	 /**
     * Accion para formulario y envio de mail de olvide mi contraseña
     * 
	 * @author Camilo Quijano <camilo@altactic.com>
	 * @version 1
     * @Route("/forgetpass", name="forget_pass")
     * @Template("vocationetBundle:Login:forgetPass.html.twig")
	 * @Method({"GET", "POST"})
	 * @param Request $request Request form
     * @return Response
     */
    public function forgetPassAction(Request $request)
    {
		$security = $this->get('security');
		
		$forgetPassFormData = array('username' => '');
        $forgetPassForm = $this->createFormBuilder($forgetPassFormData)
           ->add('username', 'email', array('required' => true))
           ->getForm();
		
		if($request->getMethod() == 'POST') 
        {
            $forgetPassForm->bind($request);
            if ($forgetPassForm->isValid()) 
			{
				$data = $forgetPassForm->getData();
				$email = $data['username'];
				if($this->get('usuarios')->existsUsuario($email)) {
					$token = $security->encriptar("D".date('Ymd')."TOKEN".$email);
					$title = $this->get('translator')->trans("recuperar.contrasena", array(), 'mail');
					
					// Estructura del envio del email.
					$link = $this->get('request')->getSchemeAndHttpHost().$this->get('router')->generate('change_pass', array('email'=>$email, 'token'=>$token)); 
					$dataRender = array(
						'title' => $title, 
						'body' => $this->get('translator')->trans("mensaje.de.recuperacion.contrasena", array(), 'mail'), 
						'link' => $link, 
						'link_text' => $title,
					);
				
					// Envio de mail, y notificacion por pantalla informado del envio
					$this->get('mail')->sendMail($email, $title, $dataRender);
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $title, "text" => $this->get('translator')->trans("informacion.envio.mail.enlace.recuperacion.contrasena")));
					return $this->redirect($this->generateUrl('login'));
				} else {
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
				}
			}
		}
		return array(
            'form' => $forgetPassForm->createView(),
        );
	}
	
	/**
     * Formulario de cambio de contraseña y validacion de token
     * 
	 * @author Camilo Quijano <camilo@altactic.com>
	 * @version 1
     * @Route("/changepass/{email}/{token}", name="change_pass")
     * @Template("vocationetBundle:Login:changePass.html.twig")
	 * @Method({"GET", "POST"})
	 * @param Request $request Request form
	 * @param String $email correo electronico del usuario a cambiar la contraseña
	 * @param String $token token generado de validación de usuario solicitando cambio de contraseña
     * @return Response
     */
    public function changePassAction(Request $request, $email, $token)
    {
		$security = $this->get('security');
		$token_val = $security->encriptar("D".date('Ymd')."TOKEN".$email);
		
		if ($token_val != $token) {
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("recuperar.contrasena", array(), 'mail'), "text" => $this->get('translator')->trans("token.invalido")));
			return $this->redirect($this->generateUrl('login'));
		}
		
		$data = array('pass' => null, 'conf_pass' => null);
		$changePassForm = $this->createFormBuilder($data)
		   ->add('pass', 'password', array('required' => true))
		   ->add('conf_pass', 'password', array('required' => true))
		   ->getForm();
		   
		if($request->getMethod() == 'POST') {
			$changePassForm->bind($request);
			if ($changePassForm->isValid()) {
				$dataForm = $changePassForm->getData();
				if($security->validarPassword($dataForm['pass'])) {
					if($dataForm['pass'] == $dataForm['conf_pass']) {
						$em = $this->getDoctrine()->getManager();
						$usuario = $em->getRepository('vocationetBundle:Usuarios')->findOneByUsuarioEmail($email);
						if ($usuario) {
							//Cambio de contraseña
							$usuario->setUsuarioPassword($security->encriptar($dataForm['pass']));
							$usuario->setModified(new \DateTime());
							$em->persist($usuario);
							$em->flush();
							
							$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("cambio.contrasena"), "text" => $this->get('translator')->trans("cambio.contrasena.correcto")));
							return $this->redirect($this->generateUrl('login'));
						}
					}
				}
			}
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
		}
		
		return array(
			'form' 	=> $changePassForm->createView(),
			'email' => $email, 
			'token' => $token
		);
	}
}
