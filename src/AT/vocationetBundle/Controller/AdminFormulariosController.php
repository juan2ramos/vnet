<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador para administracio de formularios
 * 
 * @Route("/admin/formularios")
 * @author Diego Malagón <diego@altactic.com>
 */
class AdminFormulariosController extends Controller
{
    /**
     * Accion index para formularios
     * 
     * @Route("/{fid}", name="admin_formularios", defaults={"fid" = 0})
     * @Template("vocationetBundle:AdminFormularios:index.html.twig")
     * @return Response
     */
    public function indexAction(Request $request, $fid = false)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $formularios_serv = $this->get('formularios');        
        $formularios = $this->getFormularios();
        $subFormularios = false;
        $form = $this->createFormPreguntas();        
        
        
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $data = $form->getData();
                
                $this->registrarPregunta($data);
                
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("pregunta.agregada"), "text" => $this->get('translator')->trans("pregunta.agregada.correctamente")));
                return $this->redirect($this->generateUrl('admin_formularios', array('fid'=>$fid)));
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
            }
        }
        
        if($fid)
        {
            $subFormularios = $formularios_serv->getFormulario($fid);
        }
        
        return array(
            'fid' => $fid,
            'formularios' => $formularios,
            'subformularios' => $subFormularios,
            'form' => $form->createView(),
        );
    }
    
    /**
     * Accion ajax para eliminar una pregunta
     * 
     * @Route("/delete/{pid}", name="delete_pregunta")
     * @Method({"POST"})
     * @param integer $pid id de pregunta
     */
    public function deleteAction($pid)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $response = array(
            'status' => 'error',
            'message' => 'test',
            'detail' => 'test'
        );        
        
        return new Response(json_encode($response));
    }
    
    
    private function getFormularios()
    {
        $dql = "SELECT
                    f.id,
                    f.nombre,
                    f.numero
                FROM 
                    vocationetBundle:Formularios f
                    JOIN vocationetBundle:Formularios sf WITH sf.formulario = f.id
                WHERE
                    f.formulario IS NULL  
                GROUP BY f.id
                ORDER BY f.numero
                ";
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $result = $query->getResult();
        
        return $result;
    }
    
    
    private function createFormPreguntas()
    {
        //  Obtener tipos de preguntas
        $dql = "SELECT pt.id, pt.nombre FROM vocationetBundle:PreguntasTipos pt";
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $result = $query->getResult();
        $preguntasTipos = array();
        
        foreach($result as $r)
        {
            $preguntasTipos[$r['id']] = $r['nombre'];
        }
        
        
        $dataForm = array(
            'pregunta' => null, 
            'numero' => null,
            'formulario' => null,
            'preguntaTipo' => null,
            'opciones' => null,
            'pesos' => null
        );
        
        $form = $this->createFormBuilder($dataForm)
           ->add('pregunta', 'textarea', array('required' => true))
           ->add('numero', 'number', array('required' => true))
           ->add('formulario', 'text', array('required' => true))
           ->add('preguntaTipo', 'choice', array('required' => true, 'choices' => $preguntasTipos))
           ->add('opciones', 'text', array('required' => false))
           ->add('pesos', 'text', array('required' => false))
           ->getForm();
        
        return $form;        
    }
    
    
    private function registrarPregunta($data)
    {
        $em = $this->getDoctrine()->getManager();
        
        $pregunta = new \AT\vocationetBundle\Entity\Preguntas();
        
        $formulario = $em->getRepository("vocationetBundle:Formularios")->findOneById($data['formulario']);
        $preguntaTipo = $em->getRepository("vocationetBundle:PreguntasTipos")->findOneById($data['preguntaTipo']);
        
        $pregunta->setPregunta($data['pregunta']);
        $pregunta->setNumero($data['numero']);
        $pregunta->setPreguntaTipo($preguntaTipo);
        $pregunta->setFormulario($formulario);
        
        $em->persist($pregunta);
        
        // Registrar opciones de respuesta
        if($data['preguntaTipo'] == 1 || $data['preguntaTipo'] == 2 || $data['preguntaTipo'] == 3)
        {
            if(isset($data['opciones']) && count($data['opciones']))
            {
                foreach($data['opciones'] as $ko => $o)
                {
                    $opcion = new \AT\vocationetBundle\Entity\Opciones();
                    
                    $opcion->setNombre($o);
                    $opcion->setPeso($data['pesos'][$ko]);
                    $opcion->setPregunta($pregunta);
                    
                    $em->persist($opcion);
                }
                
            }
        }        
        $em->flush();
    }
    
}

?>