<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador para la evaluacion 360
 * 
 * El id para este formulario principal es 9
 *
 * @Route("/evaluacion360")
 * @author Diego Malagón <diego@altactic.com>
 */
class Evaluacion360Controller extends Controller
{
    /**
     * Index de la evaluacion 360
     * 
     * Aca el usuario ingresa los correos de las personas que invita
     * para que contesten su evaluacion 360
     * 
     * @Route("/", name="evaluacion360")
     * @Template("vocationetBundle:Evaluacion360:index.html.twig")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
//        
        $formularios_serv = $this->get('formularios');
        $form_id = 9;
        
        $form = $this->createFormBuilder()
            ->add('emails', 'text', array('required' => true))
            ->getForm();
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $data = $form->getData();
                
                $emails = $this->validateForm($data);
                
                if(count($emails) < 3)
                {
                    $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("emails.invalidos"), "text" => $this->get('translator')->trans("debe.seleccionar.mas.emails")));
                }
                else
                {
                    
                }
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
            }
        }
        
        
        $formulario = $formularios_serv->getInfoFormulario($form_id);
        
        return array(
            'formulario_info' => $formulario,
            'form' => $form->createView(),
        );
    }
    
    /**
     * Funcion que valida la lista de correos del formulario
     * @param array $data
     * @return array|boolean 
     */
    private function validateForm($data)
    {
        $validate = $this->get('validate');
        $val = false;
            
        $emails = $data['emails'];        
        
        foreach($emails as $email)
        {
           if($validate->validateEmail($email))
           {
               $val[] = $email;
           }
        }
                
        return $val;
    }
}