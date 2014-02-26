<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AT\vocationetBundle\Entity\Informacion;
use AT\vocationetBundle\Form\InformacionType;

/**
 * Controlador de información y/o publicidad del sidebar
 * @package vocationetBundle
 * @Route("/admin/informacion")
 * @author Camilo Quijano <camilo@altactic.com>
 * @version 1
 */
class InformacionController extends Controller
{
	/**
     * Listado de informacion
     * 
     * @Template("vocationetBundle:Informacion:index.html.twig")
	 * @Route("/", name="admin_informacion")
     * @Method("GET")
     * @return Response
     */
    public function indexAction()
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('vocationetBundle:Informacion')->findBy(array(), array('id' => 'DESC'));
        return array('entities' => $entities);
    }

	/**
     * Ver detalles de la información
	 *
     * @param Int $id Id de la información
     * @return Render Vista renderizada con detalles de la información
     * @Template("vocationetBundle:Informacion:show.html.twig")
     * @Route("/{id}/show", name="admin_informacion_show")
	 * @Method("GET")
    */
    public function showAction($id)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('vocationetBundle:Informacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Informacion entity.');
        }
		
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }
	
	/**
     * Agregar información
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Form de nueva información
     * @return Render Formulario de nueva información
     * @Template("vocationetBundle:Informacion:new.html.twig")
     * @Route("/new", name="admin_informacion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
		$entity = new Informacion();
        $form  = $this->createForm(new InformacionType(), $entity);
        
        if ($request->getMethod() == "POST")
        {
            $form->bind($request);
            if ($form->isValid()) 
            {
				if ($entity->getInformacionImagen()) {
					$em = $this->getDoctrine()->getManager();
					$entity->setCreated(new \DateTime());
					$em->persist($entity);
					$em->flush();
					
					// Subir imagen
					$rutaImagenes = $security->getParameter('ruta_images_informacion');
					$name = 'Informacion'.$entity->getId().'.png';
					$form['informacionImagen']->getData()->move($rutaImagenes, $name);
					
					$entity->setInformacionImagen($name);
					$em->persist($entity);
					$em->flush();
					
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("informacion.creada"), "text" => $this->get('translator')->trans("informacion.creada.correctamente")));
					return $this->redirect($this->generateUrl('admin_informacion_show', Array('id'=>$entity->getId())));
				}
            }
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

	
	/**
     * Editar Información
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Form de edición
     * @return Render Formulario de edición
     * @Template("vocationetBundle:Informacion:edit.html.twig")
     * @Route("/{id}/edit", name="admin_informacion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('vocationetBundle:Informacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Informacion entity.');
        }

        $editForm = $this->createForm(new InformacionType(), $entity);
        //$deleteForm = $this->createDeleteForm($id);
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bind($request);
            if ($editForm->isValid()) 
            {
				$name = 'Informacion'.$entity->getId().'.png';
				if ($entity->getInformacionImagen()) {
					$rutaImagenes = $security->getParameter('ruta_images_informacion');
					$editForm['informacionImagen']->getData()->move($rutaImagenes, $name);
				}
				
				$entity->setInformacionImagen($name);
				$entity->setModified(new \DateTime());
                $em->flush();

				$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("informacion.editada"), "text" => $this->get('translator')->trans("informacion.editada.correctamente")));
                return $this->redirect($this->generateUrl('admin_informacion_show', Array('id' => $id)));
            }
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
        }

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        );
    }
	
	/**
     * Borrar una información
	 *
     * @param \Symfony\Component\HttpFoundation\Request $request Form de eliminar información
     * @param Int $id Id de la información
     * @return Redirect Redirigir a listado de información
     * @Route("/{id}/delete", name="admin_informacion_delete")
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
            $entity = $em->getRepository('vocationetBundle:Informacion')->find($id);

            if (!$entity) { throw $this->createNotFoundException('Unable to find Informacion entity.'); }

            $rutaImagenes = $security->getParameter('ruta_images_informacion');
			@unlink($rutaImagenes.$entity->getInformacionImagen());
			
			$em->remove($entity);
            $em->flush();
			
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("informacion.eliminada"), "text" => $this->get('translator')->trans("informacion.eliminada.correctamente")));
        //}

        return $this->redirect($this->generateUrl('admin_informacion'));
    }

	/**
     * Creación de formulario para eliminar informacion
	 *
     * @param Int $id Id de la información
     * @return \Symfony\Component\Form\Form Formulario de eliminacion
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_informacion_delete', array('id' => $id)))
            ->setMethod('DELETE')
			->add('submit', 'button', array('label' => $this->get('translator')->trans("eliminar", Array(), "label"), 'attr' => array('class' => 'btn btn-danger confirmdelete', 'data-id' => $id, 'data-ent' => 'Informacion')))
            ->getForm();
    }
}
