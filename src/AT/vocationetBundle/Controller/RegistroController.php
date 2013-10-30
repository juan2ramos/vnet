<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para el registro nativo de la aplicacion
 *
 * @Route("/registro")
 * @author Diego Malagón <diego@altactic.com>
 */
class RegistroController extends Controller
{
    /**
     * Accion para el formulario de registro
     * 
     * @Route("/", name="registro")
     * @Template("vocationetBundle:Registro:index.html.twig")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $security = $this->get('security');     
        if($security->authentication()){ return $this->redirect($this->generateUrl('homepage'));}  
        
        $form = $this->createRegistroForm();
        
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $data = $form->getData();
                
                if($this->validateForm($data))
                {
                    $user_serv = $this->get('usuarios');                

                    if(!$user_serv->existsUsuario($data['email']))
                    {                
                        // Registrar usuario
                        $usuario = $user_serv->addUsuarioNativo($data, $security->encriptar($data['pass']));

                        //Enviar email de confirmacion
                        $this->enviarEmailConfirmacion($usuario);

                        $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "time" => 30000, "title" => $this->get('translator')->trans("registro.exitoso"), "text" => $this->get('translator')->trans("registro.exitoso.descripcion")));
                        return $this->redirect($this->generateUrl('login'));
                    }
                    else
                    {
                        $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("usuario.ya.existe"), "text" => $this->get('translator')->trans("ya.existe.usuario.correo")));
                    }
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
                }
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
            }
        }
        
        
        return array(
            'form' => $form->createView(),
            'login_facebook_url' => $this->get('facebook')->getLoginUrl(),
            'login_linkedin_url' => $this->get('linkedin')->getLoginUrl() 
        );
    }
    
    /**
     * Funcion para crear el formulario de registro
     * 
     * @return Object formulario de registro
     */
    private function createRegistroForm()
    {
        $data = array(
            'first_name' => null, 
            'last_name' => null,
            'email' => null,
            'pass' => null,
            'conf_pass' => null
        );
        $form = $this->createFormBuilder($data)
           ->add('first_name', 'text', array('required' => true, 'attr' => Array('pattern' => '^[a-zA-Z áéíóúÁÉÍÓÚñÑ]*$')))
           ->add('last_name', 'text', array('required' => true, 'attr' => Array('pattern' => '^[a-zA-Z áéíóúÁÉÍÓÚñÑ]*$')))
           ->add('email', 'email', array('required' => true))
           ->add('pass', 'password', array('required' => true))
           ->add('conf_pass', 'password', array('required' => true))
           ->getForm();
        
        return $form;
    }
    
    /**
     * Funcion para validar el formulario de registro
     * 
     * @param array $dataForm arreglo con datos del formulario
     */
    private function validateForm($dataForm)
    {
        $return = false;
        $validate = $this->get('validate');
        $security = $this->get('security');
        $countErrors = 0;
        
        if($security->validarPassword($dataForm['pass']))
        {
            if($dataForm['pass'] == $dataForm['conf_pass'])
            {
                $validateForm = array(
                    'first_name' => $validate->validateTextOnly($dataForm['first_name'], true),
                    'last_name' => $validate->validateTextOnly($dataForm['last_name'], true),
                    'email' => $validate->validateEmail($dataForm['email'], true),
                );
                
                foreach($validateForm as $vf)
                {
                    if($vf == false)
                        $countErrors ++;
                }
                
                if($countErrors == 0)
                    $return = true; 
            }
        }
        
        return $return;
    }
    
    /**
     * Funcion para enviar correo de confirmacion para registro de usuario
     * 
     * Esta funcion genera un token que es incluido en las url de confirmacion
     * 
     * @param Object $usuario entidad usuario
     */
    private function enviarEmailConfirmacion($usuario)
    {
        $security = $this->get('security');
        $mail = $this->get('mail');
        
        $hash = uniqid('r', true);
        $usuarioHash = $usuario->getUsuarioHash();
        $enc = $security->encriptar($hash.'/'.$usuarioHash);
        $strtoken = base64_encode($hash.'/'.$usuarioHash.'/'.$enc);
        $link = $this->getRequest()->getSchemeAndHttpHost().$this->generateUrl('confirmar_registro', array('token' => $strtoken));        
        
        $dataRender = array(
            'title' => $this->get('translator')->trans("confirmacion.registro", array(), 'mail'),
            'body' => $this->get('translator')->trans("confirmacion.registro.instrucciones", array(), 'mail').':',
            'link' => $link,
            'link_text' => $this->get('translator')->trans("confirmacion.registro.link", array(), 'mail'),
        );
        
        $mail->sendMail($usuario->getUsuarioEmail(), $this->get('translator')->trans("confirmacion.registro", array(), 'mail'), $dataRender);
    }
    
    /**
     * Accion para confirmar el registro de usuarios
     * 
     * @Route("/confirmar_registro/{token}", name="confirmar_registro")
     * @return type Description
     */
    public function confirmarRegistroAction($token)
    {
        $usuarioHash = $this->validateToken($token);
        if($usuarioHash)
        {
            // Verificar usuario con el hash
            $dql = "SELECT u FROM vocationetBundle:Usuarios u
                    WHERE u.usuarioHash = :usuarioHash";
            $em = $this->getDoctrine()->getEntityManager();
            $query = $em->createQuery($dql);
            $query->setParameter('usuarioHash', $usuarioHash);
            $query->setMaxResults(1);
            $result = $query->getResult();
            
            if(count($result) && isset($result[0]))
            {
                $usuario = $result[0];
                
                $usuario->setUsuarioEstado(1);
                $em->persist($usuario);
                $em->flush();
                
                // Iniciar sesion y redireccinar a home
                $this->get('security')->login($usuario);
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "time" => 30000, "title" => $this->get('translator')->trans("bienvenido"), "text" => $this->get('translator')->trans("registro.exitoso")));
                return $this->redirect($this->generateUrl('homepage'));
            }
            else 
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "warning", "time" => 30000, "title" => $this->get('translator')->trans("token.invalido"), "text" => $this->get('translator')->trans("token.invalido.instrucciones")));
                return $this->redirect($this->generateUrl('login'));
            }
        }
        else
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "warning", "time" => 30000, "title" => $this->get('translator')->trans("token.invalido"), "text" => $this->get('translator')->trans("token.invalido.instrucciones")));
            return $this->redirect($this->generateUrl('login'));
        }
        
        return new Response();
    }
    
    /**
     * Funcion que valida si un token es valido
     * @param string $token token recibido en la uri
     * @return boolean|string falso si el token es invalido, hash del usuario en caso contrario
     */
    private function validateToken($token)
    {
        $validate = false;
        
        $security = $this->get('security');
        
        if($security->validateBase64($token))
        {
            $explode = explode('/', base64_decode($token, false));
            $hash = (isset($explode[0])) ? $explode[0] : false;
            $usuarioHash = (isset($explode[1])) ? $explode[1] : false;
            $encToken = (isset($explode[2])) ? $explode[2] : false;
            
            if($hash && $usuarioHash && $encToken)
            {
                if($security->encriptar($hash.'/'.$usuarioHash) == $encToken)
                {
                    $validate = $usuarioHash;
                }
            }
        }
        
        return $validate;
    }
}
?>
