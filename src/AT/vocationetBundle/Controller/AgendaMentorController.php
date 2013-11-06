<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para la agenda de mentor
 *
 * @Route("/agenda_mentor")
 * @author Diego Malagón <diego@altactic.com>
 */
class AgendaMentorController extends Controller
{
    /**
     * Index de la agenda de mentor
     * 
     * @Route("/", name="agenda_mentor")
     * @Template("vocationetBundle:AgendaMentor:index.html.twig")
     * @return Response
     */
    public function indexAction()
    {
        return array();
    }
}
?>
