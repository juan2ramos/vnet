<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AT\vocationetBundle\Entity\Carreras;
use AT\vocationetBundle\Form\CarrerasType;

/**
 * Controlador de Carreras
 * @package vocationetBundle
 * @Route("/admin/carreras")
 * @author Camilo Quijano <camilo@altactic.com>
 * @version 1
 */
class CarrerasController extends Controller
{
	/**
     * Listado de carreras
     * 
     * @Template("vocationetBundle:Carreras:index.html.twig")
	 * @Route("/", name="admin_carreras")
     * @Method("GET")
     * @return Response
     */
    public function indexAction()
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('vocationetBundle:Carreras')->findBy(array(), array('nombre' => 'ASC'));
        return array('entities' => $entities);
    }

	/**
     * Ver detalles de la carrera
	 *
     * @param Int $id Id de la carrera
     * @return Render Vista renderizada con detalles de la carrera
     * @Template("vocationetBundle:Carreras:show.html.twig")
     * @Route("/{id}/show", name="admin_carreras_show")
	 * @Method("GET")
    */
    public function showAction($id)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('vocationetBundle:Carreras')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Carreras entity.');
        }
		
		$temas = $this->get('foros')->getTemasCountForos($id);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
			'temas'		  => $temas,
            'delete_form' => $deleteForm->createView(),
        );
    }

	/**
     * Agregar carrera
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Form de nuevo carrera
     * @return Render Formulario de nueva carrera
     * @Template("vocationetBundle:Carreras:new.html.twig")
     * @Route("/new", name="admin_carreras_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
		$entity = new Carreras();
        $form  = $this->createForm(new CarrerasType(), $entity);
        
        if ($request->getMethod() == "POST")
        {
            $form->bind($request);
            if ($form->isValid()) 
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
				
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("carrera.creada"), "text" => $this->get('translator')->trans("carrera.creada.correctamente")));
                return $this->redirect($this->generateUrl('admin_carreras'));
            }
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
	
	/**
     * Editar Carrera
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Form de edición
     * @return Render Formulario de edición
     * @Template("vocationetBundle:Carreras:edit.html.twig")
     * @Route("/{id}/edit", name="admin_carreras_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('vocationetBundle:Carreras')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TblCargo entity.');
        }

        $editForm = $this->createForm(new CarrerasType(), $entity);
        //$deleteForm = $this->createDeleteForm($id);
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bind($request);
            if ($editForm->isValid()) 
            {
                $em->flush();

				$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("carrera.editada"), "text" => $this->get('translator')->trans("carrera.editada.correctamente")));
                return $this->redirect($this->generateUrl('admin_carreras_show', Array('id' => $id)));
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
     * Borrar una carrera
	 *
     * @param \Symfony\Component\HttpFoundation\Request $request Form de eliminar carrera
     * @param Int $id Id de la carrera
     * @return Redirect Redirigir a listado de carreras
     * @Route("/{id}/delete", name="admin_carreras_delete")
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
            $entity = $em->getRepository('vocationetBundle:Carreras')->find($id);

            if (!$entity) {	throw $this->createNotFoundException('Unable to find Carreras entity.'); }
			
			$temas = $em->getRepository('vocationetBundle:Temas')->findByCarrera($entity);
			$alternativas = $em->getRepository('vocationetBundle:AlternativasEstudios')->findByCarrera($entity);
			
			if ($temas or $alternativas) {
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.al.eliminar"), "text" => $this->get('translator')->trans("no.puede.eliminar.esta.carrera.asociados.temas.o.alternativas")));
			} else {
				$em->remove($entity);
				$em->flush();
				
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("carrera.eliminada"), "text" => $this->get('translator')->trans("carrera.eliminada.correctamente")));
			}
        //}
        return $this->redirect($this->generateUrl('admin_carreras'));
    }

	/**
     * Creación de formulario para eliminar carrera
	 *
     * @param Int $id Id de la carrera
     * @return \Symfony\Component\Form\Form Formulario de eliminacion
     */
    private function createDeleteForm($id)
    {
		return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_carreras_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'button', array('label' => $this->get('translator')->trans("eliminar", Array(), "label"), 'attr' => array('class' => 'btn btn-danger confirmdelete', 'data-id' => $id, 'data-ent' => 'Carrera')))
            ->getForm();
    }
}