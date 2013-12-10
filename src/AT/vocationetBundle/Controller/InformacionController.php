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
 * Informacion controller.
 *
 * 
 */
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
		//if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('vocationetBundle:Informacion')->findBy(array(), array('id' => 'DESC'));
        return array('entities' => $entities);
    }
	
    /**
     * Creates a new Informacion entity.
     *
     * @Route("/", name="admin_informacion_create")
     * @Method("POST")
     * @Template("vocationetBundle:Informacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Informacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_informacion_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Informacion entity.
    *
    * @param Informacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Informacion $entity)
    {
        $form = $this->createForm(new InformacionType(), $entity, array(
            'action' => $this->generateUrl('admin_informacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Informacion entity.
     *
     * @Route("/new", name="admin_informacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Informacion();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Informacion entity.
     *
     * @Route("/{id}", name="admin_informacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
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
     * Displays a form to edit an existing Informacion entity.
     *
     * @Route("/{id}/edit", name="admin_informacion_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('vocationetBundle:Informacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Informacion entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Informacion entity.
    *
    * @param Informacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Informacion $entity)
    {
        $form = $this->createForm(new InformacionType(), $entity, array(
            'action' => $this->generateUrl('admin_informacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Informacion entity.
     *
     * @Route("/{id}", name="admin_informacion_update")
     * @Method("PUT")
     * @Template("vocationetBundle:Informacion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('vocationetBundle:Informacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Informacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_informacion_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Informacion entity.
     *
     * @Route("/{id}", name="admin_informacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('vocationetBundle:Informacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Informacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_informacion'));
    }

    /**
     * Creates a form to delete a Informacion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_informacion_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
