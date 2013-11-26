<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para el cuestionario de diagnostico
 *
 * @Route("/diagnostico")
 * @author Diego Malagón <diego@altactic.com>
 */
class DiagnosticoController extends Controller
{
    /**
     * Index de el cuestionario de diagnostico
     * 
     * @Route("/", name="diagnostico")
     * @Template("vocationetBundle:Diagnostico:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $preguntas_serv = $this->get('preguntas');
        
        $formularios = $preguntas_serv->getFormulario(1);
        $form = $this->createFormCuestionario();
        
        return array(
            'formularios' => $formularios,
            'form' => $form->createView(),
        );
    }
    
    
    /**
     * Accion que recibe y procesa el cuestionario de diagnostico
     * 
     * @Route("/procesar", name="procesar_diagnostico")
     * @Method({"POST"})
     * @param \AT\vocationetBundle\Controller\Request $request
     */
    public function procesarCuestionario(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $form = $this->createFormCuestionario();
        
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $respuestas = $request->get('pregunta');
                
                $security->debug($respuestas);
            }
        }
        return new Response();
    }
            
    private function createFormCuestionario()
    {
        $form = $this->createFormBuilder()
            ->getForm();
        
        return $form;
    }
}
?>
