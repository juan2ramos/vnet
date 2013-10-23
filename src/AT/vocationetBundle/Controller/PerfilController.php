<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
//use AT\CuentaBundle\Entity\AtPlugins;

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
		//$perfilId = 2;
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
			// ROL ESTUDIANTE
			return $this->render('vocationetBundle:Perfil:perfilestudiante.html.twig', array(
						'perfil' => $perfil ));
		}
    }

	

	
}
