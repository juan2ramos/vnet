<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para el registro nativo de la aplicacion
 *
 * @Route("/registro")
 * @author Diego Malagón <diego@altactic.com>
 */
class RegistroController extends Controller
{
    /**
     * Accion para el formulario de registro
     * 
     * @Route("/", name="registro")
     * @Template("vocationetBundle:Registro:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        
        return array();
    }
}
?>
