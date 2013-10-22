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
        $facebook_serv = $this->get('facebook');
        
        $security->debug($facebook_serv->login());
        
        
        echo $this->getRequest()->get('code');
        echo "<br/>";
        echo $this->getRequest()->get('state');
        echo "<br/>";
        
        return array(
            
        );
    }
    
    
    /**
     * Accion para redireccionar a la autenticacion de facebook
     * 
     * @Route("/facebook", name="login_facebook")
     * @return Response redireccion a facebook
     */
    public function callFacebookAuthAction()
    {
        $facebook = $this->get('facebook');
        $security = $this->get('security');
        $redirect_uri =  $this->getRequest()->getSchemeAndHttpHost().$this->generateUrl('login');
        
        $unique = uniqid('fb');
        
        $token = $unique.'.'.$security->encriptar($unique.$facebook->token);
        
        $endpoint = 'https://www.facebook.com/dialog/oauth?client_id='.$facebook->appId.'&redirect_uri='.$redirect_uri.'&state='.$token;
        
        
        $this->redirect($endpoint);
        echo $endpoint;
        
        return new Response();
    }
}
