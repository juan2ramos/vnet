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
//        $facebook = new Facebook(array(
//            'appId'     =>  '357031241098327',
//            'secret'    =>  '50ccb410c4705b04f3854b88f1535f38'
//        ));
//        
//        $user = $facebook->getUser();
        
        
        $facebook = $this->get('facebook');       
        
        $user = $facebook->facebook->getUser();
        
        
        
        $security->debug($user);
        
        
        return array(
            
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
        $facebook = $this->get('facebook');
        $security = $this->get('security');
        
        $request = $this->getRequest();        
        
        
        $response = array(
            'error' => $request->get('error'),
            'error_reason' => $request->get('error_reason'), 
            'error_description' => $request->get('error_description'), 
            'code' => $request->get('code'),
            'state' => $request->get('state') 
        );
        
        
//        $login = $facebook->handleLoginResponse($response);
        
        $security->debug($response);
        
        
        
        
        return new Response();
    }
}
