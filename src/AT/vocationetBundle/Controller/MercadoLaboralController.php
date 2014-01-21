<?php

namespace AT\vocationetBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AT\vocationetBundle\Entity\AlternativasEstudios;
use AT\vocationetBundle\Entity\Participaciones;

/**
 * Controlador de mercado laboral
 * @package vocationetBundle
 * @Route("/mercadolaboral")
 * @author Camilo Quijano <camilo@altactic.com>
 * @version 1
 */
class MercadoLaboralController extends Controller
{
    /**
     * Index de mercado laboral
     * 
     * @Route("/", name="mercado_laboral")
     * @Template("vocationetBundle:MercadoLaboral:index.html.twig")
     * @param Request $request Request enviado con alternativas de estudio seleccionadas
     * @Method({"GET", "POST"})
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
		//if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$usuarioId = $security->getSessionValue('id');
        
        // Verificar pago
        $pago = $this->verificarPago($usuarioId);        
        if(!$pago)
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("no.existe.pago"), "text" => $this->get('translator')->trans("antes.de.continuar.debes.realizar.el.pago")));
            return $this->redirect($this->generateUrl('planes'));
        } 
        
		$em = $this->getDoctrine()->getManager();
        $seleccionarMentor = $this->get('perfil')->confirmarMentorOrientacionVocacional($usuarioId);

        if(!$seleccionarMentor) {
			$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("Acceso denegado"), "text" => $this->get('translator')->trans("no.ha.seleccionado.mentor.ov")));
			return $this->redirect($this->generateUrl('lista_mentores_ov'));
		}

		$archivoCargado = false;
		$rutaInformeML = false;
        $carreras = false;

		$form_id = $this->get('formularios')->getFormId('mercado_laboral');
		$formulario = $this->get('formularios')->getInfoFormulario($form_id);

		$pr = $this->get('perfil');
		$alternativasEstudio = $pr->getAlternativasEstudio($usuarioId);
		if ($alternativasEstudio)
		{
			// Validación de si ya se ha subido informe relacionado con el listado de carreras seleccionadas por el usuario
			$rutaInformeML = $security->getParameter('ruta_files_mercado_laboral').'user'.$usuarioId.'.pdf';
			$archivoCargado = file_exists($rutaInformeML);
		}
		else {

			// En el caso de que NO tenga alternativas de estudio registradas se enlistan carreras para seleccionarlas
			$carreras = $em->getRepository('vocationetBundle:Carreras')->findAll();
			
			if ($request->getMethod() == "POST") {
				$errorCantidad = false;
				$errorAlternativa = false;
				$alternativas = $request->request->get('carrerasSeleccionadas');
				if ((count($alternativas)>0) && (count($alternativas)<=3)) {
					$usuario = $em->getRepository('vocationetBundle:Usuarios')->findOneById($usuarioId);

					$auxAlternativas = '<br>';
					foreach ($alternativas as $alt) {
						$carrera = $em->getRepository('vocationetBundle:Carreras')->findOneById($alt);
						if ($carrera) {
							$newAE = new AlternativasEstudios();
							$newAE->setCarrera($carrera);
							$newAE->setUsuario($usuario);
							$em->persist($newAE);

							$auxAlternativas .= '<br> - '.$carrera->getNombre();
						} else {
							$errorAlternativa = true;
						}
					}

					//Registro de que el usuario participo en el mercado laboral
					$participacion = new Participaciones();
					$participacion->setFormulario($form_id);
					$participacion->setFecha(new \DateTime());
					$participacion->setUsuarioParticipa($usuarioId);
					$participacion->setUsuarioEvaluado($usuarioId);
					$participacion->setEstado(1);
					$em->persist($participacion);
					$em->flush();

					//Notificacion para mentor de que el estudiante  ha seleccionado alternativas de estudio
					$name = $security->getSessionValue('usuarioNombre').' '.$security->getSessionValue('usuarioApellido');
					$dias = $security->getParameter('dias_habiles_informe_mercado_laboral');
					$asunto = $this->get('translator')->trans('%name%.mail.mentor.estudiante.selecciona.alternativas', Array('%name%'=>$name), 'mail');
					$message = $this->get('translator')->trans('%name%.ha.seleccionado%dias%.%alternativas%', Array('%name%'=>$name, '%alternativas%'=>$auxAlternativas, '%dias%'=>$dias), 'mail');
					$this->get('mensajes')->enviarMensaje($usuarioId, Array($seleccionarMentor['id']), $asunto, $message);
					
					// Notificacion a estudiante de que el mentor tendra X tiempo para responder
					$asunto = $this->get('translator')->trans('asunto.mail.mentor.informa.tiempo.espera', Array(), 'mail');
					$message = $this->get('translator')->trans('mentor.tiene.%dias%.dias.para.informe.de.alternativas.seleccionadas', Array('%dias%'=>$dias), 'mail');
					$this->get('mensajes')->enviarMensaje($seleccionarMentor['id'], Array($usuarioId), $asunto, $message);
				}
				else {
					$errorCantidad = true;
				}

				if ($errorCantidad) {
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.los datos.suministrados")));
				}
				if ($errorAlternativa) {
					$this->get('session')->getFlashBag()->add('alerts', array("type" => "warning", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("ocurrio.error.al.registrar.alternativa.de.estudio")));
				}
				return $this->redirect($this->generateUrl('mercado_laboral'));
			}
		}

        return array(
			'formulario_info' => $formulario,
			'carreras' => $carreras,
			'altEstudio' => $alternativasEstudio,
			'archivoCargado' => $archivoCargado,
			'rutaInformeML' => $rutaInformeML,
        );
    }
    
    /**
     * Funcion que verifica el pago para test vocacional
     * 
     * @param integer $usuarioId id de usuario
     * @return boolean
     */
    private function verificarPago($usuarioId)
    {
        $pagoCompleto = $this->get('pagos')->verificarPagoProducto($this->get('pagos')->getProductoId('programa_orientacion'), $usuarioId);
        
        if($pagoCompleto)
        {
            return true;
        }
        else
        {
            $productoId = $this->get('pagos')->getProductoId('informe_mercado_laboral'); // Producto: informe de mercado laboral
            $pagoIndividual = $this->get('pagos')->verificarPagoProducto($productoId, $usuarioId);
            
            if($pagoIndividual)
                return true;
            else
                return false;
        }
    }
}
?>
