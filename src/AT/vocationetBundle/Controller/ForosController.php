<?php

namespace AT\vocationetBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AT\vocationetBundle\Entity\Comentarios;
use AT\vocationetBundle\Entity\Foros;
use AT\vocationetBundle\Entity\ForosArchivos;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Controlador de foros
 * @package vocationetBundle
 * @Route("/foros/")
 */
class ForosController extends Controller
{
    /**
     * Listado de foros y temas de la carrera que ingresa por parametro
     *
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @Route("c_{id}/t_{temaId}/f_{foroId}", name="foros_temas", defaults={"temaId" = 0, "foroId" = 0, "id" = 0})
     * @Template("vocationetBundle:Foros:index.html.twig")
     * @Template("vocationetBundle:Foros:showForo.html.twig")
     * @Method("GET")
     * @param Int $id Id de la carrera
     * @param Int $temaId Id del tema
     * @param Int $foroId Id del foro
     * @return Render
     */
    public function indexAction($id, $temaId, $foroId)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$em = $this->getDoctrine()->getManager();
        $carreras = $em->getRepository('vocationetBundle:Carreras')->findAll();

		/*
        if($id == 0){
			if($carreras){
				$id = $carreras[0]->getId();
			}
		}
		*/
		
        $temas = $this->get('foros')->getTemasCountForos($id);
        $PosActual = Array('carreraId' => $id, 'temaId' => $temaId);

        if ($foroId)
        {
			// If hay foro seleccionado redireccionara el show del foro
			$foro = $em->getRepository('vocationetBundle:Foros')->findOneById($foroId);

			if (!$foro){
				throw $this->createNotFoundException($this->get('translator')->trans("foro.no.existe"));
			}

			// Validacion de que al foro que intenta acceder corresponde a la carrera seleccionada
			if ($foro->getTema()->getCarrera()->getId() != $id) {
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("ingreso.invalido")));
				return $this->redirect($this->generateUrl('foros_temas', array('id'=> $id, 'temaId' => $temaId)));
			}

			$foroAdjuntos = $this->getAdjuntosForos($foroId);
			$comentarios = $this->getComentarios($foroId);
			$deleteForm = $this->createDeleteForm($foroId);
			
			return $this->render('vocationetBundle:Foros:showForo.html.twig', array(
				'carreras' => $carreras,
				'temas' => $temas,
				'actual' => $PosActual,
				'foro' => $foro,
				'foroAdjuntos' => $foroAdjuntos,
				'comentarios' => $comentarios,
				'delete_form' => $deleteForm->createView()));
		}

		else
		{
			$foros = $this->get('foros')->ForosCarrerasTemas($id, $temaId);
			return $this->render('vocationetBundle:Foros:index.html.twig', array(
					'carreras' => $carreras, 'temas' => $temas,	'actual' => $PosActual,	'foros' => $foros ));
		}
    }

	/**
     * Crear un comentario
     * 
     * Funcion que permite crear un comentario desde una peticion AJAX
     * envia $request tipo, 1=>respuesta a un comentario, 0=>Comentario principal o normal
     * envia $request idforo, Id del forp
     * envia $request comentario, variable con contenido del comentario
     * envia $request idcomrespuesta, Id del comentario a responder, si es comentario principal viene con 0
     * 
     * @author Camilo Quijano
     * @version 1
     * @Method("POST")
     * @Route("comentar", name="crear_comentario")
     * @param Request $request Request enviado desde ajax, con el id, tipo, id de comentario respuesta y contenido del comentario
     * @return object Vista renderizada con el comentario creado
     */
    public function comentarAction(Request $request)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
		$usuarioId = $security->getSessionValue('id');
		$usuarioImagen = $security->getSessionValue('usuarioImagen');
		$usuarioName = $security->getSessionValue('usuarioNombre')." ".$security->getSessionValue('usuarioApellido');

		$id = $request->get('idforo');
        $comentarioTexto = $request->get('comentario');
        $tipo = $request->get('tipo');
        $comentarioRespuestaId = $request->get('idcomrespuesta');
        $fechaCreacion = new \DateTime();

        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('vocationetBundle:Usuarios')->findOneById($usuarioId);
        $foro = $em->getRepository('vocationetBundle:Foros')->findOneById($id);

		// Agregar a usuario creador del foro para notificacion del comentario
		$toList = Array();
        $usuarioForo = $foro->getUsuario()->getId();
        if ($usuarioId != $usuarioForo) {
				$toList[] = $usuarioForo;
			}
        

        if ($comentarioRespuestaId != 0) {
			$comentarioRespuesta = $em->getRepository('vocationetBundle:Comentarios')->findOneById($comentarioRespuestaId);

			// Agregar a usuario del comentario que estoy respondiendo para notificacion del comentario
			$usuarioCommentR = $comentarioRespuesta->getUsuario()->getId();
			if ($usuarioId != $usuarioCommentR) {
				$toList[] = $usuarioCommentR;
			}
		} else {
			$comentarioRespuesta = null;
		}

		$newComentario = new Comentarios();
		$newComentario->setTexto($comentarioTexto);
		$newComentario->setCreated($fechaCreacion);
		$newComentario->setModified($fechaCreacion);
		$newComentario->setEstado('');
		$newComentario->setUsuario($usuario);
		$newComentario->setForo($foro);
		$newComentario->setComentario($comentarioRespuesta);
		$em->persist($newComentario);
		$em->flush();
		
		$comentarioId = $newComentario->getId();

		if (count($toList)>0) {
			$subject = $this->get('translator')->trans("nuevo.comentario", Array(), 'label');
			$message = $usuarioName .' '. $this->get('translator')->trans("ha.escrito.un.comentario", Array(), 'label').'\nl"'.$comentarioTexto.'"';
			$mensaje_serv = $this->get('mensajes')->enviarMensaje($usuarioId, $toList, $subject, $message, null, null);
		}
        
		$renderComentario =  $this->renderView('vocationetBundle:Foros:comentarioShow.html.twig', array(
            'usuarioId' => $usuarioId,
            'imagen' => $usuarioImagen,
            'nombre' => $usuarioName,
            'content' => $comentarioTexto,
            'fechaCreacion' => $fechaCreacion,
            'respuesta' => $tipo,
            'comentarioId' => $comentarioId,
            'foroId'=> $id,
        ));

        if ($comentarioRespuesta) {
			$return = $renderComentario;
		}
		else {
			$renderRespuesta =  $this->renderView('vocationetBundle:Foros:comentarioNew.html.twig', array(
				'respuesta'=> 1,
				'foroId'=> $id,
				'comentarioId' => $comentarioId,
				'usuarioId' => $usuarioId,
				'imagen' => $usuarioImagen
			));

			$return = $renderComentario."
				<div id='divnew1".$id.$comentarioId."'></div><div STYLE='display:none;' id='commentresponseid".$comentarioId."' >
					".$renderRespuesta."
				</div>";
		}
		return new Response($return);
    }

    /**
	 * Crear un foro
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @Template("vocationetBundle:Foros:new.html.twig")
	 * @Route("new", name="crear_foro")
	 * @Method({"GET","POST"})
	 * @param Request Form de nuevo foro
	 * @return Render
	 */
	 public function newAction(Request $request)
	{
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

        $usuarioId = $security->getSessionValue('id');
 
		$form = $this->formForo();
		if ($request->getMethod() == 'POST')
		{
			$form->bind($request);
			if ($form->isvalid())
			{
				$dataForm = $form->getData();
				$errores = $this->validateFormForo($dataForm);

				if ($errores == 0)
				{
					$em = $this->getDoctrine()->getManager();
					$ObjTema = $em->getRepository('vocationetBundle:Temas')->findOneById($dataForm['temaId']);
					$ObjUsuario = $em->getRepository('vocationetBundle:Usuarios')->findOneById($usuarioId);
					$fechaCreacion = new \DateTime();
					
					$newForo = new Foros();
					$newForo->setForoTitulo($dataForm['foroTitulo']);
					$newForo->setForoTexto($dataForm['foroTexto']);
					$newForo->setTema($ObjTema);
					$newForo->setUsuario($ObjUsuario);
					$newForo->setCreated($fechaCreacion);
					$newForo->setModified($fechaCreacion);
					$em->persist($newForo);
					$em->flush();

					$ObjCarrera = $ObjTema->getCarrera();
					$carreraId = $ObjCarrera->getId();
					$foroId = $newForo->getId();
					$temaId = $ObjTema->getId();

					$upload = $this->get('file')->upload($dataForm['attachment'], 'foros/');
                    $files_id = $upload['files_id'];
                    
					foreach($files_id as $idFile){
						$ObjArchivo = $em->getRepository('vocationetBundle:Archivos')->findOneById($idFile);
						$newFA = new ForosArchivos();
						$newFA->setForo($newForo);
						$newFA->setArchivo($ObjArchivo);
						$em->persist($newFA);
						$em->flush();
					}
                    
                    if($upload['errors'] > 0)
                    {
                        $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.cargar.archivos"), "text" => $this->get('translator')->trans("%n%.archivos.exceden.tamano.maximo", array('%n%' => $upload['errors'])))); 
                    }
					
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("foro.creado"), "text" => $this->get('translator')->trans("foro.creado.correctamente")));
					return $this->redirect($this->generateUrl('foros_temas', array('id'=> $carreraId, 'temaId' => $temaId, 'foroId' => $foroId)));
				}
			}
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
			
		}
		return array('form' => $form->createView());
	}

	/**
	 * Editar un foro
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @Template("vocationetBundle:Foros:edit.html.twig")
	 * @Route("{foroId}/edit", name="edit_foro")
	 * @Method({"GET","POST"})
	 * @param Request $request Form de edicion del foro
	 * @param Request $foroId Id del foro a editar
	 * @return Render
	 */
	public function editAction(Request $request, $foroId)
	{
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

        $em = $this->getDoctrine()->getManager();
        $usuarioId = $security->getSessionValue('id');
		$foro = $em->getRepository('vocationetBundle:Foros')->findOneBy(Array('id'=>$foroId));
		
        if (!$foro) {
			throw $this->createNotFoundException($this->get('translator')->trans("foro.no.existe"));
		}
		
		$usuarioCreador = $foro->getUsuario()->getId();
		$sess_permissions = $security->session->get('sess_permissions');
        if ((in_array('acceso_edit_delete_foro', $sess_permissions['permissions'])) || ($usuarioCreador === $usuarioId)){
			
			$form = $this->formForo($foro);
			$foroAdjuntos = $this->getAdjuntosForos($foroId);

			$PosActual = Array('carreraId' => $foro->getTema()->getCarrera()->getId(), 'temaId' => $foro->getTema()->getId());

			if ($request->getMethod() == "POST") {
				$form->bind($request);
				if ($form->isvalid())
				{
					$dataForm = $form->getData();
					$errores = $this->validateFormForo($dataForm);

					if ($errores == 0)
					{
						$ObjTema = $em->getRepository('vocationetBundle:Temas')->findOneById($dataForm['temaId']);

						$foro->setForoTitulo($dataForm['foroTitulo']);
						$foro->setForoTexto(stripslashes($dataForm['foroTexto']));
						$foro->setTema($ObjTema);
						$foro->setModified(new \DateTime());
						$em->persist($foro);
						$em->flush();

						$ObjCarrera = $ObjTema->getCarrera();
						$carreraId = $ObjCarrera->getId();
						$foroId = $foro->getId();
						$temaId = $ObjTema->getId();

						$this->eliminarAdjuntos($request->request->get('del_adj'), $foro);
						
						$upload = $this->get('file')->upload($dataForm['attachment'], 'foros/');
                        
                        $files_id = $upload['files_id'];
						foreach($files_id as $idFile){
							$ObjArchivo = $em->getRepository('vocationetBundle:Archivos')->findOneById($idFile);
							$newFA = new ForosArchivos();
							$newFA->setForo($foro);
							$newFA->setArchivo($ObjArchivo);
							$em->persist($newFA);
							$em->flush();
						}
						
                        if($upload['errors'] > 0)
                        {
                            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.cargar.archivos"), "text" => $this->get('translator')->trans("%n%.archivos.exceden.tamano.maximo", array('%n%' => $upload['errors'])))); 
                        }
                        
						$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("foro.editado"), "text" => $this->get('translator')->trans("foro.editado.correctamente")));
						return $this->redirect($this->generateUrl('foros_temas', array('id'=> $carreraId, 'temaId' => $temaId, 'foroId' => $foroId)));
					}
				}
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
			}
		}
		else {
			throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));
		}

        return array('form' => $form->createView(), 'foroId' => $foroId, 'foroAdjuntos' => $foroAdjuntos, 'actual' => $PosActual);
	}

	/**
     * Borrar un foro
     * 
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param \Symfony\Component\HttpFoundation\Request $request Form de eliminar foro
     * @param Int $foroId Id del foro a eliminar
     * @return Redirect 
     * @Route("{foroId}/delete", name="delete_foro")
     * @Method("DELETE")
     */
	public function deleteAction(Request $request, $foroId)
	{
		$form = $this->createDeleteForm($foroId);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
		$foro = $em->getRepository('vocationetBundle:Foros')->findOneBy(Array('id'=>$foroId));

		if (!$foro) {
			throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));
		}

		$temaId = $foro->getTema()->getId();
		$carreraId = $foro->getTema()->getCarrera()->getId();

        if ($form->isValid()) {
            $usuarioCreador = $foro->getUsuario()->getId();
            $security = $this->get('security');
            $usuarioId = $security->getSessionValue('id');
			$sess_permissions = $security->session->get('sess_permissions');
			if ((in_array('acceso_edit_delete_foro', $sess_permissions['permissions'])) || ($usuarioCreador === $usuarioId))
			{
				//Eliminar comentarios del foro
				$this->deleteComentariosForo($foroId);

				//Eliminar archivos adjuntos del foro
				$adjuntos = $this->getAdjuntosForos($foroId);
				if ($adjuntos) {
					$ConcatenacionIds = '';
					foreach ($adjuntos as $adj) {
						$ConcatenacionIds .= $adj['id'].',' ;
					}
					$this->eliminarAdjuntos($ConcatenacionIds, $foro);
				}

				$em->remove($foro);
				$em->flush();

				$foroId = 0;
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("foro.eliminado"), "text" => $this->get('translator')->trans("foro.eliminado.correctamente")));
			}
			else {
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("acceso.denegado"), "text" => $this->get('translator')->trans("no.tiene.permisos.para.realizar.esta.accion")));
			}
		}
		else
		{
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("acceso.invalido"), "text" => $this->get('translator')->trans("acceso.invalido")));
		}
		return $this->redirect($this->generateUrl('foros_temas', Array('id'=> $carreraId, 'temaId' => $temaId, 'foroId' => $foroId)));
	}


	// FUNCIONES Y METODOS

	/**
     * Creación de formulario para eliminar un foro
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $id Id del foro
     * @return \Symfony\Component\Form\Form Formulario de eliminacion
     */
	private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('delete_foro', Array('foroId' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'button', array('label' => $this->get('translator')->trans("delete.foro", Array(), 'label'), 'attr' => array ('class' => 'btn btn-primary btn-xs confirmdelete' )))
            ->getForm();
    }

	/**
	 * Funcion privada para eliminar comentarios de un foro
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Objeto $foroId Id del foro
	 */
    private function deleteComentariosForo($foroId)
    {
		$em = $this->getDoctrine()->getManager();
        $dql = "DELETE FROM vocationetBundle:Comentarios c
                WHERE c.foro =:foroId";
        $query = $em->createQuery($dql);
        $query->setParameter('foroId', $foroId);
        $query->getResult();
	}
	
	/*
     * Funcion para eliminar archivos adjuntos de un foro
     *
     * Funcion que elimina tanto fisicamente(en disco duro), como en base de datos los archivos del foro pasados por parametro
     * 
     * $delAdj = string con los id de los archivos que se eliminaran separados por comas
     * $foro = entidad del foro
     */
    function eliminarAdjuntos($delAdj, $foro)
    {
        $archivos= explode(',', $delAdj);
        unset($archivos[count($archivos)-1]);

        if(count($archivos)>0)
        {
            $em = $this->getDoctrine()->getManager();
            $dql_ids = array();
            $dqlII_ids = array();

            foreach($archivos as $archivoId)
            {
                $archivo = $em->getRepository('vocationetBundle:Archivos')->findOneById($archivoId);
                if ($archivo) {
					$this->get('file')->deleteFile($archivo->getArchivoPath());
					$dql_ids[] = 'fa.archivo = '.$archivoId;
					$dqlII_ids[] = 'a.id = '.$archivoId;
				}
            }

            if(count($dql_ids)>0)
            {
                $dql = "DELETE FROM vocationetBundle:ForosArchivos fa
                        WHERE fa.foro =:foro AND (". implode(' OR ', $dql_ids) .")";
                $query = $em->createQuery($dql);
                $query->setParameter('foro', $foro);
                $query->getResult();

                $dql ="DELETE FROM vocationetBundle:Archivos a
					   WHERE (". implode(' OR ', $dqlII_ids) .")";
				$query = $em->createQuery($dql);
				$query->getResult();
            }
        }
    }

	
	/**
	 * Estructura del formulario de creacion y edicion de foro
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Objeto $foro objeto del foro si es caso edicion
     * @return Formulario de creacion de foro
	 */
	private function formForo($foro = false)
	{
		$temas= $this->getCarrerasTemasARRAY();
		$foroTitulo = ($foro) ? $foro->getForoTitulo()  : null;
		$foroText = ($foro) ? $foro->getForoTexto()  : null;
		$temaAct = ($foro) ? $foro->getTema()->getId() : 1;
		
		$formData = Array('foroTitulo'=> $foroTitulo, 'foroTexto'=> $foroText, 'temaId' => null);
		$form = $this->createFormBuilder($formData)
		   ->add('foroTitulo', 'text', array('required' => true, 'attr' => Array('pattern' => '^[a-zA-Z0-9 áéíóúÁÉÍÓÚñÑ-]*$' )))
		   ->add('foroTexto', 'textarea', array('required' => true, 'attr' => Array('style' => 'resize:vertical;', 'rows' => 7) ))
		   ->add('temaId', 'choice', array('choices'  => $temas,  'preferred_choices' => array($temaAct), 'required' => true))
		   ->add('attachment', 'file', array('required' => false))
		   ->getForm();
		return $form;
	}

	/**
	 * validacion del formulario de creacion de foro
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Array $dataForm Datos del formulario
     * @return Int (Mayor a 0, no paso la validación)
	 */
	private function validateFormForo($dataForm)
	{
		$foroTitulo = $dataForm['foroTitulo'];
		$foroTexto = $dataForm['foroTexto'];
		$temaId = $dataForm['temaId'];

		$NotBlank = new Assert\NotBlank();
		$Regex = new Assert\Regex(Array('pattern'=>'/^[a-zA-Z0-9 áéíóúÁÉÍÓÚñÑ-]*$/'));

		$countErrores = 0;
		$countErrores += (count($this->get('validator')->validateValue($foroTitulo, Array($NotBlank, $Regex))) == 0) ? 0 : 1;
		//$countErrores += (count($this->get('validator')->validateValue($foroTexto, Array($NotBlank))) == 0) ? 0 : 1;
		$countErrores += (count($this->get('validator')->validateValue($temaId, Array($NotBlank))) == 0) ? 0 : 1;
		return $countErrores;
	}

	/**
     * Funcion para obtener la lista de adjuntos de un foro
     *
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $foroId id de foro
     * @return Array arreglo de adjuntos
     */
    private function getAdjuntosForos($foroId)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT a.id, a.archivoNombre
                FROM vocationetBundle:Archivos a
                JOIN vocationetBundle:ForosArchivos fa WITH a.id = fa.archivo
                WHERE fa.foro = :fid";
        $query = $em->createQuery($dql);
        $query->setParameter('fid', $foroId);
        $adjuntos = $query->getResult();
        return $adjuntos;
    }
    
	/**
	 * Comentario del foro que ingresa por parametro
	 *
	 * Esta funcion retorna los comentarios del foro que se ingresa por parametro organizados en dos niveles
	 * Comentarios principal, y dentro de este, comentarios respuesta (cr)
	 *
	 * @author Camilo Quijano
     * @version 1
	 * @param Int $foroId Id del foro
	 * @return Array Arreblo bidimensional
	 */
    private function getComentarios($foroId)
    {
		$em = $this->getDoctrine()->getManager();
		$dql = "SELECT c.id, c.texto, c.created, u.usuarioNombre, u.usuarioApellido, u.usuarioImagen, u.id AS usuarioId,
					cr.id AS commentRespuesta
				FROM vocationetBundle:Comentarios c
				LEFT JOIN c.comentario cr
				LEFT JOIN c.usuario u
				WHERE c.foro =:foroId";
		$query = $em->createQuery($dql);
		$query->setParameter('foroId', $foroId);
		$comentarios = $query->getResult();
		
		$Objcomentarios = Array();
        $auxKeys = Array();
		foreach($comentarios as $c)
		{
			$comentId = $c['id'];
			$comentRespo = $c['commentRespuesta'];
			if ($comentRespo)
			{
				$Objcomentarios[$comentRespo]['cr'][$comentId] = $c;
			}
			else
			{
				$Objcomentarios[$comentId] = $c;
			}
        }
        return $Objcomentarios;
	}

	/**
	 * Funcion que retorna un Array bidimencional para formulario de creacion en donde optgroup es el nombre de la carrera
	 * y las opcioner organizadas por carreras son los temas.
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @return Arreglo Arreglo bidimensional
	 */
	private function getCarrerasTemasARRAY()
	{
		/**
		 * @var String Consulta SQL de temas con su respectiva carrera
		 * SELECT * FROM temas t join carreras c ON c.id = t.carrera_id ORDER BY c.id;
		 */
		$em = $this->getDoctrine()->getManager();
		$dql = "SELECT t.id AS temaId, t.nombre AS temaNombre, c.id, c.nombre AS nombreCarrera
				FROM vocationetBundle:Temas t
				LEFT JOIN vocationetBundle:Carreras c WITH c.id = t.carrera
				ORDER BY c.id";
		$query = $em->createQuery($dql);
		$result = $query->getResult();

		$ArrayCarTem = Array();
		if ($result) {
			foreach ($result as $res) {
				$ArrayCarTem[$res['nombreCarrera']]["".$res['temaId'].""] = $res['temaNombre'];
			}
		}
		return $ArrayCarTem;
	}
}
?>
