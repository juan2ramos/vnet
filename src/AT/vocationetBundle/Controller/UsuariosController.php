<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AT\vocationetBundle\Entity\Usuarios;
use AT\vocationetBundle\Form\UsuariosType;

/**
 * Usuarios controller.
 *
 * @Route("/admin/usuarios")
 */
class UsuariosController extends Controller
{

    /**
     * Lists all Usuarios entities.
     *
	 * @Template("vocationetBundle:Usuarios:index.html.twig")
     * @Route("/estudiantes", name="admin_usuarios_e")
     * @Method("GET")
     */
    public function estudiantesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('vocationetBundle:Usuarios')->findBy(Array('rol' => 1));
        return array('entities' => $entities, 'filtro' => 1 );
    }
	
	/**
     * Lists all Usuarios entities.
     *
	 * @Template("vocationetBundle:Usuarios:index.html.twig")
     * @Route("/mentorExpertos", name="admin_usuarios_me")
     * @Method("GET")
     */
    public function mentoresExpertosAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('vocationetBundle:Usuarios')->findBy(Array('rol' => 2));
        return array('entities' => $entities, 'filtro' => 2 );
    }
	
	/**
     * Lists all Usuarios entities.
     *
	 * @Template("vocationetBundle:Usuarios:index.html.twig")
     * @Route("/mentorVocacional", name="admin_usuarios_mov")
     * @Method("GET")
     */
    public function mentoresOVAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('vocationetBundle:Usuarios')->findBy(Array('rol' => 3));
        return array('entities' => $entities, 'filtro' => 3);
    }
	
    /**
     * Creates a new Usuarios entity.
     *
     * @Route("/", name="admin_usuarios_create")
     * @Method("POST")
     * @Template("vocationetBundle:Usuarios:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Usuarios();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_usuarios_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Usuarios entity.
    *
    * @param Usuarios $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Usuarios $entity)
    {
        $form = $this->createForm(new UsuariosType(), $entity, array(
            'action' => $this->generateUrl('admin_usuarios_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Usuarios entity.
     *
     * @Route("/new", name="admin_usuarios_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Usuarios();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Usuarios entity.
     *
     * @Route("/{id}", name="admin_usuarios_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('vocationetBundle:Usuarios')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuarios entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Usuarios entity.
     *
     * @Route("/{id}/edit", name="admin_usuarios_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('vocationetBundle:Usuarios')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuarios entity.');
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
    * Creates a form to edit a Usuarios entity.
    *
    * @param Usuarios $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Usuarios $entity)
    {
        $form = $this->createForm(new UsuariosType(), $entity, array(
            'action' => $this->generateUrl('admin_usuarios_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Usuarios entity.
     *
     * @Route("/{id}", name="admin_usuarios_update")
     * @Method("PUT")
     * @Template("vocationetBundle:Usuarios:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('vocationetBundle:Usuarios')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuarios entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_usuarios_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Usuarios entity.
     *
     * @Route("/{id}", name="admin_usuarios_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('vocationetBundle:Usuarios')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Usuarios entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_usuarios'));
    }

    /**
     * Creates a form to delete a Usuarios entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_usuarios_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
