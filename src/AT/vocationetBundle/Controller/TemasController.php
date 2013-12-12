<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AT\vocationetBundle\Entity\Temas;
use AT\vocationetBundle\Form\TemasType;

/**
 * Controlador de Temas
 * @package vocationetBundle
 * @Route("/admin/temas")
 * @author Camilo Quijano <camilo@altactic.com>
 * @version 1
 */
class TemasController extends Controller
{
	/**
     * Listado de temas
     * 
     * @Template("vocationetBundle:Temas:index.html.twig")
	 * @Route("/", name="admin_temas")
     * @Method("GET")
     * @return Response
     */
    public function indexAction()
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('vocationetBundle:Temas')->findBy(array(), array('nombre' => 'ASC'));
        return array('entities' => $entities);
    }

	/**
     * Ver detalles de temas
	 *
     * @param Int $id Id del tema
     * @return Render Vista renderizada con detalles del tema
     * @Template("vocationetBundle:Temas:show.html.twig")
     * @Route("/{id}/show", name="admin_temas_show")
	 * @Method("GET")
    */
    public function showAction($id)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('vocationetBundle:Temas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Temas entity.');
        }
		
		$foros = $this->get('foros')->ForosCarrerasTemas($entity->getCarrera()->getId(), $id);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
			'foros'		  => $foros,
            'delete_form' => $deleteForm->createView(),
        );
    }
	
	/**
     * Agregar tema
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Form de nuevo tema
     * @return Render Formulario de nuevo tema
     * @Template("vocationetBundle:Temas:new.html.twig")
     * @Route("/new", name="admin_temas_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
		$entity = new Temas();
        $form  = $this->createForm(new TemasType(), $entity);
        
        if ($request->getMethod() == "POST")
        {
            $form->bind($request);
            if ($form->isValid()) 
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
				
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("tema.creado"), "text" => $this->get('translator')->trans("tema.creado.correctamente")));
                return $this->redirect($this->generateUrl('admin_temas'));
            }
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
	
	/**
     * Editar Tema
     *
     * @param \Symfony\Component\HttpFoundation\Request $request Form de edición
     * @return Render Formulario de edición
     * @Template("vocationetBundle:Temas:edit.html.twig")
     * @Route("/{id}/edit", name="admin_temas_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('vocationetBundle:Temas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Temas entity.');
        }

        $editForm = $this->createForm(new TemasType(), $entity);
        //$deleteForm = $this->createDeleteForm($id);
        
        if ($request->getMethod() == 'POST')
        {
            $editForm->bind($request);
            if ($editForm->isValid()) 
            {
                $em->flush();

				$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("tema.editado"), "text" => $this->get('translator')->trans("tema.editado.correctamente")));
                return $this->redirect($this->generateUrl('admin_temas_show', Array('id' => $id)));
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
     * Borrar un tema
	 *
     * @param \Symfony\Component\HttpFoundation\Request $request Form de eliminar tema
     * @param Int $id Id del tema
     * @return Redirect Redirigir a listado de temas
     * @Route("/{id}/delete", name="admin_temas_delete")
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
            $entity = $em->getRepository('vocationetBundle:Temas')->find($id);

            if (!$entity) {  throw $this->createNotFoundException('Unable to find Temas entity.'); }


			$foros = $em->getRepository('vocationetBundle:Foros')->findByTema($entity);
		
			if ($foros) {
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.al.eliminar"), "text" => $this->get('translator')->trans("no.puede.eliminar.este.tema.asociados.foros")));
			} else {
				$em->remove($entity);
				$em->flush();
				
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("tema.eliminado"), "text" => $this->get('translator')->trans("tema.eliminado.correctamente")));
			}
        //}
        return $this->redirect($this->generateUrl('admin_temas'));
    }

	/**
     * Creación de formulario para eliminar tema
	 *
     * @param Int $id Id del tema
     * @return \Symfony\Component\Form\Form Formulario de eliminacion
     */
    private function createDeleteForm($id)
    {
		return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_temas_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'button', array('label' => $this->get('translator')->trans("eliminar", Array(), "label"), 'attr' => array('class' => 'btn btn-danger confirmdelete', 'data-id' => $id, 'data-ent' => 'Tema')))
            ->getForm();
    }
}