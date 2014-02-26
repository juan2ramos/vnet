<?php

namespace AT\vocationetBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AT\vocationetBundle\Entity\Colegios;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Controlador de perfil de usuarios de vocationet
 * @package vocationetBundle
 * @Route("/")
 */
class PerfilController extends Controller
{
	/**
	 * Perfil del usuario
	 *
	 * En este action se estructuran los datos necesarios para el perfil dependiendo el rol
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @Template("vocationetBundle:Perfil:perfilmentor.html.twig")
	 * @Template("vocationetBundle:Perfil:perfilestudiante.html.twig")
	 * @Route("/{perfilId}/perfil", name="perfil")
	 * @Method("GET")
	 * @param Int $pefilId Id del mentor
	 * @return Render Vista renderizada del perfil
	 * @throws createNotFoundException Si el perfil al que se quiere acceder no existe 
	 */
    public function indexAction($perfilId)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
		$tr = $this->get('translator');
		$pr = $this->get('perfil');
		$em = $this->getDoctrine()->getManager();
		//error_reporting(true);

		$perfil = $pr->getPerfil($perfilId);
		
		if (!$perfil) {
			throw $this->createNotFoundException($tr->trans("perfil.no.existe", array(), 'label'));
		}
		
		//PUBLICIDAD Y/O INFORMACION
		$publicidad = $pr->getPublicidad($security->getSessionValue("rolId"));
 
		if ($perfil['nombreRol'] == 'mentor_e' or $perfil['nombreRol'] == 'mentor_ov' or $perfil['nombreRol'] == 'administrador')
		{
			// ROL MENTOR
			$estudios = $pr->getEstudiosPerfil($perfilId);
			$trabajos = $pr->getTrabajosPerfil($perfilId);
			$rutas = Array('HV'=>$pr->getRutaHojaVida(), 'TP' => $pr->getRutaTarjetaProfesional());

			$usuarioId = $security->getSessionValue('id');
			$calificarMentor = $pr->mentoriasSinCalificar($perfilId, $usuarioId);

			if ($calificarMentor)
			foreach ($calificarMentor as $key=>$mentoria) {
				$auxForm = $this->formCalificarMentoria($mentoria['id']);
				$calificarMentor[$key]['form'] = $auxForm->createView();
			}

			// Calificaciones actuales del mentos, clasificados por No. de estrellas
			$calificaciones = $pr->mentoriasCalificadas($perfilId);

			if (($perfilId == $usuarioId) && ($perfil['nombreRol'] != 'administrador')) {
				// Campos vacios para pedir que sean diligenciados
				$auxRol = ($perfil['usuarioRolEstado'] == 0) ? 1 : 0;
				$auxVM = ($perfil['nombreRol'] == 'mentor_ov') ? 0 : (($perfil['usuarioValorMentoria']) ? 0 : 1);
				$auxTP = ($perfil['usuarioTarjetaProfesional']) ? 0 : 1;
				$auxHV = ($perfil['usuarioHojaVida']) ? 0 : 1;
				$auxTotal = $auxRol + $auxVM + $auxTP + $auxHV;
				
				$camposVacios = Array('rol' => $auxRol, 'valorhora' => $auxVM, 'tarjetaProfesional' => $auxTP,
					'hojaVida' => $auxHV, 'total' => $auxTotal );
			} else {
				$camposVacios = Array('total' => 0);
			}

			return $this->render('vocationetBundle:Perfil:perfilmentor.html.twig', array(
					'perfil' => $perfil, 'estudios' => $estudios, 'trabajos' => $trabajos, 'rutas' => $rutas,
					'mentorias' => $calificarMentor, 'calificaciones' => $calificaciones,
					'camposvacios' => $camposVacios, 'publicidad' => $publicidad
			));
		}
		else
		{
			// Acceso a informe de mercado laboral
			$showInformeML = $rutaInformeML = false;
			$seleccionarMentor = $this->get('perfil')->confirmarMentorOrientacionVocacional($perfilId);
			if ($seleccionarMentor) {
				if ($seleccionarMentor['id'] == $security->getSessionValue('id')) {
					
					// Validación de si ya se ha subido informe relacionado con el listado de carreras seleccionadas por el usuario
					$rutaInformeML = $security->getParameter('ruta_files_mercado_laboral').'user'.$perfilId.'.pdf';
					$showInformeML = file_exists($rutaInformeML);
				}
			}
			
			$fechaactual = strtotime(date('Y-m-d H:i:s'));
			$fechaplaneada = ($perfil['usuarioFechaPlaneacion']) ? $perfil['usuarioFechaPlaneacion']->getTimestamp() : 0;
			$tiempoRestante = ($fechaplaneada - $fechaactual) / 100000;
			
			//progress-bar-success - verde , progress-bar-warning - amarillo, progress-bar-danger - rojo
			$semaforo = $this->calculoEstadoSemaforo($tiempoRestante);
			//$semaforo = Array('porcentaje' =>20, 'color'=>'progress-bar-danger');  //porcentaje avance
			$avancePrograma = Array('porcentaje' =>60, 'color'=>'progress-bar-warning');  //porcentaje avance
			$vancesDiagnostico = Array('mitdc'=> 50, 'hyp' => 60, 'info' => 40, 'invest' => 80, 'dc' => 10 );
			
			$estadoActual = $this->get('perfil')->getEstadoActualPlataforma($perfilId);

			$adicionales = Array('tiempoRestante' => $tiempoRestante);
			$pendientes = Array(
				'semaforo' => $semaforo,
				'msjsinleer' => 10,
				'avancePrograma' => $avancePrograma,
				'avanceDiagnostico' => $vancesDiagnostico,
				'nivel' => 3,
			);
			
			// ROL ESTUDIANTE
			return $this->render('vocationetBundle:Perfil:perfilestudiante.html.twig', array(
						'perfil' => $perfil,
						'adicional' =>  $adicionales,
						'pendiente' => $pendientes,
						// Acceso de visualizacion para mentor
						'showInformeML' => $showInformeML,
						'rutaInformeML' => $rutaInformeML,
						'publicidad' => $publicidad,
						'recorrido' => $estadoActual,
					));
		}
    }

	/**
	 * Editar perfil de usuario
	 *
	 * En este action el usuario (estudiante) modifica su informacion personal, como
	 * colegio, grado, nombres, apellidos, fecha de nacimiento, genero, imagen (Si no esta logeado con facebook), etc.
	 * - Exclusivo para estudiantes
	 *
	 * Si el usuario es mentor tendra la posibilidad de subir su hoja de vida y tarjeta profesional
	 * - Exclusivo para mentores
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @Template("vocationetBundle:Perfil:editperfilEstudiante.html.twig")
     * @Template("vocationetBundle:Perfil:editperfilMentor.html.twig")
	 * @Route("/edit-perfil", name="perfil_edit")
	 * @param Request Form edicion
	 * @Method({"GET", "POST"})
	 */
	public function editAction(Request $request)
	{
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
		$pr = $this->get('perfil');
		$tr = $this->get('translator');
		$em = $this->getDoctrine()->getManager();
		
		//error_reporting(true);
		$id = $security->getSessionValue('id');
		$perfil = $pr->getPerfil($id);

		if (!$perfil) {
			throw $this->createNotFoundException($tr->trans("perfil.no.existe", array(), 'label'));
		}
		
		//PUBLICIDAD Y/O INFORMACION
		$publicidad = $pr->getPublicidad($security->getSessionValue("rolId"));

		if ($perfil['nombreRol'] == 'estudiante')
		{
			// Genero (masculino o femenino del usuario)
			$generoAct = ($perfil['usuarioGenero']) ? $perfil['usuarioGenero'] : '';
			$generos = Array('masculino' => $tr->trans("masculino"), 'femenino' => $tr->trans("femenino"));

			// String de la fecha de nacimiento
			$stringDATE = '';
			if ($perfil['usuarioFechaNacimiento']) {
				$stringDATE = $perfil['usuarioFechaNacimiento']->format('Y-m-d');
			}

			// Fecha planeacion
			$stringFPDATE = '';
			if ($perfil['usuarioFechaPlaneacion']) {
				$stringFPDATE = $perfil['usuarioFechaPlaneacion']->format('Y-m-d');
			}

			//Colegios
			$colegioAct = ($perfil['colegioId']) ? $perfil['colegioId'] : '';
			$empty_value_col = (!$colegioAct) ? $tr->trans("seleccione.un.colegio", array(), 'label') : false;
			
			$colegios = $pr->getColegios();
			$colegios['otro'] = $tr->trans("otro", array(), 'label');

			// Grados
			$gradoAct = ($perfil['usuarioCursoActual']) ? $perfil['usuarioCursoActual'] : '';
			$empty_value_grado = (!$gradoAct) ? $tr->trans("seleccione.un.grado", array(), 'label') : false;
			$grados = $pr->getGrados();

			// Formulario de edicion con datos actuales del usuario
			$formData = Array('nombre'=> '');
			$form = $this->createFormBuilder($formData)
			   ->add('nombre', 'text', array('required' => true, 'data'=> $perfil['usuarioNombre'], 'attr' => Array('pattern' => '^[a-zA-Z áéíóúÁÉÍÓÚñÑ]*$' )))
			   ->add('apellido', 'text', array('required' => true, 'data'=> $perfil['usuarioApellido'], 'attr' => Array('pattern' => '^[a-zA-Z áéíóúÁÉÍÓÚñÑ]*$')))
			   ->add('fechaNacimiento', 'text', array('required' => false, 'data'=> $stringDATE))
			   ->add('fechaPlaneacion', 'text', array('required' => false, 'data'=> $stringFPDATE))
			   ->add('genero', 'choice', array('choices'  => $generos,  'preferred_choices' => array($generoAct), 'required' => true))
			   ->add('colegio', 'choice', array('choices'  => $colegios,  'preferred_choices' => array($colegioAct), 'required' => false, 'empty_value' => $empty_value_col))
			   ->add('colegio_otro', 'text', array('required' => false))
			   ->add('grado', 'choice', array('choices'  => $grados, 'preferred_choices' => array($gradoAct), 'required' => false, 'empty_value' => $empty_value_grado))
			   ->add('imagen', 'file', array('required' => false))
			   ->getForm();

			if ($request->getMethod() == 'POST')
			{		
				$form->bind($request);
				if ($form->isvalid())
				{
					$dataForm = $form->getData();
					$errores = $this->validatePerfilEditEstudiante($dataForm);

					if ($errores == 0)
					{
						$usuario = $em->getRepository('vocationetBundle:Usuarios')->findOneById($id);
						$usuario->setUsuarioNombre($dataForm['nombre']);
						$usuario->setUsuarioApellido($dataForm['apellido']);
						$usuario->setUsuarioGenero($dataForm['genero']);

						if ($dataForm['colegio']) {
							if ($dataForm['colegio'] == 'otro') {
								$colegio = new colegios();
								$colegio->setNombre($dataForm['colegio_otro']);
								$em->persist($colegio);
								$em->flush();
							} else {
								$colegio = $em->getRepository('vocationetBundle:Colegios')->findOneById($dataForm['colegio']);
							}
							$usuario->setColegio($colegio);
						}

						if ($dataForm['grado'])	{
							$usuario->setUsuarioCursoActual($dataForm['grado']);
						}
						
						if ($dataForm['fechaNacimiento']) {
							$usuario->setUsuarioFechaNacimiento(new \DateTime($dataForm['fechaNacimiento']));
						}

						if ($dataForm['fechaPlaneacion']) {
							$usuario->setUsuarioFechaPlaneacion(new \DateTime($dataForm['fechaPlaneacion']));
						}

						if ($dataForm['imagen']) {
							$dir ='img/vocationet/usuarios/';
							$nameImg = 'ImgUsuarioId_'.$id.'.png';
							$form['imagen']->getData()->move($dir, $nameImg);
							$usuario->setUsuarioImagen($dir.$nameImg);
						}

						$usuario->setModified(new \DateTime());
						$em->persist($usuario);
						$em->flush();
						
						$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("perfil.editado"), "text" => $this->get('translator')->trans("perfil.editado.correctamente")));
						return $this->redirect($this->generateUrl('perfil', array('perfilId'=> $id)));
					}
				}
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
			}
			
			$pendientes = Array('msjsinleer' => 10 );
			return $this->render('vocationetBundle:Perfil:editperfilEstudiante.html.twig', array(
						'perfil' => $perfil, 'pendiente' => $pendientes, 'form' => $form->createView(),
						'publicidad' => $publicidad,
					));
		}
		else {
			
			if ($perfil['usuarioRolEstado'] == 0) {
				
				$rolActual = $perfil['rolId'];
				$roles = $pr->getRolesMentor();
				
				$formData = Array('hojaVida' => null, 'tarjetaProfesional' => null, 'valorHora' => 0);
				$form = $this->createFormBuilder($formData)
					->add('rol', 'choice', array('choices'  => $roles,  'preferred_choices' => array($rolActual), 'required' => true))
					->add('profesion', 'text', array('required' => true, 'data'=> $perfil['usuarioProfesion']))
					->add('valorHora', 'text', array('required' => true, 'data'=> $perfil['usuarioValorMentoria'], 'attr' => Array('pattern' => '^[0-9]*$')))
					->add('hojaVida', 'file', array('required' => false, 'attr' => array ('accept'=> 'application/pdf')))
					->add('tarjetaProfesional', 'file', array('required' => false, 'attr' => array ('accept'=> 'application/pdf')))
					->getForm();
			} else {
				$formData = Array('hojaVida' => null, 'tarjetaProfesional' => null, 'valorHora' => 0);
				$form = $this->createFormBuilder($formData)
					->add('profesion', 'text', array('required' => true, 'data'=> $perfil['usuarioProfesion']))
					->add('valorHora', 'text', array('required' => true, 'data'=> $perfil['usuarioValorMentoria'], 'attr' => Array('pattern' => '^[0-9]*$')))
					->add('hojaVida', 'file', array('required' => false, 'attr' => array ('accept'=> 'application/pdf')))
					->add('tarjetaProfesional', 'file', array('required' => false, 'attr' => array ('accept'=> 'application/pdf')))
					->getForm();
			}

			if ($request->getMethod() == 'POST')
			{
				$form->bind($request);
				if ($form->isvalid())
				{
					$dataForm = $form->getData();
					$formIncludeRol = ($perfil['usuarioRolEstado'] == 0) ? true : false;
					$errores = $this->validatePerfilEditMentor($dataForm, $formIncludeRol);

					//Validación del rol seleccionado
					if ($perfil['usuarioRolEstado'] == 0) {
						$rolMentor = $em->getRepository('vocationetBundle:Roles')->findOneById($dataForm['rol']);
						if (!$rolMentor) {
							$errores += 1;
						}
					}

					if ($errores == 0)
					{
						$usuario = $em->getRepository('vocationetBundle:usuarios')->findOneById($id);
						
						if ($dataForm['hojaVida']) {
							$rutaHV = $this->get('perfil')->getRutaHojaVida();
							$dir ='img/vocationet/usuarios/';
							if ($usuario->getUsuarioHojaVida()){
								$nameHV = $usuario->getUsuarioHojaVida();
							}
							else {
								$stdate = strtotime(date('Y-m-d H:i:s'));
								$nameHV = 'HojaVidaId_'.$id.$stdate.'.pdf';
								$usuario->setUsuarioHojaVida($nameHV);
							}
							$form['hojaVida']->getData()->move($rutaHV, $nameHV);
						}

						if ($dataForm['tarjetaProfesional']) {
							$rutaTP = $this->get('perfil')->getRutaTarjetaProfesional();
							if ($usuario->getUsuarioTarjetaProfesional()){
								$nameTP = $usuario->getUsuarioTarjetaProfesional();
							}
							else {
								$stdate = strtotime(date('Y-m-d H:i:s'));
								$nameTP = 'tarjetaProfesional_'.$id.$stdate.'.pdf';
								$usuario->setUsuarioTarjetaProfesional($nameTP);
							}
							$form['tarjetaProfesional']->getData()->move($rutaTP, $nameTP);
						}

						if ($perfil['usuarioRolEstado'] == 0) {
								$usuario->setRol($rolMentor);
								$usuario->setUsuarioRolEstado(1);
						}
						
						$usuario->setUsuarioProfesion($dataForm['profesion']);
						$usuario->setUsuarioValorMentoria($dataForm['valorHora']);
						$usuario->setModified(new \DateTime());
						$em->persist($usuario);
						$em->flush();
						
						$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("perfil.sincronizado"), "text" => $this->get('translator')->trans("perfil.editado.correctamente")));
						return $this->redirect($this->generateUrl('perfil', array('perfilId'=> $id)));
					}
				}
				
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
			}

			$pendientes = Array('msjsinleer' => 10 );
			return $this->render('vocationetBundle:Perfil:editperfilMentor.html.twig', array(
					'perfil' => $perfil, 'pendiente' => $pendientes, 'form' => $form->createView(),
					'publicidad' => $publicidad,
				));
		}
		
	}

	/**
	 * Sincronizar el perfil de linkedin con vocationet
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @Route("/sincronizar-perfil", name="perfil_sincronizar")
	 * @Method("GET")
	 */
	 public function sincronizarAction()
	 {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
		$linkedin = $this->get('linkedin');
		$pr = $this->get('perfil');

		$accessToken = $security->getSessionValue('access_token');
		$usuarioId = $security->getSessionValue('id');

		$em = $this->getDoctrine()->getManager();
		$usuario = $em->getRepository('vocationetBundle:Usuarios')->findOneById($usuarioId);
		$ultimaModDB = $usuario->getSyncLinkedin();
		$ultimaModDB = ($ultimaModDB) ? ($ultimaModDB->getTimestamp() * 1000) : 0;

		$ultimaModLinkedIn = $linkedin->getLastModifiedTimestamp($accessToken);
		if ($ultimaModLinkedIn === false) {
			$ultimaModLinkedIn = 0;
		}
		
		if ($ultimaModDB < $ultimaModLinkedIn) {
			$educacion = $linkedin->getEducations($accessToken);
			$positions = $linkedin->getPositions($accessToken);
			$FieldsAdditional = $linkedin->getFieldsAdditional($accessToken);

			if (($educacion === false) || ($educacion === false) || ($FieldsAdditional === false) ) {
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("error.sincronizacion"), "text" => $this->get('translator')->trans("ocurrio.un.error.al.sincronizar.con.su.perfil.de.linkedin")));
			}
			else
			{
				$pr->deleteEstudiosPerfil($usuarioId);
				$pr->setEstudiosPerfil($educacion, $usuarioId);
				$pr->deleteTrabajosPerfil($usuarioId);
				$pr->setTrabajosPerfil($positions, $usuarioId);
				
				$usuario->setSyncLinkedin(new \DateTime());
				$usuario->setUsuarioPerfilProfesional($FieldsAdditional['perfilProfesional']);
				$em->persist($usuario);
				$em->flush();

				$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("perfil.sincronizado"), "text" => $this->get('translator')->trans("perfil.sincronizado.correctamente")));
			}
		}
		else {
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("perfil.sincronizado"), "text" => $this->get('translator')->trans("no.se.encontratron.cambios.para.sincronizacion")));
		}
		return $this->redirect($this->generateUrl('perfil', array('perfilId'=> $usuarioId)));
	 }

	/**
	 * Ultimas 25 Reseñas del mentor
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $pefilId Id del mentor
     * @return Render Vista renderizada del perfil, con reseñas
	 * @Template("vocationetBundle:Perfil:resenasMentor.html.twig")
	 * @Route("/{perfilId}/resenas", name="mentor_resenas")
	 * @Method("GET")
	 */
    public function resenasAction($perfilId)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
		$tr = $this->get('translator');
		$pr = $this->get('perfil');

		$perfil = $pr->getPerfil($perfilId);
		
		if (!$perfil) {
			throw $this->createNotFoundException($tr->trans("perfil.no.existe", array(), 'label'));
		}
 
		if ($perfil['nombreRol'] == 'mentor_e' or $perfil['nombreRol'] == 'mentor_ov') {
			// Calificaciones actuales del mentos, clasificados por No. de estrellas
			$calificaciones = $pr->mentoriasCalificadas($perfilId);
			$resenas = $pr->getResenasMentor($perfilId);
		}
		else {
			throw $this->createNotFoundException($tr->trans("perfil.no.existe", array(), 'label'));
		}
		
		$publicidad = $pr->getPublicidad($security->getSessionValue("rolId"));

		return array('perfil' => $perfil, 'calificaciones' => $calificaciones, 'resenas' => $resenas, 'publicidad' => $publicidad);
	}

	/**
	 * Horarios disponibles del mentor
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $pefilId Id del mentor
     * @return Render Vista renderizada del perfil, con agenda con mentorias disponibles del mentor
	 * @Template("vocationetBundle:Perfil:horariosDisponiblesMentor.html.twig")
	 * @Route("/{perfilId}/horarioDisponible", name="horarios_disponibles_mentor")
	 * @Method("GET")
	 */
    public function horarioDisponibleAction($perfilId)
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
		$tr = $this->get('translator');
		$pr = $this->get('perfil');

		$perfil = $pr->getPerfil($perfilId);
		
		if (!$perfil) {
			throw $this->createNotFoundException($tr->trans("perfil.no.existe", array(), 'label'));
		}
 
		if ($perfil['nombreRol'] == 'mentor_e' or $perfil['nombreRol'] == 'mentor_ov')
		{
			$rolIdSession = $security->getSessionValue('rolId');
			if ($rolIdSession == 1 or $rolIdSession == 4){
				$dql = "SELECT
							m.id,
							m.mentoriaInicio,
							m.mentoriaFin,
							m.mentoriaEstado,
							u.id estudianteId,
							u.usuarioNombre,
							u.usuarioApellido
						FROM 
							vocationetBundle:Mentorias m
							LEFT JOIN vocationetBundle:Usuarios u WITH m.usuarioEstudiante = u.id
						WHERE
							m.usuarioMentor = :usuarioId AND u.id IS NULL";
				$em = $this->getDoctrine()->getManager();
				$query = $em->createQuery($dql);
				$query->setParameter('usuarioId', $perfilId);
				$mentorias = $query->getResult();
			}

			/**
			 * @var Bool $tipoIngreso Variable para controlar si el ingreso es solo de consulta (FALSE),
			 * o si se habilita funcionamiento agendamiento al calendario (TRUE), teniendo en cuenta la relacion
			 * que existe entre ambos usuarios ($perfil, $estudianteId) existe y esta aprobada
			 */
			$tipoIngreso = $this->getRelacionMentoria($perfilId, $security->getSessionValue('id'));
		}
		else
		{
			throw $this->createNotFoundException($tr->trans("perfil.no.existe", array(), 'label'));
		}
		
		//PUBLICIDAD Y/O INFORMACION
		$publicidad = false;
		return array('perfil' => $perfil, 'mentorias' => $mentorias, 'tipoIngreso' => $tipoIngreso);
	}

	/**
	 * Calificar un mentor por AJAX
	 *
	 * Esta función registra la calificación del usuario y actualiza el promedio de la calificación del mentor
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @Template("vocationetBundle:Perfil:perfilmentor.html.twig")
     * @param Request $request Form de calificación de mentoria
     * @param Int $pefilId Id del mentor
     * @param Int $mentoriaId Id de la mentoria
	 * @Route("/{perfilId}/{mentoriaId}/calificar", name="calificar_mentor")
	 * @Method("POST")
	 * @return Response JSON
	 */
	public function calificarAction(Request $request, $perfilId, $mentoriaId)
	{
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$form = $this->formCalificarMentoria($mentoriaId);
		$msg = $this->get('translator')->trans("error.en.solicitud.calificar", array(), 'messages');
		$status = 'error';
		
		if ($request->getMethod() == "POST")
		{
			$form->bind($request);
			if ($form->isvalid())
			{
				$formData = $form->getData();
				$em = $this->getDoctrine()->getManager();
				$mentoria = $em->getRepository('vocationetBundle:Mentorias')->findOneBy(Array('id' => $mentoriaId, 'mentoriaEstado' => 1, 'usuarioMentor'=>$perfilId));
				
				$calificacion = $formData['calificacion'];
				$resena = $formData['resena'];
				
				if ($mentoria && ($calificacion>=0 && $calificacion<=5) && ($resena != '') && ($formData['mentoriaId'] == $mentoriaId)) {

					$usuarioMentor = $em->getRepository('vocationetBundle:Usuarios')->findOneById($perfilId);
					if ($usuarioMentor) {
						
						$mentoria->setCalificacion($calificacion);
						$mentoria->setResena($resena);
						$em->persist($mentoria);
						$em->flush();

						$pr = $this->get('perfil');
						$calificaciones = $pr->mentoriasCalificadas($perfilId);
						$Puntuacion = ($calificaciones['totalCalificacion'] > 0) ? ($calificaciones['totalCalificacion']/$calificaciones['totalUsuarios']) : 0;

						$usuarioMentor->setUsuarioPuntos($Puntuacion);
						$em->persist($usuarioMentor);
						$em->flush();

						$status = 'success';
						$msg = $this->get('translator')->trans("calificacion.guardada.correctamente", array(), 'messages');
					}
				}
			}
		}

		return new Response(json_encode(array(
            'status' => $status,
			'message' => $msg,
        )));
	}

	/**
	 * Funcion Privada de validación de formulario del usuario tipo mentor
	 * (hoja de vida y tarjeta profesional)
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Array $dataForm Formulario a validar
     * @param Boolean $formIncludeRol Para validar campo de seleccion de tipo de mentor por primera vez
     * @return Int Cantidad de errores encontrados
	 */
	private function validatePerfilEditMentor($dataForm, $formIncludeRol)
	{
		/**
		 * Validacione aplicadas
		 * valorHora => NotBlank, Regex(Solo numeros)
		 * hojaVida => File(Formatos permitidos)
		 * tarjetaProfesional => File(Formatos permitidos)
		 * profesion => NotBlank, Regex(Texto y numeros)
		 */

		$NotBlank = new Assert\NotBlank();
		$File = new Assert\File(Array('mimeTypes' => Array("application/pdf")));
		$Regex = new Assert\Regex(Array('pattern'=>'/^[0-9]*$/'));
		$RegexAN = new Assert\Regex(Array('pattern'=>'/^[a-zA-Z áéíóúÁÉÍÓÚñÑ]*$/'));

		$hojaVida = $dataForm['hojaVida'];
		$tarjetaProfesional = $dataForm['tarjetaProfesional'];
		$valorHora = $dataForm['valorHora'];
		$profesion = $dataForm['profesion'];

		$countErrores = 0;
		$countErrores += (count($this->get('validator')->validateValue($valorHora, Array($Regex, $NotBlank))) == 0) ? 0 : 1;
		$countErrores += (count($this->get('validator')->validateValue($profesion, Array($RegexAN, $NotBlank))) == 0) ? 0 : 1;
		
		if ($formIncludeRol) {
			if (($dataForm['rol'] != 2) and ($dataForm['rol'] != 3)) {
				$countErrores += 1;
			}
		}
		if ($hojaVida) {
			$countErrores += (count($this->get('validator')->validateValue($hojaVida, $File)) == 0) ? 0 : 1;
		}
		if ($tarjetaProfesional) {
			$countErrores += (count($this->get('validator')->validateValue($tarjetaProfesional, $File)) == 0) ? 0 : 1;
		}
		
		return $countErrores;
	}

	/**
	 * Funcion Privada de validación de formulario del usuario tipo estudiante
	 * (nombre, apellido, imagen, fecha de nacimiento, colegio, etc.)
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Object Formulario a validar
     * @return Int Cantidad de errores encontrados
	 */
	private function validatePerfilEditEstudiante($dataForm)
	{
		$nombre = $dataForm['nombre'];
		$apellido = $dataForm['apellido'];
		$imagen = $dataForm['imagen'];
		$fechanacimiento = $dataForm['fechaNacimiento'];
		$colegio = $dataForm['colegio'];
		$colegioOtro = $dataForm['colegio_otro'];
		$fechaPlaneacion = $dataForm['fechaPlaneacion'];

		$NotBlank = new Assert\NotBlank();
		$Regex = new Assert\Regex(Array('pattern'=>'/^[a-zA-Z áéíóúÁÉÍÓÚñÑ]*$/'));
		$File = new Assert\File(Array('mimeTypes' => Array("image/jpg", "image/jpeg", "image/png")));
		$Date = new Assert\Date();

		/**
		 * Validacione aplicadas
		 * Nombre => NotBlank - Regex (Validacion para solo nombres y apellidos (letras y espacios))
		 * Apellido => NotBlank - Regex (Validacion para solo nombres y apellidos (letras y espacios))
		 * Imagen => File(Formatos permitidos)
		 * FechaNacimiento => Date (Formato de fecha valido)
		 * fechaPlaneacion => Date (Formato de fecha valido)
		 * colegio_otro => NotBlank
		 */

		$countErrores = 0;
		$countErrores += (count($this->get('validator')->validateValue($nombre, $NotBlank)) == 0) ? 0 : 1;
		$countErrores += (count($this->get('validator')->validateValue($nombre, $Regex)) == 0) ? 0 : 1;
		$countErrores += (count($this->get('validator')->validateValue($apellido, $NotBlank)) == 0) ? 0 : 1;
		$countErrores += (count($this->get('validator')->validateValue($apellido, $Regex)) == 0) ? 0 : 1;
		if ($imagen){
			$countErrores += (count($this->get('validator')->validateValue($imagen, $File)) == 0) ? 0 : 1;
		}
		if ($fechanacimiento) {
			$countErrores += (count($this->get('validator')->validateValue($fechanacimiento, $Date)) == 0) ? 0 : 1;
		}
		if ($fechaPlaneacion) {
			$countErrores += (count($this->get('validator')->validateValue($fechaPlaneacion, $Date)) == 0) ? 0 : 1;
		}
		if ($colegio == 'otro') {
			$countErrores += (count($this->get('validator')->validateValue($colegioOtro, $NotBlank)) == 0) ? 0 : 1;
		}
		
		return $countErrores;
	}

	/**
	 * Formulario de calificación de mentoria
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @param Int $mentoriaId Id de la mentoria
	 * @return Formulario de calificación de mentoria
	 */
	private function formCalificarMentoria($mentoriaId)
	{
		$calificacion = Array('0'=> 0, '1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5);
		$formData = Array('calificacion'=> '', 'resena' => '');
		$form = $this->createFormBuilder($formData)
			->add('calificacion', 'choice', array('choices'  => $calificacion, 'required' => true, 'empty_value' => false))
			->add('mentoriaId', 'hidden', array('required' => true, 'data' => $mentoriaId))
			->add('resena', 'textarea', array('required' => true))
			->getForm();
		return $form;
	}

	/**
	 * Estado de semaforo (Barra de progreso)
	 * 
	 * Función que dependiendo del numero de dias restantes retorna uno u otro color a la barra de progreso
	 * el procentaje no se esta tendiendo en cuenta en este momento.
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $diasRestantes Cantidad de dias restantes para presentación
	 * @return Array Arreglo con porcentaje y color de barra dependiendo estado
	 */
    private function calculoEstadoSemaforo($diasRestantes)
    {
		$color = 'progress-bar-success';
		if($diasRestantes<=120){
			$color = 'progress-bar-warning';
		}
		if($diasRestantes<=30){
			$color = 'progress-bar-danger';
		}
		return Array('porcentaje' =>100, 'color'=>$color);
	}

	/**
	 * Funcion que retorna si hay relacion de mentoria entre los dos usuarios que ingresan como parametro
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $mentorId Id del primer usuario (mentor y/o estudiante)
     * @param Int $estudianteId Id del segundo usuario (mentor y/o estudiante)
     * @return Bool Retorna TRUE si exite relacion, caso contrario FALSE
	 */
	private function getRelacionMentoria($mentorId, $estudianteId)
	{
		/**
		 * @var String Consulta SQL que trae la relacion entre los dos usuarios con tipo mentoria, y aprobada.
		 * SELECT * FROM relaciones r
		 * WHERE ((r.usuario_id = 25 OR r.usuario2_id = 25) AND (r.usuario_id = 12 OR r.usuario2_id = 12))
		 * AND (r.tipo = 2 OR r.tipo = 3) AND r.estado = 1;
		 */
		$dql = "SELECT r.id FROM vocationetBundle:Relaciones r
				WHERE
					((r.usuario = :mentorId OR r.usuario2 = :mentorId) AND (r.usuario = :estudianteId OR r.usuario2 = :estudianteId))
					AND (r.tipo = 2 OR r.tipo = 3)
					AND r.estado = 1";
		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery($dql);
		$query->setParameter('mentorId', $mentorId);
		$query->setParameter('estudianteId', $estudianteId);
		$return = $query->getResult();
		$aux = ($return) ? true : false;
		return $aux;
	}
}

	
