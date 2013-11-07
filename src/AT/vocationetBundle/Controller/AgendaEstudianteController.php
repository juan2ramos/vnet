<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para la agenda de estudiante
 *
 * @Route("/agenda_estudiante")
 * @author Diego Malagón <diego@altactic.com>
 */
class AgendaEstudianteController extends Controller
{
    /**
     * Index de la agenda de estudiante
     * 
     * @Route("/", name="agenda_estudiante")
     * @Template("vocationetBundle:AgendaEstudiante:index.html.twig")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        $usuarioId = $security->getSessionValue('id');
        
        $mentorias = $this->getMentorias($usuarioId);
        
        return array(
            'mentorias' => $mentorias
        );
    }
    
    /**
     * Funcion para obtener las mentorias disponibles y aceptadas
     * 
     * Obtiene las mentorias disponibles registradas por los mentores con los que tiene relacion
     * Obtiene tambien las mentorias a las que aplico
     * 
     * @param integer $usuarioId id de usuario
     */
    private function getMentorias($usuarioId)
    {
        /**
         * SELECT u.id, u.usuario_nombre, m.* FROM 
         * usuarios u
         * JOIN relaciones r ON r.usuario_id = u.id OR r.usuario2_id = u.id
         * JOIN mentorias m ON u.id = m.usuario_mentor_id
         * WHERE 
         * (r.usuario_id = 6 OR r.usuario2_id = 6)
         * AND u.id != 6
         * AND r.tipo = 2
         * AND (m.usuario_estudiante_id = 6 OR m.usuario_estudiante_id IS NULL);
         */
        
        $dql = "SELECT 
                    m.id,
                    u.usuarioNombre,
                    u.usuarioApellido,
                    m.mentoriaInicio,
                    m.mentoriaFin,
                    m.usuarioEstudiante estudianteId
                FROM
                    vocationetBundle:Usuarios u
                    JOIN vocationetBundle:Relaciones r WITH r.usuario = u.id OR r.usuario2 = u.id
                    JOIN vocationetBundle:Mentorias m WITH u.id = m.usuarioMentor
                WHERE
                    (r.usuario = :usuarioId OR r.usuario2 = :usuarioId)
                    AND u.id != :usuarioId
                    AND r.tipo = 2
                    AND r.estado = 1
                    AND (m.usuarioEstudiante = :usuarioId OR m.usuarioEstudiante IS NULL)
        ";
        
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $result = $query->getResult();
        
        return $result;
    }
    
    /**
     * Accion ajax para el detalle de una mentoria
     * 
     * @Route("/{id}", name="show_mentoria_estudiante")
     * @Template("vocationetBundle:AgendaEstudiante:show.html.twig")
     * @return Response
     */
    public function showMentoriaAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        if(!$this->getRequest()->isXmlHttpRequest()) throw $this->createNotFoundException();
        
        $mentoria = false;
        
        $dql = "SELECT
                    m.id,
                    m.mentoriaInicio,
                    m.mentoriaFin,
                    m.usuarioEstudiante estudianteId,
                    u.usuarioNombre,
                    u.usuarioApellido,
                    u.usuarioImagen
                FROM 
                    vocationetBundle:Mentorias m
                    LEFT JOIN vocationetBundle:Usuarios u WITH m.usuarioMentor = u.id
                WHERE
                    m.id = :mentoriaId";
        
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('mentoriaId', $id);
        $result = $query->getResult();
        
        if(isset($result[0]))
        {
            $mentoria = $result[0];
        }
        
        return array(
            'mentoria' => $mentoria
        );
        
    }
    
    /**
     * 
     * Accion para separar una mentoria
     * 
     * @Route("/separar/{id}", name="separar_mentoria_estudiante")
     * @return Response
     */
    public function separarMentoriaAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
    }
}
?>
