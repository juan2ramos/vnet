<?php

namespace AT\vocationetBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador de contactos y busqueda de usuarios y/o mentores
 * @package vocationetBundle
 * @Route("/contactos")
 */
class ContactosController extends Controller
{
    /**
     * PENDIENTE DOCUMENTACION
     * PENDIENTE DOCUMENTACION
     * 
     * @Route("/", name="contactos")
     * @Template("vocationetBundle:Contactos:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        //if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$usuarioId = $security->getSessionValue('id');
        $contactos = $this->getAmistades($usuarioId);
        //print_r($contactos);
        //$contactos = Array();
        return array('contactos' => $contactos);
    }

	/**
     * PENDIENTE DOCUMENTACION AJAX POST
     * PENDIENTE DOCUMENTACION
     * 
     * @Route("/estado", name="edit_estado_relacion")
     * @return Response
     */
    public function EditEstadoRelacionAction(Request $request)
    {
		$security = $this->get('security');
		$usuarioId = $security->getSessionValue('id');
		
		$usuario = $request->request->get('u');
		$relacionId = $request->request->get('r');
		$tipo = $request->request->get('t'); //Tipo r=>Rechazar c=>Cancelar a=>Aprobar

		$em = $this->getDoctrine()->getManager();

		$estadoReturn = 'error';
		if ($tipo == 'r') {
			$relacion = $em->getRepository('vocationetBundle:Relaciones')->findOneBy(Array('id' => $relacionId, 'usuario' => $usuario, 'usuario2' => $usuarioId, 'tipo' => 1, 'estado' => 0));
			$estadoReturn = ($relacion) ? 'delete' : 'error';
			if ($relacion) { $em->remove($relacion); }
		} elseif ($tipo == 'c') {
			$relacion = $em->getRepository('vocationetBundle:Relaciones')->findOneBy(Array('id' => $relacionId, 'tipo' => 1, 'estado' => 1));
			$estadoReturn = ($relacion) ? 'delete' : 'error';
			if ($relacion) { $em->remove($relacion); }
		} elseif ($tipo == 'a') {
			$relacion = $em->getRepository('vocationetBundle:Relaciones')->findOneBy(Array('id' => $relacionId, 'usuario' => $usuario, 'usuario2' => $usuarioId, 'tipo' => 1, 'estado' => 0));
			$estadoReturn = ($relacion) ? 'cancelar' : 'error';
			if ($relacion) {
				$relacion->setEstado(1);
				$em->persist($relacion);
			}
		}
		
		$em->flush();
		$renderBtn =  $this->renderView('vocationetBundle:Contactos:ajaxBtn.html.twig', array('id'=> $usuario, 'relacionId' => $relacionId, 'estadoReturn' =>$estadoReturn));
		return new response($renderBtn);
	}

    /**
     * PENDIENTE DOCUMENTACION
     */
     private function getAmistades($usuarioId)
    {
		$em = $this->getDoctrine()->getManager();
		/**
		 * @var String Consulta SQL que trae las relaciones de amistad del usuario, y las que tiene pendientes por aprobar
		 * SELECT u.id, u.usuario_nombre, u.usuario_Apellido, rol.nombre
		 * FROM usuarios u
		 * JOIN roles rol ON u.rol_id = rol.id
		 * LEFT JOIN estudios est ON u.id = est.usuario_id
		 * JOIN relaciones r ON r.usuario_id = u.id OR r.usuario2_id = u.id
		 * WHERE r.tipo = 1 AND  u.id != 7
		 * 	AND (((r.usuario_id = 7 OR r.usuario2_id = 7) AND r.estado = 1)  OR  (r.usuario2_id = 7 and r.estado = 0));
		 */
        $dql = "SELECT u.id, u.usuarioNombre, u.usuarioApellido, u.usuarioImagen,
						rol.nombre AS rolNombre,
						u.usuarioProfesion,
						c.nombre AS nombreColegio, u.usuarioCursoActual,
						est.nombreInstitucion, est.titulo,
						r.estado, r.id AS relacionId
                FROM vocationetBundle:Usuarios u
                JOIN u.rol rol
                LEFT JOIN u.colegio c
                LEFT JOIN vocationetBundle:Estudios est WITH u.id = est.usuario
                JOIN vocationetBundle:Relaciones r WITH r.usuario = u.id OR r.usuario2 = u.id
                WHERE r.tipo = 1 AND u.id !=:usuarioId
					AND ((r.usuario2 =:usuarioId AND r.estado = 0)  OR
                    ((r.usuario2 =:usuarioId OR r.usuario =:usuarioId) AND r.estado = 1))
                ORDER BY r.estado, u.id";
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $contactos = $query->getResult();
        
		$arrContactos = Array();
        if ($contactos) {
			$auxId = 0;
			foreach ($contactos as $cont)
			{
				if ($auxId != $cont['id']) {
					$arrContactos[$cont['id']] = Array(
						'id' => $cont['id'],
						'usuarioImagen' => $cont['usuarioImagen'],
						'usuarioNombre' => $cont['usuarioNombre'],
						'usuarioApellido' => $cont['usuarioApellido'],
						'rolNombre' => $cont['rolNombre'],
						'usuarioProfesion' => $cont['usuarioProfesion'],
						'nombreColegio' => $cont['nombreColegio'],
						'usuarioCursoActual' => $cont['usuarioCursoActual'],
						'estado' => $cont['estado'],
						'relacionId' => $cont['relacionId']);
					$auxId = $cont['id'];
				} else {
					$arrContactos[$cont['id']]['estudios'][] = Array(
						'nombreInstitucion' => $cont['nombreInstitucion'],
						'titulo' => $cont['titulo']);
				}
			}
		}

		//return $contactos;
		return $arrContactos;
	}
}
?>
