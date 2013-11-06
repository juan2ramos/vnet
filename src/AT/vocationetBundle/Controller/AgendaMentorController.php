<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para la agenda de mentor
 *
 * @Route("/agenda_mentor")
 * @author Diego Malagón <diego@altactic.com>
 */
class AgendaMentorController extends Controller
{
    /**
     * Index de la agenda de mentor
     * 
     * @Route("/", name="agenda_mentor")
     * @Template("vocationetBundle:AgendaMentor:index.html.twig")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        $usuarioId = $security->getSessionValue('id');
        
        
        $form = $this->createMentoriaForm();
        
        if($request->getMethod() == 'POST') 
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $data = $form->getData();
                $usuarioId = $security->getSessionValue('id');
                
                $mentoriaInicio = new \DateTime($data['fecha'].' '.$data['hora'].':'.$data['min'].':00');
                $mentoriaFin = new \DateTime();
                $mentoriaFin->setTimestamp($mentoriaInicio->getTimestamp());
                $mentoriaFin->modify('+1 hour');
                
                $mentoria = new \AT\vocationetBundle\Entity\Mentorias();
                $mentoria->setUsuarioMentor($usuarioId);
                $mentoria->setMentoriaInicio($mentoriaInicio);
                $mentoria->setMentoriaFin($mentoriaFin);
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($mentoria);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("mentoria.agregada"), "text" => $this->get('translator')->trans("mentoria.agregada.correctamente")));
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
            }
        }
        
        $mentorias = $this->getMentorias($usuarioId);
        return array(
            'form' => $form->createView(),
            'mentorias' => $mentorias
        );
    }
    
    private function createMentoriaForm()
    {
        $data = array(
            'fecha' => date('Y-m-d'),
            'hora' => null,
            'min' => null
        );
        $form = $this->createFormBuilder($data)
           ->add('fecha', 'text', array('required' => true))
           ->add('hora', 'text', array('required' => true))
           ->add('min', 'text', array('required' => true))
           ->getForm();
        
        return $form; 
    }
    
    /**
     * Funcion para obtener las mentorias registradas por el usuario
     * 
     * @param type $usuarioId
     */
    private function getMentorias($usuarioId)
    {
        $dql = "SELECT
                    m.id,
                    m.mentoriaInicio,
                    m.mentoriaFin,
                    u.id estudianteId,
                    u.usuarioNombre,
                    u.usuarioApellido
                FROM 
                    vocationetBundle:Mentorias m
                    LEFT JOIN vocationetBundle:Usuarios u WITH m.usuarioEstudiante = u.id
                WHERE
                    m.usuarioMentor = :usuarioId";
        
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $result = $query->getResult();
        
        return $result;
    }
    
}
?>
