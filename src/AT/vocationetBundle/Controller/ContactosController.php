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
     * Lista de amistades y solicitudes.
     *
     * Listado de amistades, y solicitudes que tiene el usuario
     * y acceso a eliminar amistades y/o aprobarlas
     * 
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @Route("/", name="contactos")
     * @Template("vocationetBundle:Contactos:index.html.twig")
     * @Method("GET")
     * @return Render Listado de contactos
     */
    public function indexAction()
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        //if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$usuarioId = $security->getSessionValue('id');
        $contactos = $this->getAmistades($usuarioId);
        return array('contactos' => $contactos);
    }

	/**
	 * @Route("/buscar", name="busqueda")
	 * @Template("vocationetBundle:Contactos:busqueda.html.twig")
	 */
    public function busquedaAction()
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        //if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$usuarioId = $security->getSessionValue('id');
        $contactos = $this->getAmistades($usuarioId);

        $form = $this->formBusqueda();

        
        return array('contactos' => $contactos, 'form' => $form->createView());
	}

	/**
     * Peticion de amistad, solicitud, aprobacion, rechazar, y eliminar relacion entre usuarios por AJAX
     *
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @Route("/estado", name="edit_estado_relacion")
     * @Method("POST")
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
     * Listado de amistades del usuario
     *
     * Retorna array bidimencional en donde en primer nivel estan los usuarios y un subnivel por usuario estan los estudios
     *
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $usuarioId Id del usuario a consultarle las amistades
     * @return Array Arreglo bi-dimensional de usuarios
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
		 * 	AND (((r.usuario_id = 7 OR r.usuario2_id = 7) AND r.estado = 1)  OR  (r.usuario2_id = 7 and r.estado = 0))
		 * ORDER BY r.estado, u.id;
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


	private function getBusquedaDetallada($busqueda)
	{
		/**
		 *
		 * SELECT u.id, est.id, r.*, SUM(CASE WHEN (r.usuario_id = 7 OR r.usuario2_id = 7) THEN 1 ELSE 0 END) AS relacionExistente 
FROM usuarios u
LEFT JOIN relaciones r ON (r.usuario_id = u.id OR r.usuario2_id = u.id) AND r.tipo = 1
LEFT JOIN estudios est ON u.id = est.usuario_id
WHERE u.id != 7
GROUP BY u.id, est.id;
;
*/
	}

	private function formBusqueda()
	{
		$pr = $this->get('perfil');
		// Grados
		//$gradoAct = ($perfil['usuarioCursoActual']) ? $perfil['usuarioCursoActual'] : '';
		//$empty_value_grado = (!$gradoAct) ? $tr->trans("seleccione.un.grado", array(), 'label') : false;
		
		// TipoUsuario ROL
		$rolAct = '';
		$roles = $pr->getRolesBusqueda();

		$colAct = '';
		$colegios = $pr->getColegios();

		$formData = Array('nombre'=> '');
		return $form = $this->createFormBuilder($formData)
			->add('tipoUsuario', 'choice', array('choices'  => $roles,  'preferred_choices' => array($rolAct), 'required' => true))
			->add('colegio', 'choice', array('choices'  => $colegios,  'preferred_choices' => array($colAct), 'required' => true))
			// Si es usuario Estudiante
			//->add('colegio', 'text', array('required' => false))
			->add('universidad', 'text', array('required' => false))
			//Si es usuario Mentor
			->add('profesion', 'text', array('required' => false))
			->add('alternativaEstudio', 'text', array('required' => false))
			->getForm();
	}
		
}
?>
