<?php

namespace AT\vocationetBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AT\vocationetBundle\Entity\colegios;
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
	 * @return Render Vista renderizada del perfil
	 * @throws createNotFoundException Si el perfil al que se quiere acceder no existe 
	 */
    public function indexAction($perfilId)
    {
		$tr = $this->get('translator');
		$pr = $this->get('perfil');
		//error_reporting(true);

		$perfil = $pr->getPerfil($perfilId);
		
		if (!$perfil) {
			throw $this->createNotFoundException($tr->trans("perfil.no.existe", array(), 'label'));
		}
 
		if ($perfil['nombreRol'] == 'mentor_e' or $perfil['nombreRol'] == 'mentor_ov')
		{
			// ROL MENTOR
			$estudios = $pr->getEstudiosPerfil($perfilId);
			$trabajos = $pr->getTrabajosPerfil($perfilId);
			$rutas = Array('HV'=>$pr->getRutaHojaVida(), 'TP' => $pr->getRutaTarjetaProfesional());
			return $this->render('vocationetBundle:Perfil:perfilmentor.html.twig', array(
						'perfil' => $perfil, 'estudios' => $estudios, 'trabajos' => $trabajos, 'rutas' => $rutas ));
		}
		else
		{
			$fechaactual = strtotime(date('Y-m-d H:i:s'));
			$fechaplaneada = ($perfil['usuarioFechaPlaneacion']) ? $perfil['usuarioFechaPlaneacion']->getTimestamp() : 0;
			$tiempoRestante = ($fechaplaneada - $fechaactual) / 100000;
			
			//progress-bar-success - verde , progress-bar-warning - amarillo, progress-bar-danger - rojo
			$semaforo = Array('porcentaje' =>20, 'color'=>'progress-bar-danger');  //porcentaje avance
			$avancePrograma = Array('porcentaje' =>60, 'color'=>'progress-bar-warning');  //porcentaje avance
			$vancesDiagnostico = Array('mitdc'=> 50, 'hyp' => 60, 'info' => 40, 'invest' => 80, 'dc' => 10 );

			$adicionales = Array('tiempoRestante' => $tiempoRestante,);
			$pendientes = Array(
				'semaforo' => $semaforo,
				'msjsinleer' => 10,
				'avancePrograma' => $avancePrograma,
				'avanceDiagnostico' => $vancesDiagnostico,
				'nivel' => 3,
			);
			// ROL ESTUDIANTE
			return $this->render('vocationetBundle:Perfil:perfilestudiante.html.twig', array(
						'perfil' => $perfil, 'adicional' =>  $adicionales, 'pendiente' => $pendientes ));
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
	 */
	public function editAction(Request $request)
	{
		$security = $this->get('security');
		$pr = $this->get('perfil');
		$tr = $this->get('translator');
		
		//error_reporting(true);
		$id = 4;
		$perfil = $pr->getPerfil($id);

		if (!$perfil) {
			throw $this->createNotFoundException($tr->trans("perfil.no.existe", array(), 'label'));
		}

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
						$em = $this->getDoctrine()->getManager();
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
						'perfil' => $perfil, 'pendiente' => $pendientes, 'form' => $form->createView()));
		}
		else {

			$formData = Array('hojaVida' => null, 'tarjetaProfesional' => null);
			$form = $this->createFormBuilder($formData)
				->add('hojaVida', 'file', array('required' => false))
				->add('tarjetaProfesional', 'file', array('required' => false))
				->getForm();

			if ($request->getMethod() == 'POST')
			{
				$form->bind($request);
				if ($form->isvalid())
				{
					$dataForm = $form->getData();
					$errores = $this->validatePerfilEditMentor($dataForm);

					if ($errores == 0)
					{
						$em = $this->getDoctrine()->getManager();
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
							if ($usuario->GetUsuarioTarjetaProfesional()){
								$nameTP = $usuario->GetUsuarioTarjetaProfesional();
							}
							else {
								$stdate = strtotime(date('Y-m-d H:i:s'));
								$nameTP = 'tarjetaProfesional_'.$id.$stdate.'.pdf';
								$usuario->setUsuarioTarjetaProfesional($nameTP);
							}
							$form['tarjetaProfesional']->getData()->move($rutaTP, $nameTP);
						}

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
					'perfil' => $perfil, 'pendiente' => $pendientes, 'form' => $form->createView()));
		}
		
	}

	/**
	 * Sincronizar el perfil de linkedin con vocationet
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1 
	 * @Route("/sincronizar-perfil", name="perfil_sincronizar")
	 */
	 public function sincronizarAction()
	 {
		$security = $this->get('security');
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
	 * Funcion Privada de validación de formulario del usuario tipo mentor
	 * (hoja de vida y tarjeta profesional)
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Object Formulario a validar
     * @return Int Cantidad de errores encontrados
	 */
	private function validatePerfilEditMentor($dataForm)
	{
		/**
		 * Validacione aplicadas
		 * hojaVida => File(Formatos permitidos)
		 * tarjetaProfesional => File(Formatos permitidos)
		 */
		
		$File = new Assert\File(Array('mimeTypes' => Array("application/pdf")));

		$hojaVida = $dataForm['hojaVida'];
		$tarjetaProfesional = $dataForm['tarjetaProfesional'];

		$countErrores = 0;
		if (($hojaVida === null) && ($tarjetaProfesional === null)) {
			$countErrores = 1;
		}
		else
		{
			if ($hojaVida) {
				$countErrores += (count($this->get('validator')->validateValue($hojaVida, $File)) == 0) ? 0 : 1;
			}
			if ($tarjetaProfesional) {
				$countErrores += (count($this->get('validator')->validateValue($tarjetaProfesional, $File)) == 0) ? 0 : 1;
			}
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

}

	
