<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AT\vocationetBundle\Entity\Comentarios;

/**
 * controlador de foros
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
     * @Route("c_{id}/t_{temaId}/f_{foroId}", name="foros_temas", defaults={"temaId" = 0, "foroId" = 0})
     * @Template("vocationetBundle:Foros:index.html.twig")
     * @Method("GET")
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
			//$security->debug($comentarios);
			

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
		
        //$carreras =$this->getCarrerasCountTemas();

        
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
     * envia $request type, p->comentario de publicacion, d->comentario de archivo, rc->respuesta comentario
     * envia $request id, variable dependiente a el tipo p->id de publicacion, d->id del archivo, rc->id de comentario
     * envia $request content, variable con contenido del comentario
     * 
     * @author Diego Malagon
     * @author Camilo Quijano
     * @version 2
     * @Method("POST")
     * @Route("/comentar", name="crear_comentario")
     * @param Request $request Request enviado desde ajax, con el id, tipo, y contenido del comentario
     * @return object Vista renderizada con el comentario creado
     */
    public function comentarAction(Request $request)
    {
		$security = $this->get('security');
		$usuarioId = $security->getSessionValue('id');
		$usuarioImagen = $security->getSessionValue('usuarioImagen');
		$usuarioName = $security->getSessionValue('usuarioNombre')." ".$security->getSessionValue('usuarioApellido');
		//$ok = 0;
		//echo 'oktest';
		//return $ok;

		$id = $request->get('idforo');
        $comentarioTexto = $request->get('comentario');
        $tipo = $request->get('tipo'); // O-> Comentario, 1->Respuesta Comentario
        $fechaCreacion = new \DateTime();

        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('vocationetBundle:Usuarios')->findOneById($usuarioId);
        $foro = $em->getRepository('vocationetBundle:Foros')->findOneById($id);

        //if ($tipo == 0) {
			$newComentario = new Comentarios();
			$newComentario->setTexto($comentarioTexto);
			$newComentario->setCreated($fechaCreacion);
			$newComentario->setModified($fechaCreacion);
			$newComentario->setEstado('');
			$newComentario->setUsuario($usuario);
			$newComentario->setForo($foro);
			$em->persist($newComentario);
			$em->flush();
			
			$comentarioId = $newComentario->getId();
			//Notificaciones
		//}

		

		/*$javascript = "<script>$('.commentresponse').on('click', function(e){
				comentId = $(this).data('comentid');
				$('#commentresponseid'+comentId).css('display','inline');
			});</script>";
	*/
	$javascript='';
		

        
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

        $renderRespuesta =  $this->renderView('vocationetBundle:Foros:comentarioNew.html.twig', array(
			'respuesta'=> 1,
			'foroId'=> $id,
			'comentarioId' => $comentarioId,
			'usuarioId' => $usuarioId,
			'imagen' => $usuarioImagen
		));

        $return = $renderComentario."<div id='divnew1".$id.$comentarioId."'></div><div STYLE='display:none;' id='commentresponseid".$comentarioId."' >
						".$renderRespuesta."
					</div>".$javascript;
		//$return = $renderComentario;
		//return $return;
		return new Response($return);
		
		//return 10;
		/**  $security = $this->get('security');
        if(!$security->autentication()){ return $this->redirect($this->generateUrl('cuenta_registro'));}
        if(!$security->autorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        $this->getRequest()->setLocale($this->get('config')->getConfigValue('USU_idioma'));
        
        if($request->getMethod() == 'POST')
        {
            $usuarioId = $security->getSessionValue('cta_usuario_id');
            $usuarioNombre = $security->getSessionValue('cta_usuario_nombre');
            $usuarioApellido = $security->getSessionValue('cta_usuario_apellido');
            $usuarioThbPerfil = $security->getSessionValue('cta_usuario_thb_perfil');

            $id = $request->get('id');
            $comentarioTexto = $request->get('content');
            $tipo = $request->get('type');

            $em = $this->getDoctrine()->getManager();
            $usuario = $em->getRepository('CuentaBundle:CtaUsuarios')->findOneById($usuarioId);
            
            $newComentario = new IntComentarios();
            $newComentario->setComentarioTexto($comentarioTexto);
            $newComentario->setComentarioFecha(new \DateTime());
            $newComentario->setUsuario($usuario);
            
            if($tipo == "p"){
                $publicacion = $em->getRepository('CuentaBundle:IntPublicaciones')->findOneById($id);
                $newComentario->setPublicacion($publicacion);
            }
            else if($tipo == "d"){
                $archivo = $em->getRepository('CuentaBundle:DocArchivos')->findOneById($id);
                $newComentario->setArchivo($archivo);
            }
            else if ($tipo == "rc"){
                $comentario = $em->getRepository('CuentaBundle:IntComentarios')->findOneById($id);
                $newComentario->setComentario($comentario);
                if ($comentario){
                    $pub = $comentario->getPublicacion();
                    if ($pub){
                        $newComentario->setPublicacion($pub);
                    }
                    $arc = $comentario->getArchivo();
                    if ($arc){
                        $newComentario->setArchivo($arc);
                    }
                }
            }
            
            $em->persist($newComentario);
            $em->flush();

            $this->get('puntos')->actualizarpuntos('PTOS_crear_comentario', $security);
            
            if($tipo == "p"){
                $this->get('publicaciones')->notificarMencionesComentarioPublicacion($publicacion, $newComentario, $usuarioNombre.' '.$usuarioApellido, $usuarioId);
            }
            else if($tipo == "d"){
                $this->get('publicaciones')->notificarMencionesComentarioArchivo($archivo, $newComentario, $usuarioNombre.' '.$usuarioApellido, $usuarioId);
            }
            else if($tipo == "rc"){
                if ($pub){  $this->get('publicaciones')->notificarMencionesComentarioPublicacion($pub, $newComentario, $usuarioNombre.' '.$usuarioApellido, $usuarioId); }
                if ($arc){  $this->get('publicaciones')->notificarMencionesComentarioArchivo($arc, $newComentario, $usuarioNombre.' '.$usuarioApellido, $usuarioId); }
            }
        }
        
        return $this->render('CuentaBundle:IntComentarios:show_ajax.html.twig', array(
            'usuarioId' => $usuarioId,
            'usuarioNombre' => $usuarioNombre,
            'usuarioApellido' => $usuarioApellido,
            'usuarioThbPerfil' => $usuarioThbPerfil,
            'comentario' => $newComentario,
            'tipo' => $tipo,
            'id' => $id,
        ));
        * */
    }
    

    function getComentarios($foroId)
    {
		$em = $this->getDoctrine()->getManager();
		//SELECT * FROM comentarios WHERE foro_id = 1;
		//$comentarios = $em->getRepository('vocationetBundle:Comentarios')->findBy(Array('foro' =>$foroId));
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
				//$comentRespoId = $comentRespo->getId();
				$Objcomentarios[$comentRespo]['cr'][$comentId] = $c;
				
			//if (!in_Array($c, $auxKeys)) {
				
			}
			else
			{
				$Objcomentarios[$comentId] = $c;
			}
			
                /*$key = $c['id'];
                if (!in_Array($c['comentarioRespuesta'], $auxKeys)){
                    $comentarios[$key] = $c;
                    $auxKeys[] = $key; 
                }
                else{
                    $comentarios[$c['comentarioRespuesta']]['ComentRespuesta'][$key] = $c;
                }*/
        }
        return $Objcomentarios;
	}
    


	/**
	 * Ver detalles del foro, idea principal y comentarios
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @Route("foro_{foroId}", name="foros_show")
	 * @Template("vocationetBundle:Foros:showForo.html.twig")
	 *
	 */
	public function showForoAction($foroId)
	{
		$em = $this->getDoctrine()->getManager();
        $carreras = $em->getRepository('vocationetBundle:Carreras')->findAll();
		return array();
	}
	
	/**
	 * Función que retorna el listado de temas de una carrera y cantidad de foros por tema
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
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
		$dql = "SELECT f.id, f.titulo, f.created, t.nombre AS temaNombre, COUNT(comment.id) AS countComent
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
}
?>
