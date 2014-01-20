<?php

namespace AT\vocationetBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AT\vocationetBundle\Entity\Relaciones;
use AT\vocationetBundle\Entity\Participaciones;

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
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$usuarioId = $security->getSessionValue('id');
        $contactos = $this->getAmistades($usuarioId);
        return array('contactos' => $contactos);
    }

	/**
     * Action con formulario de busqueda detallada
     * 
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @Route("/buscar", name="busqueda")
	 * @Template("vocationetBundle:Contactos:busqueda.html.twig")
     * @Method({"GET", "POST"})
     * @param Request $request Request enviado con busqueda avanzada
     * @return Render vista renderizada con filtro aplicado
	 */
    public function busquedaAction(Request $request)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$usuarioId = $security->getSessionValue('id');
        
        $pr = $this->get('perfil');
        //$autoCompletarColegios = $pr->getColegios();
        //$autoCompletarTitulos = $pr->getTitulos();
        //$autoCompletarUniversidades = $pr->getUniversidades();
		$autoCompletarColegios = Array();
		$autoCompletarTitulos = Array();
		$autoCompletarUniversidades = Array();
        
        $formData = Array('tipoUsuario' => 1, 'colegio'=>null, 'universidad'=>null, 'profesion'=>null, 'alternativaEstudio'=>null);
        $form = $this->formBusqueda($formData);
        
        if ($request->getMethod() == "POST") {
            
            $form->bind($request);
			if ($form->isvalid())
            {
                $formData = $form->getData();
                //$formData['tipoUsuario'] = 3;
                $contactos = $this->getBusquedaDetallada($usuarioId, $formData);
            } else {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
            }
        }
        else {
            $contactos = $this->getBusquedaDetallada($usuarioId, $formData);
        }

        return array(
            'contactos' => $contactos, 
            'form' => $form->createView(),
            'acColegios' => $autoCompletarColegios,
            'acTitulo' => $autoCompletarTitulos,
            'acUnivers' => $autoCompletarUniversidades,
            'formDT' => $formData
        );
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
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
		$usuarioId = $security->getSessionValue('id');
		
		$usuario = $request->request->get('u'); // Usuario al que sele va a hace la solicitud
		$relacionId = $request->request->get('r'); // Id de la relacion
		$tipo = $request->request->get('t'); //Tipo r=>Rechazar c=>Cancelar a=>Aprobar sa=>solicitud de amistad

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
				
				// Notificacion de solicitud de amistad
				$asunto = $this->get('translator')->trans('solicitud.de.amistad.aprobada', Array(), 'mail');
				$usuarioName = $relacion->getUsuario()->getUsuarioApellido()." ".$relacion->getUsuario()->getUsuarioNombre();
				$message = $this->get('translator')->trans('%nombre%.acepto.la.solicitud.de.amistad', Array('%nombre%'=> $usuarioName), 'mail');
				$this->get('mensajes')->enviarMensaje($usuarioId, Array($usuario), $asunto, $message);
			}
		} elseif ($tipo == 'sa') {
            $usuario1 = $em->getRepository('vocationetBundle:Usuarios')->findOneBy(Array('id' => $usuario));
            $usuario2 = $em->getRepository('vocationetBundle:Usuarios')->findOneBy(Array('id' => $usuarioId));
            if ($usuario1 && $usuario2) {
                $newR = new Relaciones();
                $newR->setUsuario($usuario2);
                $newR->setUsuario2($usuario1);
                $newR->setTipo(1);
                $newR->setCreated(new \DateTime());
                $newR->setEstado(0);
                $em->persist($newR);
				
				// Notificacion de solicitud de amistad
				$asunto = $this->get('translator')->trans('nueva.solicitud.de.amistad', Array(), 'mail');
				$usuarioName = $usuario1->getUsuarioApellido()." ".$usuario1->getUsuarioNombre();
				$message = $this->get('translator')->trans('%nombre%.te.ha.envia.una.solicitud.de.amistad', Array('%nombre%'=> $usuarioName), 'mail');
				$this->get('mensajes')->enviarMensaje($usuarioId, Array($usuario), $asunto, $message);
            }
            $estadoReturn = ($usuario1 && $usuario2) ? 'nuevaAmistad' : 'error'; 
        }
		
		$em->flush();
		$renderBtn =  $this->renderView('vocationetBundle:Contactos:ajaxBtn.html.twig', array('id'=> $usuario, 'relacionId' => $relacionId, 'estadoReturn' =>$estadoReturn));
		return new response($renderBtn);
	}

    /**
     * Seleccion de mentor de orientación vocacional con formulario de busqueda detallada
     * - Acceso desde TestVocacionalController
     * 
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @Route("/seleccionar_mentor", name="lista_mentores_ov")
	 * @Template("vocationetBundle:Contactos:MentoresVocacional.html.twig")
     * @Method({"GET", "POST"})
     * @param Request $request Request enviado con busqueda avanzada
     * @return Render vista renderizada con filtro aplicado
	 */
    public function MentoresVocacionalAction(Request $request)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$usuarioId = $security->getSessionValue('id');
        
        $pr = $this->get('perfil');
        //$autoCompletarTitulos = $pr->getTitulos();
        //$autoCompletarUniversidades = $pr->getUniversidades();
        $autoCompletarTitulos = false;
        $autoCompletarUniversidades = false;
        
        $formData = Array('tipoUsuario' => 3, 'universidad'=>null, 'profesion'=>null);
        $form = $this->formBusqueda($formData, true);

        $seleccionarMentor = $pr->confirmarMentorOrientacionVocacional($usuarioId);

        if (!$seleccionarMentor) {
			if ($request->getMethod() == "POST") {
				$form->bind($request);
				if ($form->isvalid())
				{
					$formData = $form->getData();
					$contactos = $this->getBusquedaDetallada($usuarioId, $formData);
				} else {
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("ingreso.invalido")));
					$contactos = false;
				}
			}
			else {
				$contactos = $this->getBusquedaDetallada($usuarioId, $formData);
			}
		} else {
			//$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("elegir.mentor"), "text" => $this->get('translator')->trans("mensaje.agendar.mentoria")));
			//return $this->redirect($this->generateUrl('agenda_estudiante'));
			$contactos = $this->getBusquedaDetallada($usuarioId, $formData, $seleccionarMentor['id']);
		}
		
		$formulario = $this->get('formularios')->getInfoFormulario(7);

        return array(
			'formulario_info' => $formulario,
            'contactos' => $contactos, 
            'form' => $form->createView(),
            'acTitulo' => $autoCompletarTitulos,
            'acUnivers' => $autoCompletarUniversidades,
            'formDT' => $formData,
            'selectMentor' => $seleccionarMentor,
        );
	}
	
	/**
     * Seleccion del mentor de orientación vocacional - POST
     *
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @Route("/select_mentor", name="seleccionar_mentor")
     * @Method("POST")
     * @param Request $request Request enviado con ID de mentor seleccionado
     * @return Redirect
     */
    public function seleccionarMentorOVAction(Request $request)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
		$pr = $this->get('perfil');

		if($request->getMethod() == 'POST') 
		{
			$usuarioId = $security->getSessionValue('id');
			
			$seleccionarMentor = $pr->confirmarMentorOrientacionVocacional($usuarioId);
			if (!$seleccionarMentor) {
				$mentorOVId = $request->request->get('mentorOV');
				$em = $this->getDoctrine()->getManager();
				$usuarioMentor = $em->getRepository('vocationetBundle:Usuarios')->findOneById($mentorOVId);
				if ($usuarioMentor) {
					if($usuarioMentor->getRol()->getId() == 3 && $usuarioMentor->getUsuarioRolEstado() == 2) {

						// Relacion creada entre usuario y mentor
						$user = $em->getRepository('vocationetBundle:Usuarios')->findOneBy(Array('id' => $usuarioId));
						$newR = new Relaciones();
						$newR->setUsuario($usuarioMentor);
						$newR->setUsuario2($user);
						$newR->setTipo(2);
						$newR->setCreated(new \DateTime());
						$newR->setEstado(1);
						$em->persist($newR);
						$em->flush();
				
						$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("elegir.mentor"), "text" => $this->get('translator')->trans("ha.seleccionado.mentor.contactese.con.el")));
						return $this->redirect($this->generateUrl('agenda_estudiante'));
						
					} else {
						// El usuario seleccionado no es Orientador Vocacional o no ha sido aprobado
						$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.elegir.mentor"), "text" => $this->get('translator')->trans("error.en.seleccion.de.mentor")));
					}
				} else {
					//El mentor seleccionado no existe
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.elegir.mentor"), "text" => $this->get('translator')->trans("error.en.seleccion.de.mentor")));
				}
			} else {
				//El usuario ya habia seleccionado un mentor con anterioridad
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.elegir.mentor"), "text" => $this->get('translator')->trans("usuario.ya.eligio.mentor")));
				return $this->redirect($this->generateUrl('agenda_estudiante'));
			}
		}

		return $this->redirect($this->generateUrl('lista_mentores_ov'));
	}
	
	/**
     * Seleccion de mentor expertos con formulario de busqueda detallada
     * 
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @Route("/red_mentores", name="red_mentores")
	 * @Template("vocationetBundle:Contactos:RedMentores.html.twig")
     * @Method({"GET", "POST"})
     * @param Request $request Request enviado con busqueda avanzada
     * @return Render vista renderizada con filtro aplicado
	 */
    public function RedMentoresAction(Request $request)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        //if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$usuarioId = $security->getSessionValue('id');
        
        $pr = $this->get('perfil');
        //$autoCompletarTitulos = $pr->getTitulos();
        //$autoCompletarUniversidades = $pr->getUniversidades();
        $autoCompletarTitulos = false;
        $autoCompletarUniversidades = false;
        
        $formData = Array('tipoUsuario' => 2, 'universidad'=>null, 'profesion'=>null);
        $form = $this->formBusqueda($formData, true);
		
		if ($request->getMethod() == "POST") {
			$form->bind($request);
			if ($form->isvalid())
			{
				$formData = $form->getData();
				$contactos = $this->getBusquedaDetallada($usuarioId, $formData);
			} else {
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("ingreso.invalido")));
				$contactos = false;
			}
		}
		else {
			$contactos = $this->getBusquedaDetallada($usuarioId, $formData);
		}

		$formulario = $this->get('formularios')->getInfoFormulario(12);

        return array(
			'formulario_info' => $formulario,
            'contactos' => $contactos,
            'form' => $form->createView(),
            'acTitulo' => $autoCompletarTitulos,
            'acUnivers' => $autoCompletarUniversidades,
            'formDT' => $formData,
        );
	}
	
	/**
     * Seleccion del mentores expertos - POST
     *
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @Route("/select_mentor_experto", name="seleccionar_mentor_experto")
     * @Method("POST")
     * @param Request $request Request enviado con ID de mentor seleccionado
     * @return Redirect
     */
    public function seleccionarMentorExpertoAction(Request $request)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
		$pr = $this->get('perfil');

		if($request->getMethod() == 'POST') 
		{
			$usuarioId = $security->getSessionValue('id');

			$mentorEId = $request->request->get('mentorE');
			$em = $this->getDoctrine()->getManager();
			$usuarioMentor = $em->getRepository('vocationetBundle:Usuarios')->findOneById($mentorEId);
			if ($usuarioMentor) {
				if($usuarioMentor->getRol()->getId() == 2) {

					// Relacion creada entre usuario y mentor
					$user = $em->getRepository('vocationetBundle:Usuarios')->findOneBy(Array('id' => $usuarioId));
					$newR = new Relaciones();
					$newR->setUsuario($usuarioMentor);
					$newR->setUsuario2($user);
					$newR->setTipo(3);
					$newR->setCreated(new \DateTime());
					$newR->setEstado(1);
					$em->persist($newR);
					
					//Registro de que el usuario participo en red de mentores
					$form_id = $this->get('formularios')->getFormId('red_mentores');
					$auxPart = $em->getRepository('vocationetBundle:Participaciones')->findOneBy(Array('formulario' => $form_id));
					if (!$auxPart) {
						$participacion = new Participaciones();
						$participacion->setFormulario($form_id);
						$participacion->setFecha(new \DateTime());
						$participacion->setUsuarioParticipa($usuarioId);
						$participacion->setUsuarioEvaluado($usuarioId);
						$participacion->setEstado(1);
						$em->persist($participacion);
					}
					
					$em->flush();
			
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("elegir.mentor"), "text" => $this->get('translator')->trans("ha.seleccionado.mentor.contactese.con.el")));
					//return $this->redirect($this->generateUrl('agenda_estudiante'));
					
				} else {
					// El usuario seleccionado no es Orientador Vocacional o no ha sido aprobado
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.elegir.mentor"), "text" => $this->get('translator')->trans("error.en.seleccion.de.mentor")));
				}
			} else {
				//El mentor seleccionado no existe
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.elegir.mentor"), "text" => $this->get('translator')->trans("error.en.seleccion.de.mentor")));
			}
		}

		return $this->redirect($this->generateUrl('red_mentores'));
	}


    // FUNCIONES Y METODOS
    
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
                    if ($cont['nombreInstitucion']) {
                        $arrContactos[$cont['id']]['estudios'][] = Array(
                            'nombreInstitucion' => $cont['nombreInstitucion'],
                            'titulo' => $cont['titulo']);
                    }
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

    /**
     * Formulario de busqueda
     * 
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Array $formData Arreglo de campos para crear el form
     * @param Bolean $seleccionarMentor Identificar si el formulario a retornar es el de seleccion de mentor o el general
     * @return Form Formulario de busqueda
     */
	private function formBusqueda($formData, $seleccionarMentor = false)
	{
		if (!$seleccionarMentor) {
			$pr = $this->get('perfil');
			// TipoUsuario ROL
			$rolAct = '';
			$roles = $pr->getRolesBusqueda();
			$form = $this->createFormBuilder($formData)
				->add('tipoUsuario', 'choice', array('choices'  => $roles,  'preferred_choices' => array($rolAct), 'required' => true))
				// Si es usuario Estudiante
				->add('colegio', 'text', array('required' => false))
				->add('universidad', 'text', array('required' => false))
				//Si es usuario Mentor
				->add('profesion', 'text', array('required' => false))
				->add('alternativaEstudio', 'text', array('required' => false))
				->getForm();
		}

		else {
			$form = $this->createFormBuilder($formData)
				->add('profesion', 'text', array('required' => false))
				->add('universidad', 'text', array('required' => false))
				->getForm();
		}

		return $form;
	}
        
    /**
     * Funcion de busqueda detallada
     * 
     * Funcion que busca en los usuarios en modo de busqueda personalizada, en la que se puede buscar por perfil 
     * de usuario, universidad y estudios retornando un arreglo bidimensional
     * 
     * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $usuarioId Id del usuario que busca
     * @param Array $busqueda Array con claves de busqueda
     * @param Int $mentorId Id del mentor en específico a buscar
     * @return Array Arreglo bi-dimencional de registros que cumplan este filtro
     */
    private function getBusquedaDetallada($usuarioId, $busqueda, $mentorId = false)
	{
        $having = "HAVING rolId =".$busqueda['tipoUsuario']."";
        if ($busqueda['tipoUsuario'] == 1) {
            if ($busqueda['colegio']) {
                $having .= " AND c.nombre LIKE '%".$busqueda['colegio']."%'";
            }
			if ($busqueda['alternativaEstudio']) {
                $having .= " AND car.nombre LIKE '%".$busqueda['alternativaEstudio']."%'";
            }
        } else {
            if ($busqueda['universidad']) {
                $having .= " AND est.nombreInstitucion LIKE '%".$busqueda['universidad']."%'";
            }
            if ($busqueda['profesion']) {
                $having .= " AND (u.usuarioProfesion LIKE '%".$busqueda['profesion']."%' OR est.titulo LIKE '%".$busqueda['profesion']."%')";
            }
            if ($busqueda['tipoUsuario'] == 3) {
				//Mentores de orientacion Vocacional aprobados
				$having .= " AND u.usuarioRolEstado = 2";
			}
			if ($mentorId) {
				$having .= " AND u.id =".$mentorId."";
			}
        }
        $em = $this->getDoctrine()->getManager();
		/**
         * SELECT u.id, ae.id, est.id, r.*, COUNT(m.id) as cantidadMentorias,
		 *  SUM(CASE WHEN (r.usuario_id = 7 OR r.usuario2_id = 7) THEN 1 ELSE 0 END) AS relacionExistente,
		 *  SUM(DISTINCT CASE WHEN (rm.usuario2_id = 7) THEN 1 ELSE 0 END) AS mentorExpeto
		 * FROM usuarios u
		 * LEFT JOIN relaciones r ON (r.usuario_id = u.id OR r.usuario2_id = u.id) AND r.tipo = 1
		 * LEFT JOIN relaciones rm ON (rm.usuario_id = u.id) AND rm.tipo = 3
		 * LEFT JOIN relaciones ment ON (ment.usuario_id = u.id OR ment.usuario2_id = u.id) AND r.tipo = 2
		 * LEFT JOIN mentorias m ON (m.usuario_mentor_id = u.id) AND m.mentoria_estado = 1 AND  m.calificacion is not null
		 * LEFT JOIN estudios ae ON u.id = ae.usuario_id
		 * LEFT JOIN alternativas_estudios est ON u.id = est.usuario_id
		 * WHERE u.id != 7
		 * GROUP BY u.id, est.id, ae.id;
        */
        $dql = "SELECT u.id, u.usuarioNombre, u.usuarioApellido, u.usuarioImagen, 
						rol.id as rolId, rol.nombre AS rolNombre, u.usuarioRolEstado,
						u.usuarioProfesion, u.usuarioPuntos, u.usuarioValorMentoria,
						c.nombre AS nombreColegio, u.usuarioCursoActual,
						est.nombreInstitucion, est.titulo,
						r.estado, r.id AS relacionId,
                        SUM(DISTINCT CASE WHEN ((r.usuario =:usuarioId OR r.usuario2 =:usuarioId) AND r.tipo = 1) THEN 1 ELSE 0 END) AS relacionExistente,
						SUM(DISTINCT CASE WHEN (rm.usuario2 =:usuarioId AND rm.tipo = 3) THEN 1 ELSE 0 END) AS seleccionadoMentorExperto,
                        COUNT(m.id) as cantidadMentorias,
						car.id AS carreraId, car.nombre AS nombreCarrera
                FROM vocationetBundle:Usuarios u
                JOIN u.rol rol
                LEFT JOIN u.colegio c
                LEFT JOIN vocationetBundle:Estudios est WITH u.id = est.usuario
                LEFT JOIN vocationetBundle:Relaciones r WITH (r.usuario = u.id OR r.usuario2 = u.id) AND r.tipo = 1
				LEFT JOIN vocationetBundle:Relaciones rm WITH (rm.usuario = u.id) AND rm.tipo = 3
                LEFT JOIN vocationetBundle:Mentorias m WITH (m.usuarioMentor = u.id AND m.mentoriaEstado = 1 AND m.calificacion IS NOT NULL)
				LEFT JOIN vocationetBundle:AlternativasEstudios ae WITH u.id = ae.usuario
                LEFT JOIN ae.carrera car
                WHERE u.id !=:usuarioId
                GROUP BY u.id, est.id, ae.id
                ".$having."
                ORDER BY u.id, r.estado";
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
						'relacionId' => $cont['relacionId'],
						'usuarioPuntos' => $cont['usuarioPuntos'],
                        'relacionExistente' => $cont['relacionExistente'],
                        'seleccionadoMentorExperto' => $cont['seleccionadoMentorExperto'],
						'usuarioValorMentoria' => $cont['usuarioValorMentoria'],
                        'cantidadMentorias' => $cont['cantidadMentorias']);
                    if ($cont['nombreInstitucion']) {
                        $arrContactos[$cont['id']]['estudios'][] = Array(
                            'nombreInstitucion' => $cont['nombreInstitucion'],
                            'titulo' => $cont['titulo']);
                    }
					if ($cont['carreraId']) {
                        $arrContactos[$cont['id']]['alternativa'][] = Array(
							'nombreCarrera' => $cont['nombreCarrera']);
                    }
                    $auxId = $cont['id'];
				} else {
					if ($cont['nombreInstitucion']) {
						$arrContactos[$cont['id']]['estudios'][] = Array(
							'nombreInstitucion' => $cont['nombreInstitucion'],
							'titulo' => $cont['titulo']);
					}
					if ($cont['carreraId']) {
                        $arrContactos[$cont['id']]['alternativa'][] = Array(
                            'nombreCarrera' => $cont['nombreCarrera']);
                    }
				}
			}
		}
		return $arrContactos;
	}
}
?>
