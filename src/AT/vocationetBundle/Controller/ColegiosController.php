<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AT\vocationetBundle\Entity\Colegios;
use AT\vocationetBundle\Form\ColegiosType;

/**
 * Controlador de Colegios
 * @package vocationetBundle
 * @Route("/admin/colegios")
 * @author Camilo Quijano <camilo@altactic.com>
 * @version 1
 */
class ColegiosController extends Controller
{
	/**
     * Listado de colegios
     * 
     * @Template("vocationetBundle:Colegios:index.html.twig")
	 * @Route("/", name="admin_colegios")
     * @Method("GET")
     * @return Response
     */
    public function indexAction()
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('vocationetBundle:Colegios')->findBy(array(), array('nombre' => 'ASC'));
        return array('entities' => $entities);
    }
	
	/**
     * Ver detalles del colegio
	 *
     * @param Int $id Id del colegio
     * @return Render Vista renderizada con detalles del colegio
     * @Template("vocationetBundle:Colegios:show.html.twig")
     * @Route("/{id}/show", name="admin_colegios_show")
	 * @Method("GET")
    */
    public function showAction($id)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('vocationetBundle:Colegios')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Carreras entity.');
        }
		
		$usuariosColegio = $em->getRepository('vocationetBundle:Usuarios')->findBy(array('colegio' => $entity), array('usuarioNombre' => 'ASC'));
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
			'usuarios'		  => $usuariosColegio,
            'delete_form' => $deleteForm->createView(),
        );
    }

	/**
     * Agregar colegio
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Form de nuevo colegio
     * @return Render Formulario de nueva colegio
     * @Template("vocationetBundle:Colegios:new.html.twig")
     * @Route("/new", name="admin_colegios_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
		$entity = new Colegios();
        $form  = $this->createForm(new ColegiosType(), $entity);
        
        if ($request->getMethod() == "POST")
        {
            $form->bind($request);
            if ($form->isValid()) 
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
				
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("colegio.creado"), "text" => $this->get('translator')->trans("colegio.creado.correctamente")));
                return $this->redirect($this->generateUrl('admin_colegios'));
            }
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Editar Colegio
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Form de edición
     * @return Render Formulario de edición
     * @Template("vocationetBundle:Colegios:edit.html.twig")
     * @Route("/{id}/edit", name="admin_colegios_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('vocationetBundle:Colegios')->find($id);

        if (!$entity) { throw $this->createNotFoundException('Unable to find Colegios entity.');  }

        $editForm = $this->createForm(new ColegiosType(), $entity);
        //$deleteForm = $this->createDeleteForm($id);
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bind($request);
            if ($editForm->isValid()) 
            {
                $em->flush();

				$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("colegio.editado"), "text" => $this->get('translator')->trans("colegio.editado.correctamente")));
                return $this->redirect($this->generateUrl('admin_colegios_show', Array('id' => $id)));
            }
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        );
    }

	/**
     * Borrar un colegio
	 *
     * @param \Symfony\Component\HttpFoundation\Request $request Form de eliminar colegio
     * @param Int $id Id de la colegio
     * @return Redirect Redirigir a listado de colegios
     * @Route("/{id}/delete", name="admin_colegios_delete")
     * @Method("DELETE")                                                                    {
     */
    public function deleteAction(Request $request, $id)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
		//$form = $this->createDeleteForm($id);
        //$form->handleRequest($request);
        //if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('vocationetBundle:Colegios')->find($id);

            if (!$entity) { throw $this->createNotFoundException('Unable to find Colegios entity.'); }
			
			$usuariosColegio = $em->getRepository('vocationetBundle:Usuarios')->findBy(array('colegio' => $entity));
			
			if ($usuariosColegio) {
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.al.eliminar"), "text" => $this->get('translator')->trans("no.puede.eliminar.este.colegio.asociados.usuarios")));
			} else {
				$em->remove($entity);
				$em->flush();
				
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("colegio.eliminado"), "text" => $this->get('translator')->trans("colegio.eliminado.correctamente")));
			}
        //}
		return $this->redirect($this->generateUrl('admin_colegios'));
    }

	/**
     * Creación de formulario para eliminar colegio
	 *
     * @param Int $id Id del colegio
     * @return \Symfony\Component\Form\Form Formulario de eliminacion
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_colegios_delete', array('id' => $id)))
            ->setMethod('DELETE')
			->add('submit', 'button', array('label' => $this->get('translator')->trans("eliminar", Array(), "label"), 'attr' => array('class' => 'btn btn-danger confirmdelete', 'data-id' => $id, 'data-ent' => 'Colegio')))
            ->getForm();
    }
}