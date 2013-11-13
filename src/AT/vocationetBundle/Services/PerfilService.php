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
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @param Int $perfilId Id del perfil al acceder
     * @return Array Arreglo con informacion del perfil
	 */
    public function getPerfil($perfilId)
    {
		$em = $this->doctrine->getManager();
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
				u.usuarioHojaVida, u.usuarioTarjetaProfesional, u.usuarioValorMentoria, u.usuarioPerfilProfesional,
				u.usuarioCursoActual, u.usuarioFacebookid, u.usuarioFechaPlaneacion, u.usuarioGenero,
				r.nombre as nombreRol,
				col.nombre as nombreCol, col.id as colegioId
			FROM vocationetBundle:Usuarios u
			JOIN u.rol r
			LEFT JOIN u.colegio col
			WHERE u.id =:perfilId";
		$query = $em->createQuery($dql);
		$query->setParameter('perfilId', $perfilId);
		$perfil =$query->getResult();
		return $perfil[0];
	}

	/**
	 * Funcion que trae los estudios registrados del perfil
	 * - Estudios de rol mentor vocacional o experto
	 * - Acceso desde PerfilController
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @param Int $usuarioId Id del perfil al acceder
	 * @return Array Arreglo con estudios registrados del perfil
	 */
	public function getEstudiosPerfil($usuarioId)
	{
		$em = $this->doctrine->getManager();
		/**
		 * @var Consulta SQL que elimina los estudios del usuario que ingresa por parametro
		 * SELECT * FROM estudios e
		 * WHERE e.usuario_id = 2;
		 */
		 $dql= "SELECT est FROM vocationetBundle:Estudios est
			WHERE est.usuario =:usuarioId";
			$query = $em->createQuery($dql);
		$query->setParameter('usuarioId', $usuarioId);
		$estudios =$query->getResult();
		return $estudios;
	}
	
	/**
	 * Funcion que elimina los estudios registrados del usuario que ingresa por parametro
	 * - Acceso desde PerfilController
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @param Int $usuarioId Id del perfil a eliminar los estudios
	 */
	public function deleteEstudiosPerfil($usuarioId)
	{
		$em = $this->doctrine->getManager();
		/**
		 * @var Consulta SQL que trae los estudios del perfil que ingresa por Id
		 * DELETE FROM estudios e WHERE e.usuario_id = 2;
		*/
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
	 * @author Camilo Quijano <camilo@altactic.com>
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
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @param Int $perfilId Id del perfil al acceder
	 * @return Array Arreglo con trabajos registrados del perfil
	 */
	public function getTrabajosPerfil($usuarioId)
	{
		$em = $this->doctrine->getManager();
		/**
		 * @var Consulta SQL que trae los trabajos del perfil que ingresa por Id
		 * SELECT * FROM trabajos tra
		 * INNER JOIN empresas emp ON tra.empresa_id = emp.id
		 * WHERE tra.usuario_id = 2;
		 */
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
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @param Int $usuarioId Id del usuario a eliminar los trabajos
	 */
	public function deleteTrabajosPerfil($usuarioId)
	{
		$em = $this->doctrine->getManager();
		/**
		 * @var Consulta SQL que elimina los trabajos del usuario que ingresa por parametro
		 * DELETE FROM trabajos tra WHERE tra.usuario_id = 2;
		 */
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
	 * @author Camilo Quijano <camilo@altactic.com>
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

			if ($crearEmpresa) {
				//$em->flush();
			}

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
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @return Array Con colegios
	 */
	public function getColegios()
	{
		$arrayColegios = Array();
		$em = $this->doctrine->getManager();
		$colegios = $em->getRepository('vocationetBundle:colegios')->findAll();
		foreach($colegios as $colegio){
			$arrayColegios[$colegio->getId()] = $colegio->getNombre();
		}
		return $arrayColegios;
	}
    
    /**
	 * Funcion que trae los titulo registrados
	 * - Acceso desde ContactosController
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @return Array Arreglo con titulo y profesiones
	 */
	public function getTitulos()
	{
		$em = $this->doctrine->getManager();
		$dql= "SELECT u.usuarioProfesion, e.titulo FROM vocationetBundle:Usuarios u
			LEFT JOIN vocationetBundle:Estudios e WITH u.id = e.usuario
            GROUP BY u.usuarioProfesion, e.titulo";
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
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @return Array Arreglo con universidades
	 */
	public function getUniversidades()
	{
		$em = $this->doctrine->getManager();
		$dql= "SELECT e.nombreInstitucion
                FROM vocationetBundle:Estudios e
                GROUP BY e.nombreInstitucion";
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
	 * @author Camilo Quijano <camilo@altactic.com>
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
	 * @author Camilo Quijano <camilo@altactic.com>
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
	 * Funcion que retorna la ruta en donde se van a guardar y de adonde se van a acceder las hojas de vida
	 * - Acceso desde PerfilController
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @return String Ruta de acceso a hojas de vida
	 */
	public function getRutaHojaVida()
	{
		return 'img/vocationet/usuarios/';
	}

	/**
	 * Funcion que retorna la ruta en donde se van a guardar y de adonde se van a acceder las tarjetas profesionales
	 * - Acceso desde PerfilController
	 * 
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
     * @return String Ruta de acceso a tarjetas profesionales
	 */
	public function getRutaTarjetaProfesional()
	{
		return 'img/vocationet/usuarios/';
	}
	
	/**
	 * Funcion que cantidad de amistades enviadas sin aprobar
	 * - Acceso desde AlertBaggeController
	 *
	 * @author Camilo Quijano <camilo@altactic.com>
     * @version 1
	 * @param Int Id del usuario
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
}
?>
