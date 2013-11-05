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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * controlador de foros
 * @package vocationetBundle
 * @Route("/foros/")
 */
class ForosController extends Controller
{
	/**
	 *
	 * @Route("new", name="crear_foro")
	 * @Template("vocationetBundle:Foros:new.html.twig")
	 */
	 public function newAction(Request $request)
	 {
		$security = $this->get('security');
		if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        //if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

        $usuarioId = $security->getSessionValue('id');
 
		$carreraId = 1;
		$form = $this->formForo($carreraId);
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
					
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("foro.creado"), "text" => $this->get('translator')->trans("foro.creado.correctamente")));
					return $this->redirect($this->generateUrl('foros_temas', array('id'=> $carreraId, 'temaId' => $temaId, 'foroId' => $foroId)));
				}
			}
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
			
		}
		return array('form' => $form->createView());
	 }

	private function formForo($carreraId)
	{
		$temaAct = 1;
		$temas= $this->getCarrerasTemasARRAY();
		
		$formData = Array('foroTitulo'=> null, 'foroTexto'=> null, 'temaId' => null);
		$form = $this->createFormBuilder($formData)
		   ->add('foroTitulo', 'text', array('required' => true, 'attr' => Array('pattern' => '^[a-zA-Z0-9 áéíóúÁÉÍÓÚñÑ-]*$' )))
		   ->add('foroTexto', 'textarea', array('required' => true, 'attr' => Array('style' => 'resize:vertical;', 'rows' => 7) ))
		   ->add('temaId', 'choice', array('choices'  => $temas,  'preferred_choices' => array($temaAct), 'required' => true))
		   ->getForm();
		return $form;
	}

	private function validateFormForo($dataForm)
	{
		$foroTitulo = $dataForm['foroTitulo'];
		$foroTexto = $dataForm['foroTexto'];
		$temaId = $dataForm['temaId'];

		$NotBlank = new Assert\NotBlank();
		$Regex = new Assert\Regex(Array('pattern'=>'/^[a-zA-Z0-9 áéíóúÁÉÍÓÚñÑ-]*$/'));

		$countErrores = 0;
		$countErrores += (count($this->get('validator')->validateValue($foroTitulo, Array($NotBlank, $Regex))) == 0) ? 0 : 1;
		$countErrores += (count($this->get('validator')->validateValue($foroTexto, Array($NotBlank))) == 0) ? 0 : 1;
		$countErrores += (count($this->get('validator')->validateValue($temaId, Array($NotBlank))) == 0) ? 0 : 1;
		return $countErrores;
	}
	
    /**
     * Listado de foros y temas de la carrera que ingresa por parametro
     *
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @Route("c_{id}/t_{temaId}/f_{foroId}", name="foros_temas", defaults={"temaId" = 0, "foroId" = 0})
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
        //if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$em = $this->getDoctrine()->getManager();
        $carreras = $em->getRepository('vocationetBundle:Carreras')->findAll();
        $temas = $this->getTemasCountForos($id);
        $PosActual = Array('carreraId' => $id, 'temaId' => $temaId);

        if ($foroId)
        {
			// If hay foro seleccionado redireccionara el show del foro
			$foro = $em->getRepository('vocationetBundle:Foros')->findOneById($foroId);
			$comentarios = $this->getComentarios($foroId);
			return $this->render('vocationetBundle:Foros:showForo.html.twig', array(
				'carreras' => $carreras, 'temas' => $temas,	'actual' => $PosActual, 'foro' => $foro, 'comentarios' => $comentarios));
		}

		else
		{
			// If no hay foro seleccionado, enlista los foros de la carrera y/o tema seleccionado
			$foros = $this->ForosCarrerasTemas($id, $temaId);
			return $this->render('vocationetBundle:Foros:index.html.twig', array(
					'carreras' => $carreras, 'temas' => $temas,	'actual' => $PosActual,	'foros' => $foros ));
		}
        
        return array(
			'carreras' => $carreras,
			'temas' => $temas,
			'actual' => $PosActual,
			'foros' => $foros);
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

        if ($comentarioRespuestaId != 0) {
			$comentarioRespuesta = $em->getRepository('vocationetBundle:Comentarios')->findOneById($comentarioRespuestaId);
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
    function getComentarios($foroId)
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
	 * Funcion que trae los foros de la carrera y/o tema filtrados
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $carreraId Id de la carrera
     * @param Int $temaId Id del tema por el que se quiere filtrar (valor default = 0)
     * @return Array Arreglo con foros que estan incluidos en el filtro
	 */
	private function ForosCarrerasTemas($carreraId, $temaId)
	{
		$auxWhere = '';
		if ($temaId) {
			$auxWhere = ' AND t.id ='.$temaId;
		}
		$em = $this->getDoctrine()->getManager();
		/**
		 * SELECT * FROM foros f
		 * INNER JOIN temas t ON t.id = f.tema_id
		 * INNER JOIN carreras c ON c.id = t.carrera_id
		 * LEFT JOIN comentarios com ON com.foro_id = f.id;;
		 */
		$dql = "SELECT f.id, f.foroTitulo, f.foroTexto, f.created, t.nombre AS temaNombre, COUNT(comment.id) AS countComent
				FROM vocationetBundle:Foros f
				JOIN f.tema t
				JOIN t.carrera c
				LEFT JOIN vocationetBundle:Comentarios comment WITH comment.foro = f.id
				WHERE c.id =:carreraId".$auxWhere."
				GROUP BY f.id";
		$query = $em->createQuery($dql);
		$query->setParameter('carreraId', $carreraId);
		return $query->getResult();
	}

	/**
	 * Función que retorna el listado de temas de una carrera y cantidad de foros por tema
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $carreraId Id de la carrera
     * @return Array Arreglo con datos del tema, y cantidad de foros
	 */
    private function getTemasCountForos($carreraId)
    {
		$em = $this->getDoctrine()->getManager();
		$dql = "SELECT t.id, t.nombre, COUNT(f.id) as cantidadForos
				FROM vocationetBundle:Temas t
				LEFT JOIN vocationetBundle:Foros f WITH f.tema = t.id
				WHERE t.carrera =:carreraId
				GROUP BY t.id";
		$query = $em->createQuery($dql);
		$query->setParameter('carreraId', $carreraId);
		return $query->getResult();
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
