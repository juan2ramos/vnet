<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AT\vocationetBundle\Entity\Usuarios;
use AT\vocationetBundle\Form\UsuariosType;

 /**
 * Controlador de Administrador de Usuarios
 * @package vocationetBundle
 * @Route("/admin/usuarios")
 * @author Camilo Quijano <camilo@altactic.com>
 * @version 1
 */
class UsuariosController extends Controller
{
	/**
     * Listado de usuarios rol estudiante
     * 
     * @Template("vocationetBundle:Usuarios:index.html.twig")
	 * @Route("/estudiantes", name="admin_usuarios_e")
     * @Method("GET")
     * @return Response
     */
    public function estudiantesAction()
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
        $entities = $this->getUsuarios(1);
        return array('entities' => $entities, 'filtro' => 1 );
    }

	/**
     * Listado de usuarios rol mentor experto
     * 
     * @Template("vocationetBundle:Usuarios:index.html.twig")
	 * @Route("/mentorExpertos", name="admin_usuarios_me")
     * @Method("GET")
     * @return Response
     */
    public function mentoresExpertosAction()
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
		$entities = $this->getUsuarios(2);
        return array('entities' => $entities, 'filtro' => 2 );
    }
	
	/**
     * Listado de usuarios rol mentor de orientacion vocacional
     * 
     * @Template("vocationetBundle:Usuarios:index.html.twig")
	 * @Route("/mentorVocacional", name="admin_usuarios_mov")
     * @Method("GET")
     * @return Response
     */
    public function mentoresOVAction()
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$entities = $this->getUsuarios(3);
        return array('entities' => $entities, 'filtro' => 3);
    }
	
	/**
     * Listado de usuarios rol mentor de orientacion vocacional
     * 
     * @Template("vocationetBundle:Usuarios:index.html.twig")
	 * @Route("/administrador", name="admin_usuarios_admin")
     * @Method("GET")
     * @return Response
     */
    public function administradoresAction()
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
        $entities = $this->getUsuarios(4);
        return array('entities' => $entities, 'filtro' => 4);
    }

	/**
     * Ver detalles del usuario
	 *
     * @param Int $id Id del usuario
     * @return Render Vista renderizada con detalles del usuario
     * @Template("vocationetBundle:Usuarios:show.html.twig")
     * @Route("/{id}/show", name="admin_usuarios_show")
	 * @Method("GET")
    */
    public function showAction($id)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('vocationetBundle:Usuarios')->find($id);

        if (!$entity) { throw $this->createNotFoundException('Unable to find Usuarios entity.'); }

        return array('entity' => $entity);
    }
	
	/**
     * Habilitar o des-habilitar un usuario mentor de orientacion vocacional por AJAX
     *
     * @Route("/chstate", name="edit_estado_rol_mentor")
     * @Method("POST")
	 * @param Request Id mentor y tipo de accion (1=>Aprobar 0=>Denegar)
     * @return Response json_encode
     */
    public function EditEstadoRelacionAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
		$usuario = $request->request->get('m'); // Id del mentor a cambiar estado
		$tipo = $request->request->get('t'); //Cambio de estado 1=>Aprobar 0=>Denegar

		$msg = $this->get('translator')->trans("error.en.solicitud", array(), 'messages');
		$status = 'error';
		
		$em = $this->getDoctrine()->getManager();
		$usuario = $em->getRepository('vocationetBundle:Usuarios')->findOneById($usuario);
		if ($usuario && ($tipo == 0 || $tipo == 1)) {
			if ($tipo == 1){
				// Usuario activo como mentor de orientacion vocacional
				$usuario->setUsuarioRolEstado(2);
				$msg = $this->get('translator')->trans('no.validar.mentor', Array(), 'label');
			} 
			elseif ($tipo == 0)
			{
				// Usuario no activo como mentor de orientacion vocacional
				$usuario->setUsuarioRolEstado(1);
				$msg = $this->get('translator')->trans('validar.mentor', Array(), 'label');
			}
			$status = 'success';
			$em->persist($usuario);
			$em->flush();
		}
		
		return new Response(json_encode(array(
            'status' => $status,
			'message' => $msg,
        )));
	}
	
	/**
     * Trae usuario que correspondan al rol que ingresa por parametro
	 * - Caso mentor, retorna ademas de datos del mentor, cantidad de mentorias que ha realizado
     *
	 * @param Int $rolId Id del rol
     * @return Object Usuarios
     */
	private function getUsuarios($rolId)
	{
		$em = $this->getDoctrine()->getManager();
		if($rolId == 2 or $rolId == 3) 
		{
			// Si es usuario tipo mentor, retornando cantidad de mentorias por usuario - necesario en calificacion de estrellas
			$dql = "SELECT COUNT(m.id) as cantidadMentorias, 
					u.id, u.usuarioImagen, u.usuarioApellido, u.usuarioNombre, u.usuarioEmail, u.usuarioEstado, u.usuarioProfesion, u.usuarioPuntos, u.usuarioValorMentoria,
					u.usuarioRolEstado
                FROM vocationetBundle:Usuarios u
                LEFT JOIN vocationetBundle:Mentorias m WITH (m.usuarioMentor = u.id AND m.mentoriaEstado = 1 AND m.calificacion IS NOT NULL)
				WHERE u.rol =:rolId
				GROUP BY u.id";
			$query = $em->createQuery($dql);
			$query->setParameter('rolId', $rolId);
			$entities = $query->getResult();
		} else 
		{
			// Caso estudiante, o administrador
			$entities = $em->getRepository('vocationetBundle:Usuarios')->findBy(Array('rol' => $rolId));
		}
        return $entities;
	}
}