<?php

namespace AT\vocationetBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controlador de Administración por parte del mentor
 * @package vocationetBundle
 * @Route("/adminmentor")
 * @author Camilo Quijano <camilo@altactic.com>
 * @version 1
 */
class AdminMentorController extends Controller
{
    /**
     * Listado de usuarios que han seleccionado al usuario logeado como mentor de Orientacion Vocacional
     * 
     * @Route("/usuariosmentor", name="lista_usuarios_mentor")
     * @Template("vocationetBundle:AdminMentor:usuarios.html.twig")
     * @Method("GET")
     * @author Camilo Quijano <camilo@altactic.com>
     * @return Response
     */
    public function UsuariosMentoresVocacionalAction()
    {
		$security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}

		$rolId = $security->getSessionValue('rolId');
		if ($rolId != 3) {	throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado", array(), 'label'));	}

		$usuarioId = $security->getSessionValue('id');
        $contactos = $this->getUsuariosMentor($usuarioId);
        return array('contactos' => $contactos);
    }

    /**
     * Listado de pruebas por usuario
     * 
     * @Route("/pruebasusuario/{id}", name="pruebas_usuarios_mentor")
     * @Template("vocationetBundle:AdminMentor:pruebasUsuario.html.twig")
     * @author Diego Malagon <diego@altactic.com>
     * @param integer $id id de usuario
     * @return Response
     */
    public function pruebasUsuarioAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $FormServ = $this->get('formularios');
        $usuarioId = $security->getSessionValue('id');
        $rolId = $security->getSessionValue('rolId');
        
        // Valida acceso del mentor
        if(!$FormServ->validateAccesoMentor($usuarioId, $rolId, $id)) throw $this->createNotFoundException();        
        
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository("vocationetBundle:Usuarios")->findOneById($id);
        $participaciones = $this->getParticipacionesUsuario($id);
        $form_ids = $this->get('formularios')->getFormId();
                 
        //Verificar si ya existe el certificado
        $certificado_cargado = $FormServ->verificarCertificado($id);
        
        return array(
            'usuario'           => $usuario,
            'participaciones'   => $participaciones,
            'form_ids'          => $form_ids,
            'certificado'       => $certificado_cargado
        );
    }
    
    /**
     * Listado de carreras elegidas por el usuario
     * 
     * @Route("/carreras/{id}", name="alternativas_estudio_estudiante")
     * @author Diego Malagon <diego@altactic.com>
     * @param integer $id id de usuario
     * @return Response
     */
    public function carrerasEstudianteAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return new Response(json_encode(array("status" => "error", "message" => array("title" => 'authentication failed', "detail" => 'authentication failed'))));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ return new Response(json_encode(array("status" => "error", "message" => array("title" => 'authorization failed', "detail" => 'authorization failed'))));}
        
        $FormServ = $this->get('formularios');
        $usuarioId = $security->getSessionValue('id');
        $rolId = $security->getSessionValue('rolId');
        
        // Valida acceso del mentor
        if(!$FormServ->validateAccesoMentor($usuarioId, $rolId, $id)) return new Response(json_encode(array("status" => "error", "message" => array("title" => 'authorization failed', "detail" => 'authorization failed'))));
        
        $carreras = $this->getAlternativasEstudio($id);
        
        return new Response(json_encode($carreras));
    }
    
    /**
     * Listado de mentorias separadas por el usuario
     * 
     * @Route("/mentorias/{id}", name="estado_mentorias_estudiante")
     * @author Diego Malagon <diego@altactic.com>
     * @param integer $id id de usuario
     * @return Response JSON
     */
    public function mentoriasEstudianteAction($id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return new Response(json_encode(array("status" => "error", "message" => array("title" => 'authentication failed', "detail" => 'authentication failed'))));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ return new Response(json_encode(array("status" => "error", "message" => array("title" => 'authorization failed', "detail" => 'authorization failed'))));}
        
        $FormServ = $this->get('formularios');
        $usuarioId = $security->getSessionValue('id');
        $rolId = $security->getSessionValue('rolId');
        
        // Valida acceso del mentor
        if(!$FormServ->validateAccesoMentor($usuarioId, $rolId, $id)) return new Response(json_encode(array("status" => "error", "message" => array("title" => 'authorization failed', "detail" => 'authorization failed'))));
        
        $mentorias = $this->getMentoriasEstudiante($id, $usuarioId);
        
        return new Response(json_encode($mentorias));
    }
    
    /**
     * Accion para la carga de reportes de pruebas
     * 
     * @Route("/uploadreporte/{id}", name="upload_reporte_prueba")
     * @Method({"post"})
     * @author Diego Malagon <diego@altactic.com>
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return Response
     */
    public function uploadReporteAction(Request $request, $id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return $this->redirect($this->generateUrl('login'));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ throw $this->createNotFoundException($this->get('translator')->trans("Acceso denegado"));}
        
        $FormServ = $this->get('formularios');
        $usuarioId = $security->getSessionValue('id');
        $rolId = $security->getSessionValue('rolId');
        
        // Valida acceso del mentor
        if(!$FormServ->validateAccesoMentor($usuarioId, $rolId, $id)) return new Response(json_encode(array("status" => "error", "message" => array("title" => 'authorization failed', "detail" => 'authorization failed'))));
        
        $form_id = $request->request->get('form');
        $archivo = $request->files->get('file');
        
        if($archivo)
        {        
            $ext = $archivo->guessExtension();

            if(strtolower($ext) == 'pdf')
            {
                $filename = md5($id.$form_id).'.'.$ext;
                $pathname = $security->getParameter('path_reportes');

                // Cargar archivo a carpeta de reportes
                $archivo->move($pathname, $filename);

                // Registrar ruta a la participacion en base de datos
                $this->updateArchivoReporte($id, $form_id, $filename);


                $this->get('session')->getFlashBag()->add('alerts', array("type" => "success", "title" => $this->get('translator')->trans("informe.cargado"), "text" => $this->get('translator')->trans("informe.cargado.correctamente")));
            }
            else
            {
                $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("extension.invalida")));
            }
        }
        else
        {
            $this->get('session')->getFlashBag()->add('alerts', array("type" => "error", "title" => $this->get('translator')->trans("datos.invalidos"), "text" => $this->get('translator')->trans("verifique.datos.ingresados")));            
        }
        
        return $this->redirect($this->generateUrl('pruebas_usuarios_mentor', array('id' => $id)));
    }
    
    /**
     * Accion para aprobar una prueba de un estudiante
     * 
     * @Route("/aprobar/{id}", name="aprobar_prueba")
     * @Method({"post"})
     * @author Diego Malagon <diego@altactic.com>
     * @param integer $id id de usuario
     * @return Response JSON
     */
    public function aprobarPruebaAction(Request $request, $id)
    {
        $security = $this->get('security');
        if(!$security->authentication()){ return new Response(json_encode(array("status" => "error", "message" => array("title" => 'authentication failed', "detail" => 'authentication failed'))));} 
        if(!$security->authorization($this->getRequest()->get('_route'))){ return new Response(json_encode(array("status" => "error", "message" => array("title" => 'authorization failed', "detail" => 'authorization failed'))));}
        
        $FormServ = $this->get('formularios');
        $usuarioId = $security->getSessionValue('id');
        $rolId = $security->getSessionValue('rolId');
        
        // Valida acceso del mentor
        if(!$FormServ->validateAccesoMentor($usuarioId, $rolId, $id)) return new Response(json_encode(array("status" => "error", "message" => array("title" => 'authorization failed', "detail" => 'authorization failed'))));

        $form_id = $request->get('form_id');
        
        $update = $this->aprobarPrueba($id, $form_id);
        
        if($update >= 1)
        {
            $response = array(
                "status" => "success", 
                "message" => array(
                    "title" => $this->get('translator')->trans("prueba.aprobada"), 
                    "detail" => $this->get('translator')->trans("prueba.aprobada.correctamente")
                )
            );           
        }
        else
        {
            $response = array(
                "status" => "error", 
                "message" => array(
                    "title" => $this->get('translator')->trans("error.aprobar"), 
                    "detail" => $this->get('translator')->trans("ocurrio.error.aprobar.prueba")
                )
            ); 
        }
        
        return new Response(json_encode($response));
    }
    
	/**
     * Listado de usuarios que han seleccionado al usuario logeado como mentor de Orientacion Vocacional
     *
     * @author Diego Malagon <diego@altactic.com>
     * @param Int $usuarioId Id del usuario a consultarle las amistades
     * @return Array Arreglo de usuarios
     */
    private function getUsuariosMentor($usuarioId)
    {
		$em = $this->getDoctrine()->getManager();
		
        /**
         * SELECT * FROM
         * relaciones r
         * JOIN usuarios u ON r.usuario_id = u.id OR r.usuario2_id = u.id
         * WHERE 
         * (r.usuario_id = 3 OR r.usuario2_id = 3)
         * AND u.id != 3
         * AND r.tipo = 2
         * AND r.estado = 1
         */
        $dql = "SELECT 
                    u.id, u.usuarioNombre, u.usuarioApellido, u.usuarioImagen, u.usuarioCursoActual,
                    c.nombre AS nombreColegio
                FROM 
                    vocationetBundle:Relaciones r
                    JOIN vocationetBundle:Usuarios u WITH r.usuario = u.id OR r.usuario2 = u.id
                    LEFT JOIN u.colegio c       
                WHERE 
                    (r.usuario = :usuarioId OR r.usuario2 = :usuarioId)
                    AND u.id != :usuarioId
                    AND r.tipo = 2 
                    AND r.estado = 1";
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $estudiantes = $query->getResult();
                
		return $estudiantes;
	}
    
    /**
     * Funcion que obtiene las participaciones de un usuario
     * 
     * @author Diego Malagón <diego@altactic.com>
     * @param integer $usuarioId
     * @return array arreglo de participaciones
     */
    private function getParticipacionesUsuario($usuarioId)
    {
        $FormServ = $this->get('formularios');
        
        // Query
        $dql = "SELECT  
                    f.id, 
                    f.nombre, 
                    MAX(p.estado) AS estado,
                    COUNT(f.id) AS cant,
                    MAX(p.archivoReporte) AS reporte
                FROM 
                    vocationetBundle:Participaciones p
                    JOIN vocationetBundle:Formularios f WITH f.id = p.formulario
                WHERE
                    p.usuarioEvaluado = :usuarioId
                GROUP BY f.id ";
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $result = $query->getResult();        
        
        // Organizar arreglo con id de formulario por key
        $participaciones = array();
        foreach($result as $r)
        {
            $participaciones[$FormServ->getFormName($r['id'])] = $r;
        }
        
        
        return $participaciones;
    }
    
    /**
     * Funcion que obtiene las alternativas de estudio del usuario
     * 
     * @author Diego Malagon <diego@altactic.com>
     * @param integer $usuario_id id de usuario
     * @return array arreglo de carreras
     */
    private function getAlternativasEstudio($usuario_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $dql = "SELECT c.nombre FROM vocationetBundle:AlternativasEstudios e
                JOIN vocationetBundle:Carreras c WITH c.id = e.carrera
                WHERE e.usuario = :usuario_id";
        
        $query = $em->createQuery($dql);
        $query->setParameter('usuario_id', $usuario_id);
        $result = $query->getResult();
        
        return $result;
    } 
    
    /**
     * Funcion para obtener las mentorias programadas por el usuario con mentores diferentes al orientador vocacional
     * 
     * @author Diego Malagon <diego@altactic.com>
     * @param integer $estudiante_id id de usuario estudiante
     * @param integer $mentor_id id de usuario mentor
     * @return array 
     */
    private function getMentoriasEstudiante($estudiante_id, $mentor_id)
    {
        $dql = "SELECT 
                    u.usuarioNombre,
                    u.usuarioApellido,
                    m.mentoriaInicio,
                    m.mentoriaEstado
                FROM 
                    vocationetBundle:Mentorias m
                    JOIN vocationetBundle:Usuarios u WITH m.usuarioMentor = u.id
                WHERE
                    m.usuarioEstudiante = :estudiante_id
                    AND m.usuarioMentor != :mentor_id";
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('estudiante_id', $estudiante_id);
        $query->setParameter('mentor_id', $mentor_id);        
        $result = $query->getResult();
        
        return $result;
    }
    
    /**
     * Funcion que actualiza el campo de reporte de una participacion de estudiante
     * 
     * Ingresa la ruta del archivo cargado en el campo de archivo_reporte de la participacion
     * del estudiante en una prueba. si la prueba es test vocacional hace un insert ya que esta prueba
     * no registra participacion por parte del estudiante. Para los demas casos realiza un update 
     * a traves de la funcion updateParticipacion
     * 
     * @author Diego Malagon <diego@altactic.com>
     * @param integer $estudiante_id id de usuario estudiante
     * @param integer $form_id id de formulario o prueba
     * @param string $path ruta del archivo
     */
    private function updateArchivoReporte($estudiante_id, $form_id, $path)
    {
        $FormServ = $this->get('formularios');
        $em = $this->getDoctrine()->getManager();
        
        // Si es para la prueba de test vocacional
        if($FormServ->getFormId('test_vocacional') == $form_id)
        {
            // Verificar si ya existe un registro
            $dql = "SELECT COUNT(p.id) AS c 
                    FROM vocationetBundle:Participaciones p
                    WHERE 
                        p.usuarioEvaluado = :usuario_id
                        AND p.formulario = :formulario_id";
            $query = $em->createQuery($dql);
            $query->setParameter('usuario_id', $estudiante_id);
            $query->setParameter('formulario_id', $form_id);
            $result = $query->getResult();
            
            $count = $result[0]['c'];
            
            // Si no existe crearlo
            if($count == 0)
            {
                // Insertar nueva participacion para test vocacional
                $participacion = new \AT\vocationetBundle\Entity\Participaciones();

                $participacion->setFormulario($form_id);
                $participacion->setFecha(new \DateTime());
                $participacion->setUsuarioParticipa($estudiante_id);
                $participacion->setUsuarioEvaluado($estudiante_id);
                $participacion->setArchivoReporte($path);            
                $participacion->setEstado(1);

                $em->persist($participacion);

                $em->flush();
            }
            else // Si existe seguir el flujo normal
            {
                // Actualizar registro de participacion
                $this->updateParticipacion($estudiante_id, $form_id, $path);
            }
        }
        else
        {
            // Actualizar registro de participacion
            $this->updateParticipacion($estudiante_id, $form_id, $path);
        }
    }
    
    /**
     * Funcion que ejecuta el update para el campo de reportes de participaciones
     * 
     * @author Diego Malagon <diego@altactic.com>
     * @param integer $estudiante_id id de usuario estudiante
     * @param integer $form_id id de formulario o prueba
     * @param string $path ruta del archivo
     * @return integer total de registros actualizados
     */
    private function updateParticipacion($estudiante_id, $form_id, $path)
    {
        $em = $this->getDoctrine()->getManager();
        
        $dql = "UPDATE 
                    vocationetBundle:Participaciones p
                SET 
                    p.archivoReporte = :path
                WHERE 
                    p.usuarioEvaluado = :usuario_id
                    AND p.formulario = :formulario_id";
        
        $query = $em->createQuery($dql);
        $query->setParameter('path', $path);
        $query->setParameter('usuario_id', $estudiante_id);
        $query->setParameter('formulario_id', $form_id);
        $query->setMaxResults(1);
        $result = $query->getResult();
              
        
        return $result;
    }
    
    /**
     * Funcion que actualiza el estado de una participacion de un usuario
     * 
     * @author Diego Malagon <diego@altactic.com>
     * @param integer $estudiante_id id de usuario estudiante
     * @param integer $form_id id de formulario
     * @return integer total de registros actualizados
     */
    private function aprobarPrueba($estudiante_id, $form_id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $dql = "UPDATE 
                    vocationetBundle:Participaciones p
                SET 
                    p.estado = :estado
                WHERE 
                    p.usuarioEvaluado = :usuario_id
                    AND p.formulario = :formulario_id";
        
        $query = $em->createQuery($dql);
        $query->setParameter('estado', 2);
        $query->setParameter('usuario_id', $estudiante_id);
        $query->setParameter('formulario_id', $form_id);
        $result = $query->getResult();
              
        
        return $result;
    }
}
?>
