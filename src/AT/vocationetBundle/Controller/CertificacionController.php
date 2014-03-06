<?php

namespace AT\vocationetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador de Certificación
 * @package vocationetBundle
 * @Route("/certificado")
 * @author Camilo Quijano <camilo@altactic.com>
 * @version 1
 */
class CertificacionController extends Controller
{
	/**
     * Certificado del estudiante
     * 
     * @Template("vocationetBundle:Certificacion:index.html.twig")
	 * @Route("/", name="certificado")
     * @Method("GET")
     * @return Response
     */
    public function indexAction()
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$usuario_id = $security->getSessionValue('id');
		
		// Validad linealidad en programa de orientación
        $productoPago = $this->get('pagos')->verificarPagoProducto($this->get('pagos')->getProductoId('programa_orientacion'), $usuario_id);
        if ($productoPago)
        {
			$return = $this->get('perfil')->validarPosicionActual($usuario_id, 'certificacion');
			if (!$return['status']) {
				$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("Acceso denegado"), "text" => $this->get('translator')->trans($return['message'])));
				return $this->redirect($this->generateUrl($return['redirect']));
			}
		}

		//$this->generarCertificado($usuario_id, $security->getSessionValue('usuarioApellido').' '.$security->getSessionValue('usuarioNombre')); // Generar certificacion
		$ruta_certificado = $security->getParameter('ruta_certificados').'user'.$usuario_id.'.png';
		$certificado = file_exists($ruta_certificado);

		if(!$certificado)
        {
            return $this->forward("vocationetBundle:Alerts:alertScreen", array(
                "title" => $this->get('translator')->trans("certificado.no.generado"),
                "message" => $this->get('translator')->trans("explicacion.certificado.no.generado")
            )); 
        }
		
        return array('certificado' => $certificado, 'ruta_certificado' => $ruta_certificado);
    }

	/**
     * Descargar Certificado del estudiante
     * 
	 * @Route("/download", name="download_certificado")
     * @Method("GET")
     * @return Response
     */
    public function descargarAction()
    {
		$security = $this->get('security');
		if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
		
		$ruta_certificado = $security->getParameter('ruta_certificados').'user'.$security->getSessionValue('id').'.png';
		$response = $this->get('file')->downloadCertificado($ruta_certificado);
        return $response;
	}

	/**
	 * Generación del Certificado del id del usuario que ingresa como parametro
	 * @param Int $usuario_id Id del usuario
	 * @param String $nombre_usuario Nombre del usuario 
	 */
    private function generarCertificado($usuario_id, $nombre_usuario)
    {
		$security = $this->get('security');
		$ruta_certificado = $security->getParameter('ruta_certificado_vacio');
		$ruta_certificado_user = $security->getParameter('ruta_certificados').'user'.$usuario_id.'.png';

		// Crear instancias de imagen base
		$origen = imagecreatefrompng($ruta_certificado); 
		$w = imagesx($origen); 
		$h = imagesy($origen);

		// Copiar Dimensiones y estructura de imagen Base
		$destino = imagecreatetruecolor($w, $h);
		imagecopy($destino, $origen, 0, 0, 0, 0, $w, $h);

		// Fuente para certificado y colores de texto
		$font = $security->getParameter('ruta_fuente');
		$white = imagecolorallocate($destino, 255, 255, 255);
		$black = imagecolorallocate($destino, 0, 0, 0);
		$green_certificate = imagecolorallocate($destino, 0, 188, 178);

		/**
		 * Creación del nombre del usuario a certificarse
		 * 
		 * @var $nombre_usuario = Texto 1 a agregar a la imagen (Nombre del usuario) - Ingresado por parametro
		 * @var $font_size = Tamaño del texto a agregar
		 * @var $bbox = imagettfbbox retorna tamaño del contenedor (Caja circundante) del texto a agregar teniendo en cuenta tamaño, fuente, y texto
		 * @var $w_img = Ancho del contenedor
		 * @var $y_img = Alto del contenedor
		 * @var $aux_widht_rest = Ancho disponible de la imagen (tenido en cuenta para centrar texto)
		 * @var $cord_x = Inicio de impresion del texto (Dividido en dos para centrar) EJE X
		 * @var $cord_y = Inicio de impresion del texto (Dividido en dos para centrar) EJE Y
		 * imagettftext incluye el texto en las cordenadas especificadas, tamaño, color (imagecolorallocate), fuente.
		 */
		$font_size = 30;
		$bbox = imagettfbbox($font_size, 0, $font, $nombre_usuario);
		$w_img = $bbox[2] - $bbox[0]; // Ancho
		$y_img = $bbox[1] - $bbox[5]; // Alto
		$aux_widht_rest = imagesx($destino) - $w_img;
		$cord_x = ($aux_widht_rest) / 2;
		$cord_y = ($h/4)*2.25;
		imagettftext($destino, $font_size, 0, $cord_x, $cord_y, $white, $font, $nombre_usuario);

		/**
		 * Creación de la fecha de expedición del ceritificado y cordenadas antiguos
		 * $aux_mes = $this->get('translator')->trans(date('F'), Array(), 'label');
		 * $fecha_expedicion = $this->get('translator')->trans('se.expide.en.bogota.el.%dia%.de.%mes%.de.%ano%', Array('%dia%'=>date('d') , '%mes%' => $aux_mes, '%ano%' => date('Y')), 'label');
		 * $cord_x = ($aux_widht_rest) / 2;
		 * $cord_y = ($h/4)*3;
		 */
		$fecha_expedicion = date('d/m/Y');
		$font_size = 15;
		$bbox = imagettfbbox($font_size, 0, $font, $fecha_expedicion);
		$w_img = $bbox[2] - $bbox[0]; // Ancho
		$y_img = $bbox[1] - $bbox[5]; // Alto
		$aux_widht_rest = imagesx($destino) - $w_img;
		$cord_x = (($aux_widht_rest) / 10)*9.1;
		$cord_y = ($h/7)*6.01;
		imagettftext($destino, $font_size, 0, $cord_x, $cord_y, $green_certificate, $font, $fecha_expedicion);

		// Guardar imagen y liberar espacio en memoria
		imagepng($destino, $ruta_certificado_user);
		imagedestroy($destino);
		imagedestroy($origen);
	}
}
