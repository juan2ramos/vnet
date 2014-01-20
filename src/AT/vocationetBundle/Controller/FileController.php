<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * controlador para el control de archivos
 *
 * @Route("/files")
 * @author Diego Malagón <diego@altactic.com>
 */
class FileController extends Controller
{
    /**
     * Accion para descargar un archivo
     * 
     * @Route("/download/{id}", name="download_file")
     * @return Response
     */
    public function downloadAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        
        $file = $this->get('file');
        
        $response = $file->download($id);
        
        return $response;
    }
}
?>
