<?php
namespace AT\vocationetBundle\Services;
use AT\vocationetBundle\Entity\Estudios;
use AT\vocationetBundle\Entity\Empresas;
use AT\vocationetBundle\Entity\Trabajos;

/**
 * Servicio para el manejo de perfiles
 * @author Camilo Quijano <camilo@altactic.com>
 * @package Services
 */
class PerfilService
{
    var $doctrine;
    var $translate;
        
    function __construct($doctrine, $translate)
    {
        $this->doctrine = $doctrine;
        $this->translate = $translate;
    }

    /**
	 * Funcion que trae la información del usuario del perfil al que se quiere acceder
	 * incluido colegio al que pertenece de ser rol estudiante
	 * - Acceso desde PerfilController
	 * 
     * @version 1
     * @param Int $perfilId Id del perfil al acceder
     * @return Array Arreglo con informacion del perfil
	 */
    public function getPerfil($perfilId)
    {
		/**
		 * @var Consulta SQL que trae los datos del perfil al que se quiere ingresar (mentor)
		 * SELECT u.id, p.id, r.nombre, u.usuario_nombre, u.usuario_apellido 
		 * FROM perfiles p
		 * JOIN usuarios u ON u.id = p.usuario_id
		 * JOIN roles r ON r.id = u.rol_id
		 * WHERE p.id = 2
		 * //AND (r.nombre = 'mentor_e' or r.nombre = 'mentor_ov');
		 */
		$dql="SELECT u.id as usuarioId, u.usuarioNombre, u.usuarioApellido, u.usuarioImagen, u.usuarioFechaNacimiento, u.usuarioEmail,
				u.usuarioHojaVida, u.usuarioTarjetaProfesional, u.usuarioValorMentoria, u.usuarioPerfilProfesional, u.usuarioPuntos,
				u.usuarioCursoActual, u.usuarioFacebookid, u.usuarioFechaPlaneacion, u.usuarioGenero, u.usuarioRolEstado, u.usuarioProfesion,
				r.id AS rolId, r.nombre as nombreRol,
				col.nombre as nombreCol, col.id as colegioId
			FROM vocationetBundle:Usuarios u
			JOIN u.rol r
			LEFT JOIN u.colegio col
			WHERE u.id =:perfilId";
		$em = $this->doctrine->getManager();
		$query = $em->createQuery($dql);
		$query->setParameter('perfilId', $perfilId);
		$perfil =$query->getResult();
		if($perfil) {
			return $perfil[0];
		} else {
			return false;
		}
	}

	/**
	 * Funcion que trae los estudios registrados del perfil
	 * - Estudios de rol mentor vocacional o experto
	 * - Acceso desde PerfilController
	 *
     * @version 1
	 * @param Int $usuarioId Id del perfil al acceder
	 * @return Array Arreglo con estudios registrados del perfil
	 */
	public function getEstudiosPerfil($usuarioId)
	{
		/**
		 * @var Consulta SQL que elimina los estudios del usuario que ingresa por parametro
		 * SELECT * FROM estudios e
		 * WHERE e.usuario_id = 2;
		 */
		 $dql= "SELECT est FROM vocationetBundle:Estudios est
			WHERE est.usuario =:usuarioId";
		$em = $this->doctrine->getManager();
		$query = $em->createQuery($dql);
		$query->setParameter('usuarioId', $usuarioId);
		$estudios =$query->getResult();
		return $estudios;
	}
	
	/**
	 * Funcion que elimina los estudios registrados del usuario que ingresa por parametro
	 * - Acceso desde PerfilController
	 *
     * @version 1
	 * @param Int $usuarioId Id del perfil a eliminar los estudios
	 */
	public function deleteEstudiosPerfil($usuarioId)
	{
		/**
		 * @var Consulta SQL que trae los estudios del perfil que ingresa por Id
		 * DELETE FROM estudios e WHERE e.usuario_id = 2;
		*/
		$em = $this->doctrine->getManager();
		$dql= "DELETE FROM vocationetBundle:Estudios est WHERE est.usuario =:usuarioId";
		$query = $em->createQuery($dql);
		$query->setParameter('usuarioId', $usuarioId);
		$query->getResult();
	}

	/**
	 * Funcion que guarda los estudios de un usuario
	 * 
	 * Claves del array que ingresa por cada estudio
	 * $estudios[] = Array('id','institucion','notas','actividades','titulo','estudios', 'yearinicio','yearfin')
	 * - Acceso desde PerfilController
	 *
     * @version 1
	 * @param Int $usuarioId Id del perfil a relacionar los estudios
	 * @param Array $estudios Arreglo con estudios a vincular al usuario.
	 */
	public function setEstudiosPerfil($estudios, $usuarioId)
	{
		$em = $this->doctrine->getManager();
		$Objusuario = $em->getRepository('vocationetBundle:Usuarios')->findOneById($usuarioId);
		foreach($estudios as $estudio)
		{
			$newEstudio = new Estudios();
			$newEstudio->setNombreInstitucion($estudio['institucion']);
			$newEstudio->setUsuario($Objusuario);
			$newEstudio->setTitulo($estudio['titulo']);
			$newEstudio->setNotas($estudio['notas']);
			$newEstudio->setCampo($estudio['estudios']);
			$newEstudio->setActividad($estudio['actividades']);
			$newEstudio->setIdLinkedin($estudio['id']);
			if ($estudio['yearinicio']) {
				$newEstudio->setFechaInicio(new \DateTime($estudio['yearinicio'].'-01-01'));
			}
			if ($estudio['yearfin']) {
				$newEstudio->setFechaFinal(new \DateTime($estudio['yearfin'].'-01-01'));
			}
			$em->persist($newEstudio);
		}
		$em->flush();
	}

	/**
	 * Funcion que trae los trabajos registrados del perfil
	 * - Trabajos del rol mentor vocacional o experto
	 * - Acceso desde PerfilController
	 *
     * @version 1
	 * @param Int $perfilId Id del perfil al acceder
	 * @return Array Arreglo con trabajos registrados del perfil
	 */
	public function getTrabajosPerfil($usuarioId)
	{
		/**
		 * @var Consulta SQL que trae los trabajos del perfil que ingresa por Id
		 * SELECT * FROM trabajos tra
		 * INNER JOIN empresas emp ON tra.empresa_id = emp.id
		 * WHERE tra.usuario_id = 2;
		 */
		$em = $this->doctrine->getManager();
		$dql= "SELECT tra FROM vocationetBundle:Trabajos tra
			INNER JOIN tra.empresa emp
			WHERE tra.usuario =:usuarioId";
		$query = $em->createQuery($dql);
		$query->setParameter('usuarioId', $usuarioId);
		$estudios =$query->getResult();
		return $estudios;
	}

	/**
	 * Funcion que elimina los trabajos registrados del usuario que ingresa por parametro
	 * - Acceso desde PerfilController
	 *
     * @version 1
	 * @param Int $usuarioId Id del usuario a eliminar los trabajos
	 */
	public function deleteTrabajosPerfil($usuarioId)
	{
		/**
		 * @var Consulta SQL que elimina los trabajos del usuario que ingresa por parametro
		 * DELETE FROM trabajos tra WHERE tra.usuario_id = 2;
		 */
		$em = $this->doctrine->getManager();
		$dql= "DELETE FROM vocationetBundle:Trabajos tra WHERE tra.usuario =:usuarioId";
		$query = $em->createQuery($dql);
		$query->setParameter('usuarioId', $usuarioId);
		$query->getResult();
	}

	/**
	 * Funcion que guarda los trabajos de un usuario
	 * 
	 * Claves del array que ingresa por cada trabajo
	 *	$ARRpositions[] = Array(
	 * 				'id' => "Id de linkedIn"
	 * 				'title' => "Cargo"
	 * 				'summary' => "Descripción"
	 * 				'isCurrent' => "Actualmente labora aqui"
	 * 				'startDateY' => "Año de inicio"
	 * 				'startDateM' => "Mes de inicio"
	 * 				'endDateY' =>"Año de finalización"
	 * 				'endDateM' => "Mes de finalización"
	 * 				//DATOS DE LA COMPAÑIA
	 * 				'companyId' => "Id de la compañia"
	 * 				'companyName' => "Nombre de la compañia"
	 * 				'companyType' => "Tipo de compañia"
	 *				'companySize' => "Tamaño de la compañia"
	 * 				'companyIndustry' => "Industria de la compañia"
	 * 	);
	 * - Acceso desde PerfilController
	 *
     * @version 1
	 * @param Int $usuarioId Id del perfil a relacionar los trabajos
	 * @param Array $estudios Arreglo con trabajos a vincular al usuario.
	 */
	public function setTrabajosPerfil($trabajos, $usuarioId)
	{
		$em = $this->doctrine->getManager();
		$Objusuario = $em->getRepository('vocationetBundle:Usuarios')->findOneById($usuarioId);
		
		foreach($trabajos as $trabajo)
		{
			$crearEmpresa = false;
			if ($trabajo['companyId']) {
				$ObjCompany = $em->getRepository('vocationetBundle:Empresas')->findOneByIdLinkedin($trabajo['companyId']);
				if (!$ObjCompany) {
					$crearEmpresa = true;
				}
			}
			else {
				$ObjCompany = $em->getRepository('vocationetBundle:Empresas')->findOneByNombre($trabajo['companyName']);
				if (!$ObjCompany) {
					$crearEmpresa = true;
				}
			}

			if ($crearEmpresa) {
				$ObjCompany = new Empresas();
			}

			$ObjCompany->setNombre($trabajo['companyName']);
			$ObjCompany->setTipo($trabajo['companyType']);
			$ObjCompany->setSize($trabajo['companySize']);
			$ObjCompany->setIndustria($trabajo['companyIndustry']);
			$ObjCompany->setIdLinkedin($trabajo['companyId']);
			$em->persist($ObjCompany);

			$newJob = new Trabajos();
			$newJob->setUsuario($Objusuario);
			$newJob->setEmpresa($ObjCompany);
			$newJob->setCargo($trabajo['title']);
			$newJob->setResumen($trabajo['summary']);
			if ($trabajo['startDateY'] && $trabajo['startDateM']) {
				$newJob->setFechaInicio(new \DateTime($trabajo['startDateY'].'-'.$trabajo['startDateM'].'-01'));
			}
			if ($trabajo['endDateY'] && $trabajo['endDateM']) {
				$newJob->setFechaFinal(new \DateTime($trabajo['endDateY'].'-'.$trabajo['endDateM'].'-01'));
			}
			$auxEA = ($trabajo['isCurrent']) ? 1 : 0;
			$newJob->setEsActual($auxEA);
			if ($trabajo['id']) {
				$newJob->setIdLinkedin($trabajo['id']);
			}
			$em->persist($newJob);
		}
		$em->flush();
	}

	/**
	 * Funcion que retorna un array los colegios a seleccionar por el estudiante
	 * - Acceso desde PerfilController
	 * 
     * @version 1
	 * @return Array Con colegios
	 */
	public function getColegios()
	{
		$arrayColegios = Array();
		$em = $this->doctrine->getManager();
		$colegios = $em->getRepository('vocationetBundle:Colegios')->findAll();
		foreach($colegios as $colegio){
			$arrayColegios[$colegio->getId()] = $colegio->getNombre();
		}
		return $arrayColegios;
	}
    
    /**
	 * Funcion que trae los titulo registrados
	 * - Acceso desde ContactosController
	 *
     * @version 1
	 * @return Array Arreglo con titulo y profesiones
	 */
	public function getTitulos()
	{
		$dql= "SELECT u.usuarioProfesion, e.titulo FROM vocationetBundle:Usuarios u
			LEFT JOIN vocationetBundle:Estudios e WITH u.id = e.usuario
            GROUP BY u.usuarioProfesion, e.titulo";
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
		$titulos = $query->getResult();
        
        $ArrTitulos = Array();
        foreach ($titulos as $t) {
            $ArrTitulos[] = $t['usuarioProfesion'];
            $ArrTitulos[] = $t['titulo'];
        }
        return $ArrTitulos;
	}
    
    /**
	 * Funcion que trae las universidades
	 * - Acceso desde ContactosController
	 *
     * @version 1
	 * @return Array Arreglo con universidades
	 */
	public function getUniversidades()
	{
		$dql= "SELECT e.nombreInstitucion
                FROM vocationetBundle:Estudios e
                GROUP BY e.nombreInstitucion";
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
		$universidades = $query->getResult();
        
        $ArrUniversidades = Array();
        foreach ($universidades as $univ) {
            $ArrUniversidades[] = $univ['nombreInstitucion'];
        }
        return $ArrUniversidades;
	}

	/**
	 * Funcion que retorna un array con los grados (cursos) a seleccionar por el estudiante
	 * - Acceso desde PerfilController
	 * 
     * @version 1
	 * @return Array Con grados(cursos)
	 */
	public function getGrados()
	{
		$tr = $this->translate;
		return $grados = Array(
					'6' => $tr->trans("sexto", array(), 'label'),
					'7' => $tr->trans("septimo", array(), 'label'),
					'8' => $tr->trans("octavo", array(), 'label'),
					'9' => $tr->trans("noveno", array(), 'label'),
					'10' => $tr->trans("decimo", array(), 'label'),
					'11' => $tr->trans("once", array(), 'label'),
					'12' => $tr->trans("doce", array(), 'label'),
				);
	}

	/**
	 * Funcion que retorna un array con los roles por los que puede buscar un estudiante
	 * - Acceso desde ContactosController
	 * 
     * @version 1
	 * @return Array con roles
	 */
	public function getRolesBusqueda() {
		$tr = $this->translate;
		return $rolesBusqueda = Array(
					'1' => $tr->trans("estudiante", array(), 'db'),
					'2' => $tr->trans("mentor_e", array(), 'db'),
					'3' => $tr->trans("mentor_ov", array(), 'db'),
				);
	}

	/**
	 * Funcion que retorna un array con los roles para seleccionar un mentor
	 * - Acceso desde PerfilController
	 * 
     * @version 1
	 * @return Array con roles que puede escoger el mentor
	 */
	public function getRolesMentor() {
		$tr = $this->translate;
		return $rolesBusqueda = Array(
					'2' => $tr->trans("mentor_e", array(), 'db'),
					'3' => $tr->trans("mentor_ov", array(), 'db'),
				);
	}

	/**
	 * Funcion que retorna la ruta en donde se van a guardar y de adonde se van a acceder las hojas de vida
	 * - Acceso desde PerfilController
	 * 
     * @version 1
     * @return String Ruta de acceso a hojas de vida
	 */
	public function getRutaHojaVida()
	{
		return 'uploads/vocationet/usuarios/';
	}

	/**
	 * Funcion que retorna la ruta en donde se van a guardar y de adonde se van a acceder las tarjetas profesionales
	 * - Acceso desde PerfilController
	 * 
     * @version 1
     * @return String Ruta de acceso a tarjetas profesionales
	 */
	public function getRutaTarjetaProfesional()
	{
		return 'uploads/vocationet/usuarios/';
	}
	
	/**
	 * Funcion que cantidad de amistades enviadas sin aprobar
	 * - Acceso desde AlertBageController
	 *
     * @version 1
	 * @param Int $usuarioId Id del usuario
	 * @return Int Cantidad de relaciones sin aprobar
	 */
	public function getCantidadAmistades($usuarioId)
	{
		$em = $this->doctrine->getManager();
		/**
		* @var String Consulta SQL qu trae la cantidad de amistades sin responder
		* SELECT COUNT(r.id) AS cantidad FROM relaciones r
		* WHERE r.usuario2_id = 7 AND r.tipo= 1 AND r.estado = 0;
		*/
		$dql= "SELECT COUNT(r.id) AS cantidad
                FROM vocationetBundle:Relaciones r
				WHERE r.usuario2 =:usuarioId AND r.tipo= 1 AND r.estado = 0";
        $query = $em->createQuery($dql);
		$query->setParameter('usuarioId', $usuarioId);
		$cantidad = $query->getResult();
        return $cantidad[0]['cantidad'];
	}

	//MENTORIAS

	/**
	 * Funcion que retorna los ID de las mentorias que aún no han sido calificadas entre los usuarios que ingresan como parametro
	 * - Acceso desde PerfilController
	 *
     * @version 1
	 * @param Int $mentor Id del usuario Mentor
	 * @param Int $estudiante Id del usuario Estudiante
	 * @return Array Arreglo con usuarios mentorias no calificadas
	 */
	public function mentoriasSinCalificar($mentor, $estudiante)
	{
		/**
		 * @var String Consulta SQL de mentorias que estan ejecutadas pero no estan calificadas
		 * SELECT * FROM mentorias
		 * WHERE usuario_mentor_id = 8 AND usuario_estudiante_id = 12 AND mentoria_estado = 1 AND calificacion IS NULL;
		 */
		$dql = "SELECT m.id, m.mentoriaEstado, m.calificacion, m.resena, m.mentoriaInicio, m.mentoriaFin
                FROM vocationetBundle:Mentorias m
                WHERE
					m.usuarioMentor =:usuarioMenId AND m.usuarioEstudiante =:usuarioEstId AND m.mentoriaEstado = 1 AND m.calificacion IS NULL";
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioMenId', $mentor);
        $query->setParameter('usuarioEstId', $estudiante);
        $result = $query->getResult();
        return $result;
	}

	/**
	 * Funcion que retorna las calificaciones que ha recibido un mentor, agrupadas por No. de estrellas (5=> x-usuarios, 4=> y-usuarios...)
	 * - Acceso desde PerfilController
	 *
     * @version 1
	 * @param Int $mentor Id del usuario Mentor
	 * @return Array Arreglo con calificaciones, y totales
	 */
	public function mentoriasCalificadas($mentor)
	{
		/**
		 * @var String $dql Consulta SQL que trae agrupados calificaciones por cantidad de usuarios (5=> x-usuarios, 4=> y-usuarios...)
		 * SELECT id, COUNT(usuario_estudiante_id), calificacion FROM mentorias
		 * WHERE usuario_mentor_id = 8 AND mentoria_estado = 1 AND calificacion IS NOT NULL
		 * GROUP BY calificacion;
		 */
		$dql = "SELECT COUNT(m.id) AS cantidadUsuarios, m.calificacion
				FROM vocationetBundle:Mentorias m
                WHERE m.usuarioMentor =:usuarioMenId AND m.mentoriaEstado = 1 AND m.calificacion IS NOT NULL
                GROUP BY m.calificacion";
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioMenId', $mentor);
        $result = $query->getResult();

		$Aux = Array('totalUsuarios' => 0,
					'totalCalificacion' => 0,
					'5'=>0, '4'=>0,	'3'=>0,	'2'=>0,	'1'=>0,	'0'=>0);
		
		$auxTotalUsuarios = 0;
        foreach ($result as $cal) {
			$calificacion = $cal['calificacion'];
			$users = $cal['cantidadUsuarios'];
			$Aux[$calificacion] = $users;
			$Aux['totalUsuarios'] += $users;
			$Aux['totalCalificacion'] += ($calificacion*$users);
		}
        return $Aux;
	}

	/**
	 * Funcion que retorna las 25 últimas reseñas que le han hecho a un mentor, con la informacion del usuario
	 * - Acceso desde PerfilController
	 *
     * @version 1
	 * @param Int $mentorId Id del usuario Mentor
	 * @return Array Arreglo con últimas 25 reseñas
	 */
	public function getResenasMentor($mentorId)
	{
		/**
		 * @var String $dql Consulta SQL que trae las reseñas o comentarios de los usuarios que han calificado al mentor
		 * SELECT m.*, u.* FROM mentorias m
		 * LEFT JOIN usuarios u ON u.id = m.usuario_estudiante_id
		 * WHERE usuario_mentor_id = 8 AND mentoria_estado = 1 AND calificacion IS NOT NULL;
		 */
		$dql = "SELECT m.id, m.calificacion, m.resena, m.mentoriaInicio,
					u.id AS usuarioId, u.usuarioApellido, u.usuarioNombre, u.usuarioEmail, u.usuarioImagen
				FROM vocationetBundle:Mentorias m
				LEFT JOIN vocationetBundle:Usuarios u WITH u.id = m.usuarioEstudiante
                WHERE m.usuarioMentor =:usuarioMenId AND m.mentoriaEstado = 1 AND m.calificacion IS NOT NULL
                ORDER BY m.id DESC";
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
        $query->setMaxResults(25);
        $query->setParameter('usuarioMenId', $mentorId);
        $result = $query->getResult();
        return $result;
	}

	/**
	 * Funcion que retorna true ID del usuario ya ha seleccionado mentor de orientación vocacional, false si no lo ha hecho
	 * - Acceso desde ContactosController
	 * - Acceso desde MercadoLaboralController
	 *
     * @version 1
	 * @param Int $estudianteId Id del usuario Estudiante
	 * @return Boolean True=>Ya seleccionó mentorOV  False => No ha seleccionado mentorOV
	 */
	public function confirmarMentorOrientacionVocacional($estudianteId)
	{
		$return = false;
		$em = $this->doctrine->getManager();
		$dql= "SELECT u.id
                FROM vocationetBundle:Relaciones r
                JOIN vocationetBundle:Usuarios u WITH u.id = r.usuario
				WHERE r.usuario2 =:estudianteId AND r.tipo = 2 AND r.estado = 1";
        $query = $em->createQuery($dql);
		$query->setParameter('estudianteId', $estudianteId);
		$mentor = $query->getResult();
        if ($mentor) {
			//$return = true;
			$return = $mentor[0];
		}
		return $return;
	}

	/**
	 * Funcion que retorna true OBJETOS de alternativas de estudio seleccionadas por el estudiante que ingresa por parametro
	 * - Acceso desde MercadoLaboralController
	 *
     * @version 1
	 * @param Int $estudianteId Id del usuario Estudiante
	 * @return Object AlternativasEstudio
	 */
	public function getAlternativasEstudio($estudianteId)
	{
		$em = $this->doctrine->getManager();
		$dql= "SELECT ae
                FROM vocationetBundle:AlternativasEstudios ae
				WHERE ae.usuario =:estudianteId";
        $query = $em->createQuery($dql);
		$query->setParameter('estudianteId', $estudianteId);
		return $query->getResult();
	}
	
	/**
	 * Funcion que retorna publicidad o informacion registrad activa (max 5) y filtro por rol
	 * - Acceso desde PerfilController
	 *
     * @version 2 - Incluir filtro de publicidad por rol
     * @param Int $rol_id Id del rol
	 * @return Object Informacion
	 */
	public function getPublicidad($rol_id)
	{
		$destino = ($rol_id == 2 or $rol_id == 3) ? 'Mentores' : 'Estudiantes';
		$em = $this->doctrine->getManager();
		$dql= "SELECT i FROM vocationetBundle:Informacion i
				WHERE i.informacionEstado = 1 AND (i.informacionDestino =:destino OR i.informacionDestino ='Todos')
				ORDER BY i.id DESC";
		$query = $em->createQuery($dql);
		$query->setMaxResults(5);
		$query->setParameter('destino', $destino);
		return $query->getResult();
	}
	
	/**
	* Estado actual de la plataforma
	*
	* Funcion que retorna un arreglo informando que pruebas ha finalizado, y cantidad de mentorias realizadas
	* - Acceso desde PerfilController
	* - Acceso desde HomeController
	* 
    * @version 1
	* @param Int $usuarioId Id del usuario
	* @return Array Arreglo de estado actual de la plataforma
	*/
	public function getEstadoActualPlataforma($usuarioId)
	{
		$em = $this->doctrine->getManager();
        $dql = "SELECT p FROM vocationetBundle:Participaciones p
                WHERE (p.usuarioParticipa =:usuarioId OR p.usuarioEvaluado =:usuarioId) AND p.estado = 2";
        $query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $participaciones = $query->getResult();

		/**
		 * @var String Consulta SQL que trae mentorias del usuario tanto de experto como de orientado vocacional finalizadas
		 * SELECT * FROM mentorias m
		 * JOIN usuarios u ON u.id = m.usuario_mentor_id
		 * WHERE m.usuario_estudiante_id = 27 AND m.mentoria_estado = 1;
		*/
		$dql = "SELECT m.id, r.id as rolId
				FROM vocationetBundle:Mentorias m
				JOIN vocationetBundle:Usuarios u WITH m.usuarioMentor = u.id
				JOIN u.rol r
				WHERE m.usuarioEstudiante =:usuarioId AND m.mentoriaEstado = 1"; // AND u.rol = 3
		$query = $em->createQuery($dql);
        $query->setParameter('usuarioId', $usuarioId);
        $mentorias = $query->getResult();

		$cm = 0; // AuxContador de mentorias de ORIENTADOR VOCACIONAL
		$cme = 0; // AuxContador de mentorias de EXPERTO
		foreach ($mentorias as $ment)
		{
			if ($ment['rolId'] == 3)
				$cm += 1;
			elseif ($ment['rolId'] == 2)
				$cme += 1;
		}
		
		/**
		* P1. DIAGNOSTICO
		* P8. TEST VOCACIONAL (QUEMADO - REVISAR)
		* P9. EVALUACION 360
		* P10. DISEÑO DE VIDA
		* P11. MERCADO LABORAL
		* P12. RED DE MENTORES
		* P13. PONDERACION
		* P14. UNIVERSIDADES
		* P42. CERTIFICACIÓN
		*/
		
		$recorrido = Array(
			'P1' => false, 'M1' => ($cm >= 1) ? true : false,
			'P8' => false, 'M2' => ($cm >= 2) ? true : false,
			'P9' => false, 'M3' => ($cm >= 3) ? true : false,
			'P10' => false, 'M4' => ($cm >= 4) ? true : false,
			'P11' => false, 'M5' => ($cm >= 5) ? true : false,
			'P12' => false,
			'P13' => false, 'M6' => ($cm >= 6) ? true : false,
			'P14' => false,
			'P42' => false,
			'totalMentorias' => $cm,
		);
		
		foreach($participaciones as $part)
		{
			$formId = $part->getFormulario();

			// Validación de que almenos tenga una mentoria terminada con mentor Experto
			if($formId == 12) {
				if ($cme > 0) {
					$recorrido['P'.$formId] = true;
				}
			}
			else {
				$recorrido['P'.$formId] = true;
			}
		}
		return $recorrido;
	}

	/**
	 * Mapa correspondiente a la posición actual del usuario teniendo en cuenta el recorrido del usuario
	 * - Acceso desde HomeController
	 *
	 * @version 1
	 * @param Array $recorrido Arreglo retornado por getEstadoActualPlataforma()
	 * @return String Nombre de la imagen en donde esta marcado el recorrido hasta la posicion actual del usuario
	 */
	public function getImagenFaseRecorrido($recorrido)
	{
		$aux_cont = 1;
		if ($recorrido['P1']) {	$aux_cont += 1;
			if ($recorrido['M1']) {	$aux_cont += 1;
				if ($recorrido['P8']) { $aux_cont += 1;
					if ($recorrido['M2']) { $aux_cont += 1;
						if ($recorrido['P9']) { $aux_cont += 1;
							if ($recorrido['M3']) { $aux_cont += 1;
								if ($recorrido['P10']) { $aux_cont += 1;
									if ($recorrido['M4']) { $aux_cont += 1;
										if ($recorrido['P11']) { $aux_cont += 1;
											if ($recorrido['M5']) { $aux_cont += 1;
												if ($recorrido['P12']) { $aux_cont += 1;
													if ($recorrido['P13']) { $aux_cont += 1;
														if ($recorrido['M6']) { $aux_cont += 1;
															if ($recorrido['P14']) { $aux_cont += 1;
																if ($recorrido['P42']) { $aux_cont += 1;
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return 'mapa'.$aux_cont.'.png'; 
	}
	
	/**
	 * Mapa correspondiente a la posición actual del usuario teniendo en cuenta el recorrido del usuario, y q es un programa informativo
	 * - Acceso desde HomeController
	 *
	 * @version 1
	 * @param Array $recorrido Arreglo retornado por getEstadoActualPlataforma()
	 * @return String Nombre de la imagen en donde esta marcado el recorrido hasta la posicion actual del usuario
	 */
	public function getImagenProgramaInformativo($recorrido)
	{
		$img = 'mapa1.png';
		if ($recorrido['P1']) {
			$img = 'mapa2.png';
		}
		if ($recorrido['P11']) {
			$img = 'prog-inf1.png';
		}
		if ($recorrido['P12']) {
			$img = 'prog-inf2.png';
		}
		if ($recorrido['P13']) {
			$img = 'prog-inf3.png';
		}
		
		if ($recorrido['P14']) {
			$img = 'prog-inf4.png';
		}
		if ($recorrido['P42']) {
			$img = 'prog-inf5.png';
		}
		return $img;
	}
	

	/**
	 * Validación de linealidad de Vocationet
	 * 
	 * Función que valida si el punto de la plataforma al que quiere ingresar, tiene acceso
	 * teniendo en cuenta orden lineal
	 * 
	 * @version 1
	 * @param Int $usuarioId Id del usuario
	 * @param String $posActual posición de la plataforma a la que quiere acceder
	 * @return Array Arreglo con estado, mensaje y redireccionamiento si es necesario
	 */
	public function validarPosicionActual($usuarioId, $posActual)
	{
		$return = array('status' => true, 'message' => false, 'redirect' => false);
		$recorrido = $this->getEstadoActualPlataforma($usuarioId);

		switch ($posActual) {
			case 'orientador_vocacional';
				$return = $this->validarDiagnostico($return, $recorrido);
				break;
			case 'test_vocacional':
				$return = $this->validarTestVocacional($return, $recorrido);
				break;
			case 'evaluacion360':
				$return = $this->validarEvaluacion360($return, $recorrido);
				break;
			case 'disenovida':
				$return = $this->validarDisenoVida($return, $recorrido);
				break;
			case 'mercadolaboral':
				$return = $this->validarMercadoLaboral($return, $recorrido);
				break;
			case 'redmentores':
				$return = $this->validarRedMentores($return, $recorrido);
				break;
			case 'ponderacion':
				$return = $this->validarPonderacion($return, $recorrido);
				break;
			case 'universidades':
				$return = $this->validarUniversidades($return, $recorrido);
				break;
			case 'certificacion':
				$return = $this->validarCertificado($return, $recorrido);
				break;
		}		
		return $return;
	}

	/**
	 * Funcion que valida si el usuario ya realizo diagnóstico
	 * @version 1
	 * @param Array $return Arreglo con estado, redireccionamiento, y mensaje (Objetivo, de retornar al usuario a la prueba en la que se encuentra)
	 * @param Array $recorrido Arreglo con el recorrido actual del usuario, y mentorias realizadas
	 * @return Array Arreglo con cambios realizados en caso de haber sido necesario del param $return
	 */
	private function validarDiagnostico($return, $recorrido)
	{
		if (!$recorrido['P1']) {
			$return = array('status' => false, 'message' => 'no.ha.realizado.prueba.diagnostico', 'redirect' => 'diagnostico');
		}
		return $return;
	}

	 /**
	 * Función que valida si el usuario tiene terminado el proceso lineal, para realizar test vocacional
	 * - ya realizo diagnóstico, y la primera mentoria
	 * @version 1
	 * @param Array $return Arreglo con estado, redireccionamiento, y mensaje (Objetivo, de retornar al usuario a la prueba en la que se encuentra)
	 * @param Array $recorrido Arreglo con el recorrido actual del usuario, y mentorias realizadas
	 * @return Array Arreglo con cambios realizados en caso de haber sido necesario del param $return
	 */
	private function validarTestVocacional($return, $recorrido)
	{
		$return = $this->validarDiagnostico($return, $recorrido);
		if ($return['status']) {
			if (!$recorrido['M1']) {
				$return = array('status' => false, 'message' => 'por.favor.agende.mentoria', 'redirect' => 'lista_mentores_ov');
			}
		}
		return $return;
	}

	/**
	 * Función que valida si el usuario tiene terminado el proceso lineal, para realizar evaluación 360
	 * @version 1
	 * @param Array $return Arreglo con estado, redireccionamiento, y mensaje (Objetivo, de retornar al usuario a la prueba en la que se encuentra)
	 * @param Array $recorrido Arreglo con el recorrido actual del usuario, y mentorias realizadas
	 * @return Array Arreglo con cambios realizados en caso de haber sido necesario del param $return
	 */
	private function validarEvaluacion360($return, $recorrido)
	{
		$return = $this->validarTestVocacional($return, $recorrido);
		if ($return['status']) {
			if (!$recorrido['P8']) {
				$return = array('status' => false, 'message' => 'no.ha.realizado.test.vocacional', 'redirect' => 'test_vocacional');
			} elseif (!$recorrido['M2']) {
				$return = array('status' => false, 'message' => 'por.favor.agende.mentoria', 'redirect' => 'lista_mentores_ov');
			}
		}
		return $return;
	}

	/**
	 * Función que valida si el usuario tiene terminado el proceso lineal, para realizar diseño de vida
	 * @version 1
	 * @param Array $return Arreglo con estado, redireccionamiento, y mensaje (Objetivo, de retornar al usuario a la prueba en la que se encuentra)
	 * @param Array $recorrido Arreglo con el recorrido actual del usuario, y mentorias realizadas
	 * @return Array Arreglo con cambios realizados en caso de haber sido necesario del param $return
	 */
	private function validarDisenoVida($return, $recorrido)
	{
		$return = $this->validarEvaluacion360($return, $recorrido);
		if ($return['status']) {
			if (!$recorrido['P9']) {
				$return = array('status' => false, 'message' => 'no.ha.realizado.evaluacion360', 'redirect' => 'evaluacion360');
			} elseif (!$recorrido['M3']) {
				$return = array('status' => false, 'message' => 'por.favor.agende.mentoria', 'redirect' => 'lista_mentores_ov');
			}
		}
		return $return;
	}

	/**
	 * Función que valida si el usuario tiene terminado el proceso lineal, para realizar mercado laboral
	 * @version 1
	 * @param Array $return Arreglo con estado, redireccionamiento, y mensaje (Objetivo, de retornar al usuario a la prueba en la que se encuentra)
	 * @param Array $recorrido Arreglo con el recorrido actual del usuario, y mentorias realizadas
	 * @return Array Arreglo con cambios realizados en caso de haber sido necesario del param $return
	 */
	private function validarMercadoLaboral($return, $recorrido)
	{
		$return = $this->validarDisenoVida($return, $recorrido);
		if ($return['status']) {
			if (!$recorrido['P10']) {
				$return = array('status' => false, 'message' => 'no.ha.realizado.diseno.vida', 'redirect' => 'diseno_vida');
			} elseif (!$recorrido['M4']) {
				$return = array('status' => false, 'message' => 'por.favor.agende.mentoria', 'redirect' => 'lista_mentores_ov');
			}
		}
		return $return;
	}

	/**
	 * Función que valida si el usuario tiene terminado el proceso lineal, para realizar red de mentores
	 * @version 1
	 * @param Array $return Arreglo con estado, redireccionamiento, y mensaje (Objetivo, de retornar al usuario a la prueba en la que se encuentra)
	 * @param Array $recorrido Arreglo con el recorrido actual del usuario, y mentorias realizadas
	 * @return Array Arreglo con cambios realizados en caso de haber sido necesario del param $return
	 */
	private function validarRedMentores($return, $recorrido)
	{
		$return = $this->validarMercadoLaboral($return, $recorrido);
		if ($return['status']) {
			if (!$recorrido['P11']) {
				$return = array('status' => false, 'message' => 'no.ha.realizado.mercado.laboral', 'redirect' => 'mercado_laboral');
			} elseif (!$recorrido['M5']) {
				$return = array('status' => false, 'message' => 'por.favor.agende.mentoria', 'redirect' => 'lista_mentores_ov');
			}
		}
		return $return;
	}

	/**
	 * Función que valida si el usuario tiene terminado el proceso lineal, para realizar ponderación
	 * @version 1
	 * @param Array $return Arreglo con estado, redireccionamiento, y mensaje (Objetivo, de retornar al usuario a la prueba en la que se encuentra)
	 * @param Array $recorrido Arreglo con el recorrido actual del usuario, y mentorias realizadas
	 * @return Array Arreglo con cambios realizados en caso de haber sido necesario del param $return
	 */
	private function validarPonderacion($return, $recorrido)
	{
		$return = $this->validarRedMentores($return, $recorrido);
		if ($return['status']) {
			if (!$recorrido['P12']) {
				$return = array('status' => false, 'message' => 'no.ha.realizado.red.mentores', 'redirect' => 'red_mentores');
			}
		}
		return $return;
	}

	/**
	 * Función que valida si el usuario tiene terminado el proceso lineal, para realizar Prueba de universidades
	 * @version 1
	 * @param Array $return Arreglo con estado, redireccionamiento, y mensaje (Objetivo, de retornar al usuario a la prueba en la que se encuentra)
	 * @param Array $recorrido Arreglo con el recorrido actual del usuario, y mentorias realizadas
	 * @return Array Arreglo con cambios realizados en caso de haber sido necesario del param $return
	 */
	private function validarUniversidades($return, $recorrido)
	{
		$return = $this->validarPonderacion($return, $recorrido);
		if ($return['status']) {
			if (!$recorrido['P13']) {
				$return = array('status' => false, 'message' => 'no.ha.realizado.ponderacion', 'redirect' => 'ponderacion');
			} elseif (!$recorrido['M6']) {
				$return = array('status' => false, 'message' => 'por.favor.agende.mentoria', 'redirect' => 'lista_mentores_ov');
			}
		}
		return $return;
	}

	/**
	 * Función que valida si el usuario tiene terminado el proceso lineal, para ingresar a certificación
	 * @version 1
	 * @param Array $return Arreglo con estado, redireccionamiento, y mensaje (Objetivo, de retornar al usuario a la prueba en la que se encuentra)
	 * @param Array $recorrido Arreglo con el recorrido actual del usuario, y mentorias realizadas
	 * @return Array Arreglo con cambios realizados en caso de haber sido necesario del param $return
	 */
	private function validarCertificado($return, $recorrido)
	{
		$return = $this->validarUniversidades($return, $recorrido);
		if ($return['status']) {
			if (!$recorrido['P14']) {
				$return = array('status' => false, 'message' => 'no.ha.realizado.universidades', 'redirect' => 'universidad');
			}
		}
		return $return;
	}

	/**
	 * Función que actualiza puntuación del usuario que ingresa como parametro
	 *
	 * @version 1
	 * @param String $tipo Razón por la que se le van a dar puntos
	 * @param Int $usuarioId Id del usuario
	 * @param Array $aux Auxiliar en arreglo para valores a tener en cuenta para la cantidad de puntos a sumar
	 */
	function actualizarpuntos($tipo, $usuarioId, $aux = array())
	{
		$auxp = 0;//auxpuntos
		if ($tipo == 'diagnostico') {
			//$aux (rango(1,2,3), puntaje)
			$auxp = ($aux['rango'] == 1) ? 10 : ($aux['rango'] == 2) ? 20 : ($aux['rango'] == 3) ? 30 : 0;
		}
		if ($tipo == 'evaluacion360') {
			$auxp = 50;
		}
		if ($tipo == 'disenovida') {
			$auxp = 100;
		}
		if ($tipo == 'mercadolaboral') {
			$auxp = 50 * $aux['cantidad'];// Numero de alternativas seleccionadas
		}
		if ($tipo == 'redmentores') {
			$auxp = 50; //c/u
		}
		if ($tipo == 'ponderacion') {
			$auxp = 10 * $aux['cantidad'];// Numero de alternativas seleccionadas
		}

		if ($auxp > 0) {
			$em = $this->doctrine->getManager();
			$usuario = $em->getRepository("vocationetBundle:Usuarios")->findOneById($usuarioId);
			if ($usuario) {
				$ptsAct = $usuario->getUsuarioPuntos();
				$ptsAux = $ptsAct + $auxp;
				
				$usuario->setUsuarioPuntos($ptsAux);
				$em->persist($usuario);
				$em->flush();
			}
		}
    }
}
?>
