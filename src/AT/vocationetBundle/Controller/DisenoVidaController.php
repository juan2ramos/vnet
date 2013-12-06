<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador para diseño de vida
 * 
 * El id para este formulario principal es 10
 *
 * @Route("/disenovida")
 * @author Diego Malagón <diego@altactic.com>
 */
class DisenoVidaController extends Controller
{
    /**
     * Index de diseño de vida
     * 
     * @Route("/", name="diseno_vida")
     * @Template("vocationetBundle:DisenoVida:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $formularios_serv = $this->get('formularios');
        $form_id = $this->get('formularios')->getFormId('diseno_vida');
        $usuarioId = $security->getSessionValue("id");
        $formulario = $formularios_serv->getInfoFormulario($form_id);
        $formularios = $formularios_serv->getFormulario($form_id);
        $form = $this->createFormCuestionario();
        
        return array(
            'formulario_info' => $formulario,
            'formularios' => $formularios,
            'form' => $form->createView(),
        );
    }
    
    
    /**
     * Index de diseño de vida
     * 
     * @Route("/procesar", name="procesar_disenovida")
     * @Method({"POST"})
     * @return Response
     */
    public function procesarAction(Request $request)
    {
        return new Response();
    }
    
    
    /**
     * Funcion para crear un formulario vacio para cuestionarios
     * 
     * Se usa unicamente para proteccion csrf
     * 
     * @return Object formulario
     */
    private function createFormCuestionario()
    {
        $form = $this->createFormBuilder()
            ->getForm();
        
        return $form;
    }
    
}

?>