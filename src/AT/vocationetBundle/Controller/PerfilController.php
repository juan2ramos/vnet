<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
//use AT\CuentaBundle\Entity\AtPlugins;
use Symfony\Component\Validator\Constraints as Assert;
//use Symfony\Component\Validator\Mapping\ClassMetadata;
//use Symfony\Component\Validator\Constraints\NotBlank;
//use Symfony\Component\Validator\Constraints\Regex;
//use Symfony\Component\Validator\Constraints\File;

/**
 * Controlador de perfil de usuarios de vocationet
 * @package vocationetBundle
 * @Route("/vocation")
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
	 * @Route("/{perfilId}/perfil", name="perfil")
	 * @Method("GET")
	 * @return Render Vista renderizada del perfil
	 * @throws createNotFoundException Si el perfil al que se quiere acceder no existe 
	 */
    public function indexAction($perfilId)
    {
		$tr = $this->get('translator');
		$pr = $this->get('perfil');
		error_reporting(true);

		$perfil = $pr->getPerfil($perfilId);
		
		if (!$perfil) {
			throw $this->createNotFoundException($tr->trans("mentor.no.existe"));
		}
 
		if ($perfil['nombreRol'] == 'mentor_e' or $perfil['nombreRol'] == 'mentor_ov')
		{
			// ROL MENTOR
			$estudios = $pr->getEstudiosPerfil($perfilId);
			$trabajos = $pr->getTrabajosPerfil($perfilId);
			return $this->render('vocationetBundle:Perfil:perfilmentor.html.twig', array(
						'perfil' => $perfil, 'estudios' => $estudios, 'trabajos' => $trabajos ));
		}
		else
		{
			$fechaactual = strtotime(date('Y-m-d H:i:s'));
			$fechaplaneada = $fechaactual+1000000;
			$tiempoRestante = ($fechaplaneada - $fechaactual) / 100000;
			//progress-bar-success - verde , progress-bar-warning - amarillo, progress-bar-danger - rojo
			$semaforo = Array('porcentaje' =>20, 'color'=>'progress-bar-danger');  //porcentaje avance
			$avancePrograma = Array('porcentaje' =>60, 'color'=>'progress-bar-warning');  //porcentaje avance
			$vancesDiagnostico = Array('mitdc'=> 50, 'hyp' => 60, 'info' => 40, 'invest' => 80, 'dc' => 10 );
			
			$pendientes = Array(
				'fechaPlaneada' => $fechaplaneada,
				'tiempoRestante' => $tiempoRestante,
				'semaforo' => $semaforo,
				'msjsinleer' => 10,
				'avancePrograma' => $avancePrograma,
				'avanceDiagnostico' => $vancesDiagnostico,
				'nivel' => 3,
			);
			// ROL ESTUDIANTE
			return $this->render('vocationetBundle:Perfil:perfilestudiante.html.twig', array(
						'perfil' => $perfil, 'pendiente' => $pendientes ));
		}
    }


	/**
	 * Editar perfil de usuario
	 *
	 * En este action el usuario (estudiante) modifica su informacion personal, como
	 * colegio, grado, nombres, apellidos, fecha de nacimiento, genero, imagen (Si no esta logeado con facebook), etc.
	 * - Exclusivo para estudiantes
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @Template("vocationetBundle:Perfil:editperfil.html.twig")
	 * @Route("/edit-perfil", name="perfil_edit")
	 * @param Request Form edicion
     * 
	 */
	public function editAction(Request $request)
	{
		$perfilId=1;
		
		$pr = $this->get('perfil');
		$tr = $this->get('translator');
		$perfil = $pr->getPerfil($perfilId);

		
		
		if (!$perfil) {
			throw $this->createNotFoundException($tr->trans("mentor.no.existe"));
		}

		$generoAct = 'masculino';
		$generos = Array('masculino' => $tr->trans("masculino"), 'femenino' => $tr->trans("femenino"));

		$formData = Array('nombre'=> '');
		$form = $this->createFormBuilder($formData)
           ->add('nombre', 'text', array('required' => true, 'attr' => Array('pattern' => '^[a-zA-Z ]*$')))
           ->add('apellido', 'text', array('required' => true, 'attr' => Array('pattern' => '^[a-zA-Z ]*$')))
           ->add('fechaNacimiento', 'text', array('required' => false))
           ->add('genero', 'choice', array('choices'  => $generos,  'preferred_choices' => array($generoAct), 'required' => true))
           ->add('imagen', 'file', array('required' => false))
           
           ->getForm();

			


        if ($request->getMethod() == 'POST')
        {
			//$assert = new Assert();

			
			
			$form->bind($request);
			if ($form->isvalid())
			{
				$COUNTERRORS = 0;
				$dataForm = $form->getData();

				$errores = $this->validatePerfilEdit($dataForm);

				if ($errores > 0)
				{

				}
				//$form->
				
				

				if ($COUNTERRORS == 0) {
					print('ok, ok');
					//print_r($errorList);
				}
				else {
					print('errror');
					
				}
				print($COUNTERRORS);
				$fechanacimiento = $dataForm['fechaNacimiento'];
				print($fechanacimiento);
				
			
				
			}
			//return $this->redirect($this->generateUrl('perfil', array('perfilId'=> $perfilId)));
		}
        

		$pendientes = Array(
			'msjsinleer' => 10,
		);
		
		return array('perfil' => $perfil, 'pendiente' => $pendientes, 'form' => $form->createView(), );
	}

	private function validatePerfilEdit($dataForm)
	{
		$nombre = $dataForm['nombre'];
		$apellido = $dataForm['apellido'];
		$imagen = $dataForm['imagen'];
		$fechanacimiento = $dataForm['fechaNacimiento'];

		$NotBlank = new Assert\NotBlank();
		$Regex = new Assert\Regex(Array('pattern'=>'/^[a-zA-Z ]*$/')); //Validacion para solo nombres (letras y espacios)
		$File = new Assert\File(Array('mimeTypes' => Array("image/jpg", "image/jpeg", "image/png")));// Formatos permitidos
		$Date = new Assert\Date();

		$countErrores = 0;
		$countErrores += (count($this->get('validator')->validateValue($nombre, $NotBlank)) == 0) ? 0 : 1;
		$countErrores += (count($this->get('validator')->validateValue($nombre, $Regex)) == 0) ? 0 : 1;
		$countErrores += (count($this->get('validator')->validateValue($apellido, $NotBlank)) == 0) ? 0 : 1;
		if ($imagen){
			$countErrores += (count($this->get('validator')->validateValue($imagen, $File)) == 0) ? 0 : 1;
		}
		if ($fechanacimiento) {
			$countErrores += (count($this->get('validator')->validateValue($fechanacimiento, $Date)) == 0) ? 0 : 1;
		}
		return $countErrores;


//@ORM\Column(name="proyecto_img", type="string", length=100, nullable=true)
	 //* @Assert\File(maxSize="2M", mimeTypes={"image/jpg", "image/jpeg", "image/png"})
		//@Assert\Regex(pattern="/^[a-zA-Z0-9-.]+\.[A-Za-z]{2,4}$/")
	}

	
}
