<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador de perfil de mentor
 * @package vocationetBundle
 * @Route("/vocation")
 */
class PerfilMentorController extends Controller
{
	/**
	 * @Route("/mentor", name="perfil_mentor")
	 * @Template("vocationetBundle:Default:index.html.twig")
	 */
    public function indexAction()
    {
		$name = 'rew';
        return $this->render('vocationetBundle:Default:index.html.twig', array('name' => $name));
    }
}
