<?php

namespace AT\vocationetBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\File\File;
//use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador de Administración por parte del mentor
 * @package vocationetBundle
 * @Route("/adminmentor")
 * @author Camilo Quijano <camilo@altactic.com>
 * @version 1
 */
class AdminMentorController extends Controller
{
    /**
     * Listado de usuarios que han seleccionado al usuario logeado como mentor de Orientacion Vocacional
     * 
     * @Route("/usuariosmentor", name="lista_usuarios_mentor")
     * @Template("vocationetBundle:AdminMentor:UsuariosMentorVocacional.html.twig")
     * @Method("GET")
     * @return Response
     */
    public function UsuariosMentoresVocacionalAction()
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$rolId = $security->getSessionValue('rolId');
		if ($rolId != 3) {	throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado", array(), 'label'));	}

		$usuarioId = $security->getSessionValue('id');
        $contactos = $this->getUsuariosMentor($usuarioId);
        return array('contactos' => $contactos);
    }

    /**
	 * Funcion Action para subir informe de mercado laboral por parte del mentor
	 * 
     * @Route("/upfile", name="subir_informe_mercado_laboral")
     * @Template("vocationetBundle:AdminMentor:UsuariosMentorVocacional.html.twig")
     * @Method("POST")
     * @param Request $request Request con información de usuario y archivo adjunto
     */
	public function upFileReportMercadoLaboral(Request $request)
	{
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

        $rolId = $security->getSessionValue('rolId');
		if ($rolId != 3) { throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado", array(), 'label')); }

        $error = false;
        $usuarioId = $request->request->get('usuario');
        $seleccionarMentor = $this->get('perfil')->confirmarMentorOrientacionVocacional($usuarioId);
			if ($seleccionarMentor) {
				if ($seleccionarMentor['id'] == $security->getSessionValue('id'))
				{
					$informe = $request->files->get('informeml');

					$ext = $informe->guessExtension();
					if ($ext == 'pdf') {
						$nameFile = 'user'.$usuarioId.'.pdf';
						$informe->move('uploads/vocationet/ml/', $nameFile);
						$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("informe.cargado"), "text" => $this->get('translator')->trans("informe.cargado.correctamente")));
					} else {
						$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("extension.invalida")));
						//Este archivo no es valido
					}

					// Notificacion a estudiante de que el mentor ha subido el informe
					$asunto = $this->get('translator')->trans('informe.mercado.laboral', Array(), 'mail');
					$message = $this->get('translator')->trans('el.mentor.acaba.de.subir.el.informe.de.mercado.laboral', Array(), 'mail');
					$this->get('mensajes')->enviarMensaje($seleccionarMentor['id'], Array($usuarioId), $asunto, $message);
				}
				else {
					$error = true;
				}
			} else {
				$error = true;
			}

		if ($error) {
			//VALIDACION SI EL USUARIO ES MENTOR DEL ESTUDIANTE
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("Acceso denegado"), "text" => $this->get('translator')->trans("Acceso denegado")));
		}
		
		return $this->redirect($this->generateUrl('lista_usuarios_mentor'));
	}

    /**
     * Listado de pruebas por usuario
     * 
     * @Route("/pruebasusuario/{id}", name="pruebas_usuarios_mentor")
     * @Template("vocationetBundle:AdminMentor:pruebasUsuario.html.twig")
     * @param type $id
     * @return Response
     */
    public function pruebasUsuarioAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
//        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository("vocationetBundle:Usuarios")->findOneById($id);
        
        return array(
            'usuario' => $usuario
        );
    }
    
    
	 /**
     * Listado de usuarios que han seleccionado al usuario logeado como mentor de Orientacion Vocacional
     *
     * Retorna array bidimencional en donde en primer nivel estan los usuarios y un subnivel por usuario estan las alternativas de estudio
     *
     * @param Int $usuarioId Id del usuario a consultarle las amistades
     * @return Array Arreglo bi-dimensional de usuarios
     */
    private function getUsuariosMentor($usuarioId)
    {
		$em = $this->getDoctrine()->getManager();
		/**
		 * @var String Consulta SQL que trae los usuarios que seleccionaron al usuario como mentor vocacional
		 * SELECT r.id, r.tipo, u.id, u.usuario_nombre, u.usuario_Apellido, rol.nombre, est.id
		 * FROM usuarios u
		 * JOIN roles rol ON u.rol_id = rol.id
		 * LEFT JOIN alternativas_estudios est ON u.id = est.usuario_id
		 * JOIN relaciones r ON r.usuario_id = u.id OR r.usuario2_id = u.id
		 * WHERE r.tipo = 2 AND  u.id != 7 AND (((r.usuario_id = 7) AND r.estado = 1));
		 */
        $dql = "SELECT u.id, u.usuarioNombre, u.usuarioApellido, u.usuarioImagen,
						rol.nombre AS rolNombre,
						u.usuarioProfesion,
						c.nombre AS nombreColegio, u.usuarioCursoActual,
						car.id AS carreraId, car.nombre AS nombreCarrera,
						r.estado, r.id AS relacionId
                FROM vocationetBundle:Usuarios u
                JOIN u.rol rol
                LEFT JOIN u.colegio c
                LEFT JOIN vocationetBundle:AlternativasEstudios est WITH u.id = est.usuario
                LEFT JOIN est.carrera car
                JOIN vocationetBundle:Relaciones r WITH r.usuario = u.id OR r.usuario2 = u.id
                WHERE r.tipo = 2 AND u.id !=:usuarioId AND r.usuario =:usuarioId AND  r.estado = 1
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
                    if ($cont['nombreCarrera']) {
                        $arrContactos[$cont['id']]['estudios'][] = Array(
                            'nombreCarrera' => $cont['nombreCarrera']);
                    }
					$auxId = $cont['id'];
				} else {
					$arrContactos[$cont['id']]['estudios'][] = Array(
						'nombreCarrera' => $cont['nombreCarrera']);
				}
			}
		}
		return $arrContactos;
	}
}
?>
